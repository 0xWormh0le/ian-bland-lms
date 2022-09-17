<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Company;
use App\Role;
use App\Team;
use App\User;
use App\Azure;
use App\Mutex;
use App\CourseUser;
use App\CourseResult;
use App\CourseMember;
use App\Http\Repositories\ADSync;
use App\Http\Repositories\LearnerReport;

use Yajra\Datatables\Datatables;
use Alert, Auth, DB;
use Carbon\Carbon;

class UserController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $companies = Company::getLists();
        $title = @session('menuLabel')['user-management.users'] ?: @session('menuLabel')['portal-management.user-management'];
        return view('users.index', compact('companies', 'title'));
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function anyData(Request $request)
    {
        $data = User::select(
            'users.id',
            'users.email',
            'first_name',
            'last_name',
            'users.department',
            'companies.company_name',
            'teams.team_name',
            'users.created_at',
            'roles.role_name',
            'users.last_login_at',
            'users.last_login_ip',
            'users.company_id',
            'users.active',
            'users.azure_id'
        )
        ->leftJoin('companies', 'companies.id','=','users.company_id')
        ->leftJoin('teams', 'teams.id','=','users.team_id')
        ->leftJoin('roles', 'roles.id','=','users.role_id')
        ->where('users.role_id', '!=', 0);


        if(Auth::user()->isSysAdmin() && $request->company_id && $request->company_id > 0)
        {
            $data->where('users.company_id', $request->company_id);
        }
       else if(!Auth::user()->isSysAdmin() && Auth::user()->company_id > 0)
        {
            $data->where('users.company_id', Auth::user()->company_id);
            // $data->where('users.role_id', '!=', 1);
        }

        $data = $data->get();
        for($d=0;$d<count($data);$d++)
        {
          if($data[$d]->company_id == NULL ||  $data[$d]->company_id == "")
          {
            $data[$d]->company_name = "";
          }
        }

        $userlog = $request->userlog;
        $sysAdmin = Auth::user()->isSysAdmin();

        return Datatables::of($data)
                        ->editColumn('role_name', function ($data) {
                            $html = $data->role_name;
                            if(!$data->company_id && $data->role_name)
                                $html .= '<small class="text-muted"> ('.trans('controllers.system_default').')</small>';
                            return $html;
                        })
                       ->editColumn('active', function ($data) {
                           $status = trans("modules.inactive");
                           if($data->active == 1) $status = trans("modules.active");

                           return $status;
                        })
                        ->editColumn('ad', function ($data) {
                            return empty($data->azure_id) ? '' : 'Yes';
                        })
                        ->addColumn('action', function ($data) use ($userlog, $sysAdmin) {

                            $impersonate = '<a class=\'btn btn-sm btn-info\' title=\'Impersonate\' href=\'' . route('users.impersonate', $data->id) .'\'>' .
                                           '<i class=\'fa fa-user-secret\'></i></a>';

                            if ($userlog) {
                                $action = show_button('show', 'users.show', encrypt($data->id));
                                if ($sysAdmin) {
                                    $action .= $impersonate;
                                }
                                return $action;
                            }

                            $status = $data->active == 1 ? 'checked' : '';

                            $action = 
                                ' <label class="switch switch-label switch-primary pt-3">
                                    <input type="checkbox" onchange=updateStatus("'.$data->id.'",this) name="active" id="active_checkbox" value="'.$data->id.'" class="switch-input"  '.$status.'>
                                    <span class="switch-slider" data-checked="&#x2713;" data-unchecked="&#x2715;"></span>
                                  </label>'
                                ." ".
                                show_button('show', 'users.show', encrypt($data->id))
                                ." ".
                                show_button('edit', 'users.edit', encrypt($data->id))
                                ." ".
                                show_button('delete', 'users.destroy', encrypt($data->id));

                            if ($sysAdmin) {
                                $action .= $impersonate;
                            } 
                            
                            return $action;
                        })
                        ->rawColumns(['role_name', 'action'])
                        ->make(true);
    }

    public function impersonate(Request $request, User $user)
    {
        session()->forget(['menu', 'roles']);
        Auth::user()->impersonate($user);
        return redirect()->route('home');
    }

    public function leaveImpersonate(Request $request)
    {
        session()->forget('menu', 'roles');
        Auth::user()->leaveImpersonation();
        return redirect()->route('home');
    }

    public function courseData(Request $request)
    {
        $user_id = $request->user_id;
        $courses = CourseUser::select(
            "courses.title as title",
            "course_id",
            "enrol_date",
            "active",
            "completed",
            "completion_percentage",
            "completion_date",
            "course_users.id"
        )
            ->leftJoin('courses', 'courses.id','=','course_users.course_id')
            ->where('course_users.user_id', $user_id)
            ->get();

        $courses = $courses->map(function ($course) use ($user_id){
            $course_result = course_completion_rules_result($course->course_id, $user_id);
            $course->completed = $course_result['complete'];
            $course->completion_percentage = round($course_result['percentage']);

            $score = CourseResult::whereHas('module', function ($query) {
                $query->where('type', 'Elearning');
            })->where('courseuser_id', $course->id)->avg('score');

            $total_time = CourseResult::select(DB::raw('SEC_TO_TIME( SUM( TIME_TO_SEC( `total_time` ) ) ) AS timeSum'))
                ->where('courseuser_id', $course->id)
                ->groupBy('courseuser_id')->first();

            $course->score = $course->active ? round($score) : '';
            $course->total_time = optional($total_time)->timeSum;

            return $course;
        });

        return Datatables::of($courses)
                        ->editColumn('active', function ($data) {
                            // $courseComResult = course_completion_rules_result($data->course_id, $request->user_id);
                            if($data->active == 0) $data->active = trans("modules.pending");
                            if($data->active == 1) $data->active = trans("modules.active");
                            if($data->completed == 1) $data->active = trans("modules.completed");
                            return $data->active;
                        })
                        ->editColumn('enrol_date', function ($data) {
                            return $data->enrol_date
                                    ? Carbon::parse($data->enrol_date)->format('d-m-Y h:i:s')
                                    : NULL;
                        })
                        ->editColumn('completion_date', function ($data) {
                            return $data->completion_date
                                    ? Carbon::parse($data->completion_date)->format('d-m-Y h:i:s')
                                    : NULL;
                        })
                        ->editColumn('completed', function ($data) {
                            return $data->completed ? trans("modules.completed") : trans("modules.incomplete");
                        })
                        ->editColumn('title', function ($data) {
                            return ucfirst($data->title);
                        })
                        ->editColumn('completion_date', function ($data) {
                          $completion_date ="";
                          if($data->completion_date != "")
                            $completion_date = \Carbon\Carbon::parse($data->completion_date)->format("d/m/Y");
                            return ucfirst($completion_date);
                        })
                        ->make(true);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = trans("modules.add_new_user");
        $companies = Company::getLists();
        $companyId = Auth::user()->company_id ;
        $role = Auth::user()->role ;
        $userCompany = Company::where("id", $companyId)->first();
        $companyUserCount = User::where("company_id", $companyId)->count();


        $limit = false ;
        if(!Auth::user()->isSysAdmin() && $userCompany->max_users!="" && $companyUserCount >= $userCompany->max_users)
        {
          $limit = true;
        }

        return view('users.form', compact('title', 'companies', 'limit'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'first_name' => 'required',
            'email' => 'required|email',
            'role_id' => 'required',
          ];

        if(Auth::user()->company_id ==  null)
        {
          $rules['company_id'] = 'required';
        }
        $request->validate($rules);

        $record = User::withTrashed()->where('email', $request->email)->first();
        if(!$record)
            $record = new User;
        $record = $this->save($record, $request);
        if($record)
        {
            Alert::success(__('messages.save_success'))->autoclose(3000);
            return redirect()->route('users.index');
        }
        Alert::error(__('messages.save_failed'))->autoclose(3000);
        return redirect()->back()->withInput();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, LearnerReport $learnerReport)
    {
        $id = decrypt($id);
        $data = User::find($id);

        if($data && \Auth::user()->companyAccess($data->company_id))
        {
            $breadcrumbs = [
                route('users.index') => trans('controllers.users'),
                '' => $data->first_name . ' ' . $data->last_name,
            ];
            $title = trans('controllers.details_of_user');

            $res = $learnerReport->handle(['id' => $id, 'filter' => 'active', 'export' => 'none', 'type' => 'none']);
            $edit_button = true;

            return view(
                'users.details',
                array_merge(compact('title', 'breadcrumbs', 'data', 'edit_button'), $res)
            );
        }
        Alert::error(__('messages.invalid_request'))->autoclose(3000);
        return redirect()->route('users.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = User::find(decrypt($id));

        if($data && \Auth::user()->companyAccess($data->company_id))
        {
            $breadcrumbs = [
                route('users.index') => trans('controllers.users'),
                route('users.show', encrypt($data->id)) => $data->first_name .' '.$data->last_name,
                '' => trans('controllers.edit'),
            ];
            $title = trans('controllers.edit_user');
            $companies = Company::getLists();
            return view('users.form', compact('title', 'breadcrumbs', 'companies', 'data'));
        }
        Alert::error(__('messages.invalid_request'))->autoclose(3000);
        return redirect()->route('users.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'first_name' => 'required',
            'email' => 'required|email',
            'role_id' => 'required',
        ];

        if(Auth::user()->company_id == null)
        {
          $rules['company_id'] = 'required';
        }

        $request->validate($rules);

        $record = User::find(decrypt($id));
        if($record && \Auth::user()->companyAccess($record->company_id))
        {
            $record = $this->save($record, $request);
            if($record)
            {
                Alert::success(__('messages.save_success'))->autoclose(3000);
                return redirect()->route('users.show', ['id' => $id]);
            }
            Alert::error(__('messages.save_failed'))->autoclose(3000);
            return redirect()->back()->withInput();
        }
        Alert::error(__('messages.invalid_request'))->autoclose(3000);
        return redirect()->back()->withInput();
    }

    /**
     * Save Data
     *
     * @param  obj  $record
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    function save($record, $request)
    {

        $sendWelcomeEmail = false;
        $record->first_name = $request->first_name;
        $record->last_name = $request->last_name;
        $record->company_id = $request->company_id?:\Auth::user()->company_id;
        $record->email = $request->email;
        $record->role = $record->company_id ? 1 : 0;
        $record->role_id = $request->role_id;
        $record->team_id = $request->team_id;
        $record->department = $request->department;
        $record->deleted_at = null;
        if(isset($request->active))
        $record->active = $request->active ? true : false;
        else {
          $record->active = true;
        }
        if(isset($request->google2fa_enable))
        $record->google2fa_enable = $request->google2fa_enable ? true : false;
        else
        $record->google2fa_enable = false;

        if($record->id){
            $record->updated_by = \Auth::id();
            $sendWelcomeEmail = false;
        }
        else{
            $record->created_by = \Auth::id();
            $sendWelcomeEmail = true;
           /*
           if($request->password)
           {
               $record->password = bcrypt($request->password);
           }else{
               $record->password = '';
               $record->active = false;
           }
           $sendWelcomeEmail = true;
           */
        }


        if($request->password && trim($request->password) !="")
        {
            $record->password = bcrypt($request->password);
            $sendWelcomeEmail = true;
        }
        elseif($record->password == "") {

            $record->password = '';
        //    $record->active = false;

        }


        if($record->save())
        {

            if($sendWelcomeEmail)
            {
                \DB::table('token_verify')->insert([
                    'email' => $record->email,
                    'token' => str_random(32),
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
                dispatch(new \App\Jobs\SendEmail($record, 'WelcomeEmail'));
            }

           if(isset($request->courses) && $request->courses!="")
           {
              $coursesArr = explode("-", $request->courses);

                for($c=0; $c<count($coursesArr); $c++)
                {
                      $exists = CourseMember::withTrashed()
                                  ->where('course_id', $coursesArr[$c])
                                  ->where('company_id', $record->company_id);
                      $exists = $exists->where('user_id', $record->id)->first();

                      if($exists){
                          $recordCM = CourseMember::withTrashed()->find($exists->id);
                          $recordCM->updated_by = \Auth::id();
                      }
                      else{
                          $recordCM = new CourseMember;
                          $recordCM->created_by = \Auth::id();
                      }
                      $recordCM->course_id = $coursesArr[$c];
                      $recordCM->company_id = $record->company_id;
                      $recordCM->deleted_at = null;
                      $recordCM->deleted_by = null;
                      $recordCM->user_id = $record->id;
                      $recordCM->team_id = $record->team_id;

                    if($recordCM->save()){

                      $courseUser = CourseUser::withTrashed()
                                              ->where('course_id', $coursesArr[$c])
                                              ->where('user_id', $record->id)
                                              ->first();
                      if(!$courseUser)
                      {
                          $courseUser = new CourseUser;
                          $courseUser->created_by = \Auth::id();
                      }else{
                          $courseUser->created_by = \Auth::id();
                          $courseUser->deleted_at = null;
                      }
                      $courseUser->course_id = $coursesArr[$c];
                      $courseUser->course_member_id = $recordCM->id;
                      $courseUser->user_id = $record->id;
                      $courseUser->enrol_date = date('Y-m-d H:i:s');
                      $courseUser->enrolled_by = \Auth::id();
                      $courseUser->role = '';
                      $courseUser->save();
                    }
                 }
           }

            return $record;
        }
        return false;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $record = User::find(decrypt($id));
        if($record && \Auth::user()->companyAccess($record->company_id))
        {
            $record->updated_by = \Auth::id();
            $record->deleted_at = date('Y-m-d H:i:s');
            if($record->save())
            {
                Alert::success(__('messages.delete_success'))->autoclose(3000);
                return redirect()->route('users.index');
            }
            Alert::error(__('messages.delete_failed'))->autoclose(3000);
            return redirect()->route('users.index');
        }
        Alert::error(__('messages.invalid_request'))->autoclose(3000);
        return redirect()->route('users.index');
    }

    /**
     * Show import page.
     *
     * @return \Illuminate\Http\Response
     */
    public function import()
    {
        $companies = Company::getLists();
        $breadcrumbs = [
            route('users.index') => trans('controllers.users'),
            '' => trans('controllers.bulk_import'),
        ];
        $title = trans('controllers.bulk_import_user');

        $companyId = Auth::user()->company_id ;
        $userCompany = Company::where("id", $companyId)->first();
        $companyUserCount = User::where("company_id", $companyId)->count();

        $limit = false ;
        if (!Auth::user()->isSysAdmin() &&
            $userCompany->max_users != "" &&
            $companyUserCount >= $userCompany->max_users) {
          $limit = true;
          $breadcrumbs = [
              route('users.index') => trans('controllers.users'),
              '' => trans('controllers.bulk_import'),
          ];
          $title = trans('controllers.bulk_import_user');
        }

        return view('users.import', compact('breadcrumbs', 'title', 'companies', 'limit'));
    }

    /**
     * Handle Import.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function doImport(Request $request)
    {
        $path = $request->file('file')->getRealPath();
        $data = array_map('str_getcsv', file($path));
        $imported = 0;
        $resultLog = '';


       if(!Auth::user()->isSysAdmin())
       {
        $companyId = Auth::user()->company_id ;
        $userCompany = Company::where("id", $companyId)->first();
        $companyUserCount = User::where("company_id", $companyId)->count();

        $companyUserCount += count($data) ;


        $limit = false ;
        if($userCompany->max_users!="" && $companyUserCount >= $userCompany->max_users)
        {
          $limit = true;
          $breadcrumbs = [
              route('users.index') => trans('controllers.users'),
              '' => trans('controllers.bulk_import'),
          ];
          $title = trans('controllers.bulk_import_user');
          return view('users.import', compact('breadcrumbs', 'title', 'limit'));
        }
      }


        for($i = 1; $i < count($data); $i++)
        {
          if(count($data[$i]) != 9)
           {
             $resultLog .= "<p class='text-danger'>[".trans('controllers.row')." $i] ".trans('controllers.invalid_columns_count')."</p>";
           }
            $first_name = $data[$i][0];
            $last_name = $data[$i][1];
            $email = $data[$i][2];
            if (Auth::user()->isSysAdmin()) {
                $company_name = $data[$i][3];
            } else if (trim($data[$i][3]) == "" || ($data[$i][3] == $userCompany->company_name)) {
                $company_name = $userCompany->company_name;
            } else {
                continue;
            }

            $team_name = $data[$i][4];
            $department = $data[$i][5];

            if(trim($data[$i][6]) == "")
             $role_name = "Learner";
            else
             $role_name = $data[$i][6];

            $password = $data[$i][7];

            if(isset($data[$i][8]))
            $courses = $data[$i][8];
            else {
              $courses = '';
            }

            if($first_name == '' || $email == '' || $company_name == '' || $role_name == '')
            {
                $resultLog .= "<p class='text-danger'>[".trans('controllers.row')." $i] ".trans('controllers.required_field_is_empty')."</p>";
            }
            else{
                $company = Company::whereCompanyName($company_name)->first();

                if($company)
                {
                    $company_id = $company->id;
                    if(filter_var($email, FILTER_VALIDATE_EMAIL))
                    {
                        $emailExist = User::checkEmailExists($email);


                        if(!$emailExist)
                        {

                          $role = Role::whereCompanyId($company_id)->whereRoleName($role_name)->first();
                          if(!$role)
                          {
                            $role = Role::whereRoleName($role_name)->first();
                          }


                            if($role)
                            {
                                $role_id = $role->id;
                                if($team_name)
                                {
                                    $team = Team::whereCompanyId($company_id)->whereTeamName($team_name)->first();
                                    $team_id = @$team->id ?: null;
                                }
                                else
                                $team_id = null;

                                $obj = new \StdClass;
                                $obj->first_name = $first_name;
                                $obj->last_name = $last_name;
                                $obj->email = $email;
                                $obj->company_id = $company_id;
                                $obj->password = $password;
                                $obj->active = true;
                                $obj->courses = $courses;
                                $obj->role_id = $role_id;
                                $obj->team_id = $team_id;
                                $obj->department = $department;

                                if($this->save(new User, $obj)){
                                    $resultLog .= "<p class='text-success'>[".trans('controllers.row')." $i] $email  ".trans('controllers.imported_successfully')."</p>";
                                    $imported++;
                                } else {
                                    $resultLog .= "<p class='text-danger'>[".trans('controllers.row')." $i] ".trans('controllers.error_occured_when_saving')."</p>";
                                }
                            }
                            else
                                $resultLog .= "<p class='text-danger'>[".trans('controllers.row')." $i] ".trans('controllers.role_not_exists', ['role' => $role_name])."</p>";
                        }
                        else
                        {
                            $user = User::withTrashed()->where("email", $email)->restore();
                            $resultLog .= "<p class='text-danger'>[".trans('controllers.row')." $i] ".trans('controllers.email_registered', ['email' => $email])."</p>";
                            $resultLog .= "<p class='text-danger'>[".trans('controllers.row')." $i] ".trans('controllers.user_updated', ['email' => $email])."</p>";

                         }
                    }
                    else
                        $resultLog .= "<p class='text-danger'>[".trans('controllers.row')." $i] ".trans('controllers.email_format', ['email' => $email])."</p>";
                }
                else
                    $resultLog .= "<p class='text-danger'>[".trans('controllers.row')." $i] ".trans('controllers.company_exists', ['company' => $company_name])."</p>";
            }

        }
        $i--;
        $resultLog .= "<p class='text-info'>".trans('controllers.imported_row')." : <b>$i</b></p>";
        $resultLog .= "<p class='text-success'>".trans('controllers.success')." : <b>$imported</b></p>";
        $resultLog .= "<p class='text-danger'>".trans('controllers.failed')." : <b>".($i-$imported)."</b></p>";

        return redirect()->route('users.import.log')->with('log', $resultLog);
    }

    /**
     * Show Log of Import process
     */
    public function importLog()
    {
        $breadcrumbs = [
            route('users.index') => trans('controllers.list_of_users'),
            route('users.import') => trans('controllers.bulk_import'),
            '' => trans('controllers.import_log'),
        ];
        return view('users.import_result', compact('breadcrumbs'));
    }

    /**
     * Show Active Directory configuration page
     *
     * @return \Illuminate\Http\Response
     */

    public function adSetup(Request $request)
    {
        $breadcrumbs = [
            route('users.index') => trans('controllers.users'),
            '' => trans('controllers.ad_setup'),
        ];
        $title = trans('controllers.ad_setup');
        $data = auth()->user()->azure;

        // check user limit

        $limit = false;
        $user = auth()->user();
        $userCompany = $user->company;
        $companyUserCount = User::where("company_id", $user->company_id)->count();
        $maxUsers = $userCompany->max_users;

        $jobs_in_queue = \App\Job::where('queue', 'adsync')->get();
        $job_ahead_of = 0;
        $job_found = false;

        foreach ($jobs_in_queue as $key => $job) {
            $j = unserialize($job->payload['data']['command']);
            if ($j->company_admin == $user->id) {
                $job_found = true;
                break;
            }
            $job_ahead_of++;
        }

        $locked = Mutex::where('name', Mutex::$regular_ad_sync)
                        ->where('value', '>', 0)->first();

        $status = [];
        
        if ($locked) {
            if ($locked->value == $user->id) {
                // Currently working on regular Sync
                // Regular sync for this AD

                $status[] =  'Currently working on regular AD synch for this company';

                if ($userCompany->sync_processed < 0) {
                    // Getting user list from Azure AD
                    $status[] = 'Regular synch is getting user list from Azure AD';
                } else if ($userCompany->sync_processed < $userCompany->sync_total) {
                    // Sync progress percent
                    // $userCompany->sync_processed / $userCompany->sync_total
                    $percent = round($userCompany->sync_processed * 100 / $userCompany->sync_total);
                    $status[] = sprintf(
                        'Processed %d percent (%d out of %d)',
                        $percent, $userCompany->sync_processed,
                        $userCompany->sync_total
                    );
                } else {
                    // Updating Database
                    $status[] = 'Regular synch is updating database';
                }
            } else {
                // Currently working on regular Sync
                $status[] = 'Currently working on regular AD synch';
            }
        } else if ($jobs_in_queue->count() > 0 && $jobs_in_queue[0]->sync_processed < -1) {
            // Waiting for regular sync to be completed
        }
        
        if ($job_ahead_of == 0 && !$job_found) {
            // No job pending for ahead of this job
            $status[] = 'No pending job ahead of your update';
            
            if ($locked) {
                // and will your update will start after regular sync completes
                $status[] = 'Your update will start after regular sync completes';
            } else {
                $status[] = 'Okay, ready to update';
                // okay, ready to udpate
            }
        } else if ($job_ahead_of > 0 && !$job_found) {
            $status[] = sprintf('There are %d jobs in the queue. Your update will start after those job completes if you click on Save.', $job_ahead_of);
        } else if ($job_ahead_of > 0 && $job_found) {
            // Pendng
            // There are Ahead of this job
            // if update, cancel this job and will add to the last of queue(jobs_in_queue)
            $status[] = sprintf('Your update is pending now... There are %d jobs ahead of your update', $job_ahead_of);
            $status[] = 'If click on Save, pending update will be cancelled and added to the last of queue';
        } else if ($userCompany->sync_processed < 0) {
            // Getting user list from Azure AD
            // if update, cancel this job and will add to the last of queue(jobs_in_queue)
            $status[] = 'AD update is getting user list from Azure AD';
            $status[] = 'If click on Save, update will be cancelled and added to the last of queue';
        } else if ($userCompany->sync_processed < $userCompany->sync_total) {
            // Sync progress percent
            // $userCompany->sync_processed / $userCompany->sync_total
            // if update, cancel this job and will add to the last of queue(jobs_in_queue)
            $percent = round($userCompany->sync_processed * 100 / $userCompany->sync_total);
            $status[] = sprintf(
                'Update processed %d percent (%d out of %d)',
                $percent,
                $userCompany->sync_processed,
                $userCompany->sync_total
            );
            $status[] = 'If click on Save, update is cancelled and will add to the last of queue';
        } else {
            // Updating Database
            // if update, cancel this job and will add to the last of queue(jobs_in_queue)
            $status[] = 'Updating database';
            $status[] = 'If click on Save, update is cancelled and will add to the last of queue';
        }
        
        if (!$user->isSysAdmin() &&
            $userCompany->max_users != "" &&
            $companyUserCount >= $userCompany->max_users) {
            $limit = true;
        }

        return view('users.adsetup', compact(
            'breadcrumbs',
            'title',
            'data',
            'limit',
            'maxUsers',
            'status'
        ));
    }

    public function updateAdSetup(Request $request, ADSync $adSync)
    {
        $tenant_id = $request->tenant_id;
        $client_id = $request->client_id;
        $client_secret = $request->client_secret;

        $validator = \Validator::make($request->all(), [
            'tenant_id' => 'required',
            'client_id' => 'required',
            'client_secret' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator); 
        }

        $reason = NULL;
        $valid = $adSync->checkAzure($tenant_id, $client_id, $client_secret, $reason);

        if ($valid) {
            $user = auth()->user();
            $user->azure()->updateOrCreate([], [
                'tenant_id' => $tenant_id,
                'client_id' => $client_id,
                'client_secret' => $client_secret
            ]);

            $jobs_in_queue = \App\Job::where('queue', 'adsync')->get();
            // Delete all jobs in the queue this company admin created for AD sync
            foreach ($jobs_in_queue as $job) {
                $j = unserialize($job->payload['data']['command']);
                if ($j->company_admin == $user->id) {
                    $job->forceDelete();
                }
            }

            dispatch((new \App\Jobs\SyncAD($user->id))->onQueue('adsync'));
        } else {
            $validator = \Validator::make($request->all(), [
                'tenant_id' => [
                    function ($attr, $value, $fail) use ($reason) {
                        $fail($reason);
                    }
                ]
            ]);

            return redirect()->back()->withInput()->withErrors($validator);
        }

        return redirect(route('users.adsetup'));
    }

    public function statusUpdate(Request $request)
    {
       $update = User::where('id', $request->id)->update(['active'=>$request->status]);

       if($update)
        return trans("modules.status_updated") ;
       else
         return "error";
    }

    public function bulkActiveUser(Request $request)
    {
      $userIds = json_decode($request->ids) ;

        if(count($userIds) >0)
        {

          $result = User::whereIn('id', $userIds)->update(['active' => 1]);

          if($result)
          {
            return trans("messages.save_success") ;
          }
          else
          {
             return "error";
          }
       }

    }

    public function bulkInactiveUser(Request $request)
    {
      $userIds = json_decode($request->ids) ;

        if(count($userIds) >0)
        {

          $result = User::whereIn('id', $userIds)->update(['active' => 0]);

          if($result)
          {
            return trans("messages.save_success") ;
          }
          else
          {
            return "error";
          }
       }

    }

    public function bulkDeleteUser(Request $request)
    {
      $userIds = json_decode($request->ids) ;

        if(count($userIds) >0)
        {

          $result = User::whereIn('id', $userIds)->delete();

          if($result)
          {
            return trans("messages.delete_success") ;
          }
          else
          {
            return "error";
          }
       }

    }
}
