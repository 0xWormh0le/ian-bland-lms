<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Course;
use App\Module;
use App\Company;
use App\CourseCompany;
use App\CourseMember;
use App\CourseUser;
use App\User;
use App\CourseConfig;
use Yajra\Datatables\Datatables;
use Alert;
use \Carbon\Carbon;
Use Exception;

class CourseCompanyController extends Controller
{

    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function enrolledCompanies($course_id)
    {
        $data = CourseCompany::select(
            'course_companies.id',
            'company_name',
            'course_companies.updated_at',
            'course_id',
            'course_companies.company_id',
            'courses.slug as course_slug',
            'companies.slug',
            'courses.created_by'
        )
            ->leftJoin('companies', 'companies.id', '=', 'course_companies.company_id')
            ->leftJoin('courses', 'courses.id', '=', 'course_companies.course_id')
            ->where('course_id', $course_id);

        return Datatables::of($data)
            ->addColumn('action', function ($data) {
                $html = '';
                $html .= '<a href="'.route('courses.companies.show', ['course'=>$data->course_slug, 'company' => $data->slug]).
                        '" class="btn btn-info btn-sm"><i class="icon-info"></i> '.trans('controllers.detail').'</a> ';
                $user = User::find($data->created_by);

                if($user->company_id !=  $data->company_id)
                $html .= '<button type="button" class="btn btn-danger btn-sm enrol" data-id="'.$data->id.'" data-action="unenroll">'.
                        '<i class="icon-close"></i> '.trans('controllers.unenroll').'</button> ';

                return $html;
            })
            ->rawColumns(['action'])
            ->make(true);
    }


    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function unEnrolledCompanies($course_id)
    {
        $enrolled = CourseCompany::where('active', 1)->where('course_id', $course_id)->pluck('company_id');

        $data = Company::whereNotIn('id', $enrolled);

        return Datatables::of($data)
            ->addColumn('action', function ($data) {
                return '<button type="button" class="btn btn-success btn-sm enrol" data-id="'
                        . $data->id
                        . '" data-action="enroll"><i class="icon-check"></i> '
                        . trans('controllers.enroll')
                        . '</button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Enroll company to multiple courses.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function enrollMultipleCourse(Request $request)
    {
        $company = Company::find($request->company_id);

        if ($request->action == 'enroll') {
            $courses = $request->courses;
            foreach ($courses as $course_id) {
                $this->enrollOne($course_id, $company->id);
            }
        } else {
            $course_id = $request->course;
            $record = CourseCompany::where([
                ['course_id', $course_id],
                ['company_id', $company->id]
            ])->first();

            $record->deleted_by = \Auth::id();
            $record->deleted_at = date('Y-m-d H:i:s');
            $saved = $record->save();
        }

        return redirect()->route('companies.show', $company->slug);
    }

    protected function enrollOne($course_id, $company_id)
    {
        $user_id = auth()->user()->id;
        $record = CourseCompany::withTrashed()
                    ->where('course_id', $course_id)
                    ->where('company_id', $company_id)
                    ->first();

        if ($record) {
            $record->updated_by = $user_id;
        } else {
            $record = new CourseCompany;
            $record->created_by = $user_id;
        }

        $record->course_id = $course_id;
        $record->company_id = $company_id;
        $record->deleted_at = null;
        $record->deleted_by = null;

        $config = CourseConfig::withTrashed()
                    ->where('course_id', $course_id)
                    ->where('company_id', $company_id)
                    ->first();

        if (!$config) {
            $config = new CourseConfig;
        }

        $config->course_id = $course_id;
        $config->company_id = $company_id;
        $config->transversal_rule = 'none';
        $config->completion_rule = 'any';
        $config->completion_modules = '';
        $config->completion_percentage = 0;
        $config->learning_path = '';
        $config->get_certificate = 0;
        $config->deleted_at = null;
        $config->deleted_by = null;
        $config->save();

        return $record->save();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function enroll(Request $request)
    {
        $saved = false;
        
        if ($request->action == 'enroll') {
            $course_id = $request->course_id;
            $company_id = $request->company_id;
            $saved = $this->enrollOne($course_id, $company_id);
        } else if ($request->action == 'unenroll') {
            $record = CourseCompany::find($request->id);
            $record->deleted_by = \Auth::id();
            $record->deleted_at = date('Y-m-d H:i:s');
            $saved = $record->save();
        }

        if ($saved) {
            $msg = $request->action == 'enroll'
                ? trans('controllers.company_enrolled')
                : trans('controllers.company_unenrolled');
        }

        return @$msg ?: 'error';
    }


    /**
     * Display the specified resource.
     *
     * @param  string  $course_slug
     * @param  string  $company_slug
     * @return \Illuminate\Http\Response
     */
    public function show($course_slug, $company_slug)
    {
        $course = Course::findBySlug($course_slug);
        $company = Company::findBySlug($company_slug);

        if (!auth()->user()->isSysAdmin()) {
            if (is_null($company) || $company->id != auth()->user()->company_id) {
                return abort(401);
            }
        }

		if(isset($company->id))
        	$data = CourseCompany::findByCourseCompany($course->id, $company->id);

		if(isset($data))
        {
            $breadcrumbs = [
                route('courses.index') => trans('controllers.list_of_courses'),
                route('courses.show', $course_slug) => $course->title,
                '' => $company->company_name,
            ];
            $title = trans('controllers.course_details_of').$company->company_name;
            return view('courses.company.details', compact('title', 'breadcrumbs', 'data'));
        }
        Alert::error(__('messages.record_not_found'))->autoclose(3000);
        return redirect()->route('courses.show', $course_slug);
    }


    /**
     * Display the specified resource.
     *
     * @param  string  $course_slug
     * @param  string  $company_slug
     * @param  string  $module_id
     * @return \Illuminate\Http\Response
     */
    public function scheduleEdit($course_slug, $company_slug, $module_id)
    {
        $course = Course::findBySlug($course_slug);
        $company = Company::findBySlug($company_slug);
        $module = Module::find($module_id);
        if($course && $company && $module)
        {
          /*  if($module->type == 'Classroom')
                $data = Classroom::select('*');
            else
                $data = Webex::select('*');

            $data = $data->where('course_id', $course->id)
                                ->where('company_id', $company->id)
                                ->where('module_id', $module_id)
                                ->first();
            */
            $breadcrumbs = [
                route('courses.index') => trans('controllers.list_of_courses'),
                route('courses.show', $course_slug) => $course->title,
                route('courses.companies.show', ['course' => $course_slug, 'company' => $company_slug]) => $company->company_name,
                '' => trans('controllers.edit_schedule'),
            ];
            $title = trans('controllers.schedule_of') . $module->title;
            return view('courses.company.schedule-form', compact('title', 'breadcrumbs', 'data', 'course', 'company', 'module'));
        }
        Alert::error(__('messages.record_not_found'))->autoclose(3000);
        return redirect()->route('courses.companies.show', ['course' => $course_slug, 'company' => $company_slug]);
    }

    /**
     * Update Schedule.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $course_slug
     * @param  string  $company_slug
     * @param  int  $module_id
     * @return \Illuminate\Http\Response
     */
    public function scheduleUpdate(Request $request, $course_slug, $company_slug, $module_id)
    {
        $rules = [
            'title' => 'required',
            'instructor_user_id' => 'required',
            'start_date' => 'required',
            'start_time' => 'required',
        ];
        $request->validate($rules);
/*
        if($request->type == 'Classroom')
            $exists = Classroom::select('id');
        else
            $exists = Webex::select('id');

        $exists = $exists->where('course_id', $request->course_id)
                        ->where('company_id', $request->company_id)
                        ->where('module_id', $request->module_id)
                        ->first();

        if($exists)
        {
            if($request->type == 'Classroom')
                $record = Classroom::find($exists->id);
            else
                $record = Webex::find($exists->id);
        }
        else{
            if($request->type == 'Classroom')
                $record = new Classroom;
            else
                $record = new Webex;
        }
        $record->course_id = $request->course_id;
        $record->company_id = $request->company_id;
        $record->module_id = $request->module_id;
        $record->title = $request->title;
        $record->start_date = $request->start_date;
        $record->start_time = $request->start_time;
        $record->duration = $request->duration;
        $record->duration_type = $request->duration_type;
        $record->instructor_user_id = $request->instructor_user_id;
        $record->capacity = $request->capacity;
        $record->description = $request->description;

        if($record->save())
        {
            Alert::success(__('messages.save_success'))->autoclose(3000);
            return redirect()->route('courses.companies.show', ['course' => $course_slug, 'company' => $company_slug]);
        }
        */
        Alert::error(__('messages.save_failed'))->autoclose(3000);
        return redirect()->back()->withInput();
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function teamMembers($course_id, $company_id)
    {
        $data = CourseMember::select(
                    'course_members.id',
                    'team_name',
                    'course_members.updated_at'
                )->leftJoin('teams', 'teams.id', '=', 'team_id')
                ->where('course_members.course_id', $course_id)
                ->where('course_members.company_id', $company_id)
                ->whereNull('course_members.user_id');


        return Datatables::of($data)
            ->addColumn('action', function ($data) {
                return '<button type="button" class="btn btn-danger btn-sm enrolmember" data-id="'
                        . $data->id
                        . '" data-action="unenroll" data-type="team"><i class="icon-close"></i> '
                        . trans('controllers.unenroll')
                        . '</button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

     /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function teamMembersUnenrolled($course_id, $company_id)
    {
        $existsTeams = CourseMember::where('course_id', $course_id)
                                ->where('company_id', $company_id)
                                ->whereNull('user_id')
                                ->pluck('team_id');

        $data = \App\Team::select('teams.id', 'team_name')
                    ->whereNotIn('teams.id', $existsTeams)
                    ->where('teams.company_id', $company_id);

        return Datatables::of($data)
            ->addColumn('action', function ($data) {
                return '<button type="button" class="btn btn-success btn-sm enrolmember" data-id="'
                        . $data->id
                        . '" data-action="enroll" data-type="team"><i class="icon-check"></i> '
                        . trans('controllers.enroll')
                        . '</button> ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userMembers($course_id, $company_id)
    {
        $data = CourseMember::select('course_members.id', 'first_name','last_name','role_name', 'course_members.updated_at')
                    ->join('users', 'users.id', '=', 'user_id')
                    ->leftJoin('roles', 'roles.id', '=', 'users.role_id')
                    ->where('course_members.course_id', $course_id)
                    ->where('course_members.company_id', $company_id)
                    ->whereNotNull('course_members.user_id');

        return Datatables::of($data)
            ->addColumn('action', function ($data) {
                return '<button type="button" class="btn btn-danger btn-sm enrolmember" data-id="'
                        . $data->id
                        . '" data-action="unenroll" data-type="user"><i class="icon-close"></i> '
                        . trans('controllers.unenroll')
                        . '</button> ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userMembersUnenrolled($course_id, $company_id)
    {
        $existsTeams = CourseMember::where('course_id', $course_id)
                                ->where('company_id', $company_id)
                                ->whereNull('user_id')
                                ->pluck('team_id');

        $existsUsers = CourseMember::where('course_id', $course_id)
                                ->where('company_id', $company_id)
                                ->whereNull('team_id')
                                ->pluck('user_id')
                                ->toArray();
                                
        $userOfTeam = User::select('id')
                                ->whereIn('team_id', $existsTeams)
                                ->pluck('id')
                                ->toArray();

        $existsUsers = array_merge($existsUsers, $userOfTeam);

        $data = User::select(
                        'users.id',
                        'users.first_name',
                        'users.last_name',
                        'roles.role_name',
                        'teams.team_name'
                    )
                    ->leftJoin('roles', 'roles.id', '=', 'users.role_id')
                    ->leftJoin('teams', 'teams.id', '=', 'users.team_id')
                    ->where('users.company_id', $company_id)
                    ->where('users.is_suspended', false)
                    ->where('users.role_id', '!=', 0)
                    ->where('users.role_id', '!=', 1)
                    ->whereNotIn('users.id', $existsUsers);

        return Datatables::of($data)
            ->addColumn('action', function ($data) {
                return '<button type="button" class="btn btn-success btn-sm enrolmember" data-id="' .
                    $data->id .
                    '" data-action="enroll" data-type="user"><i class="icon-check"></i> ' .
                    trans('controllers.enroll').'</button> ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function enrollMember(Request $request, $course_id, $company_id)
    {

        if ($request->action == 'enroll') {
            $exists = CourseMember::withTrashed()
                        ->where('course_id', $course_id)
                        ->where('company_id', $company_id);
            if ($request->type == 'user') {
                $exists = $exists->where('user_id', $request->id)->first();
            } else {
                $exists = $exists->where('team_id', $request->id)->whereNull('user_id')->first();
            }

            if ($exists) {
                $record = CourseMember::withTrashed()->find($exists->id);
                $record->updated_by = \Auth::id();
            } else {
                $record = new CourseMember;
                $record->created_by = \Auth::id();
            }

            $record->course_id = $course_id;
            $record->company_id = $company_id;
            $record->deleted_at = null;
            $record->deleted_by = null;

            if ($request->type == 'user') {
                $record->user_id = $request->id;
                $record->team_id = @User::find($request->id)->team_id;
            } else {
                $record->team_id = $request->id;
            }
        } else if ($request->action == 'unenroll') {
            $record = CourseMember::find($request->id);
            $record->deleted_by = \Auth::id();
            $record->deleted_at = date('Y-m-d H:i:s');
        }

        if ($record->save()) {
            if ($request->type == 'user') {
                $userIds = [$record->user_id];
            } else {
                $userIds = User::select('id')
                                ->where('team_id', $record->team_id)
                                ->pluck('id')
                                ->toArray();
            }

            if ($request->action == 'enroll') {
                foreach ($userIds as $user_id) {
                    $courseUser = CourseUser::withTrashed()
                                            ->where('course_id', $record->course_id)
                                            ->where('user_id', $user_id)
                                            ->first();
                    if (!$courseUser) {
                        $courseUser = new CourseUser;
                        $courseUser->created_by = \Auth::id();
                    } else {
                        $courseUser->created_by = \Auth::id();
                        $courseUser->deleted_at = null;
                    }

                    $courseUser->course_id = $record->course_id;
                    $courseUser->course_member_id = $record->id;
                    $courseUser->user_id = $user_id;
                    $courseUser->enrol_date = date('Y-m-d H:i:s');
                    $courseUser->enrolled_by = \Auth::id();
                    $courseUser->role = '';
                    $courseUser->start_date = $request->start_date ?: date('Y-m-d');
                    $courseUser->self_enroll = $request->self_enroll;
                    $courseUser->save();

                    if (empty($request->start_date) || $request->start_date == date('d/m/Y')) {
                        dispatch(new \App\Jobs\SendEmail($courseUser, 'CourseMemberEnrollment'));
                    }
                }
            } else if ($request->action == 'unenroll') {
                CourseUser::where('course_id', $record->course_id)
                            ->whereIn('user_id', $userIds)
                            ->delete();
                CourseMember::where('course_id', $record->course_id)
                            ->whereIn('user_id', $userIds)
                            ->delete();
            }

            $msg = $request->action == 'enroll'
                    ? trans('controllers.enrolled')
                    : trans('controllers.unenrolled');
        }

        return @$msg ?: 'error';
    }


    public function enrollAllUsers(Request $request, $course_id, $company_id)
    {
      try{

      $memberExists = CourseMember::where('course_id', $course_id)
                  ->where('company_id', $company_id)
                  ->pluck("user_id")
                  ->toArray();


      $unEnrolledUsers = User::select('id', 'team_id')->where('company_id', $company_id)
                         ->whereNotIn('id', $memberExists)
                         ->get();

     for($i=0; $i<count($unEnrolledUsers); $i++)
       {
        $courseEnrollMembers =  CourseMember::firstOrNew(['course_id' => $course_id,'company_id' => $company_id, 'user_id' => $unEnrolledUsers[$i]->id]);
        $courseEnrollMembers->company_id = $company_id;
        $courseEnrollMembers->user_id = $unEnrolledUsers[$i]->id;
        $courseEnrollMembers->team_id = $unEnrolledUsers[$i]->team_id;
        $courseEnrollMembers->active = 1;
        $courseEnrollMembers->created_by = \Auth::id();
        $courseEnrollMembers->created_at =  Carbon::now();
        $courseEnrollMembers->deleted_at =  null;
        $courseEnrollMembers->deleted_by =  null;
        $courseEnrollMembers->save();

        $courseEnrollUsers = CourseUser::firstOrNew(['course_id' => $course_id, 'user_id' => $unEnrolledUsers[$i]->id]);
        $courseEnrollUsers->user_id = $unEnrolledUsers[$i]->id;
        $courseEnrollUsers->course_member_id = $courseEnrollMembers->id;
        $courseEnrollUsers->active = 1;
        $courseEnrollUsers->created_by = \Auth::id();
        $courseEnrollUsers->enrolled_by = \Auth::id();
        $courseEnrollUsers->enrol_date = Carbon::now();
        $courseEnrollUsers->role = '';
        $courseEnrollUsers->start_date = $request->start_date ?: Carbon::now();
        $courseEnrollUsers->self_enroll =  $request->self_enroll;
        $courseEnrollUsers->created_at = Carbon::now();
        $courseEnrollUsers->deleted_at = null;
        $courseEnrollUsers->created_by = null;
        $courseEnrollUsers->save();

        dispatch(new \App\Jobs\SendEmail($courseEnrollUsers, 'CourseMemberEnrollment'));
     }

      $msg = trans('controllers.enrolled');

    }catch(Exception $exception)
    {
      $msg = trans('messages.db_error');

    }

      return @$msg ?: 'error';
    }

}
