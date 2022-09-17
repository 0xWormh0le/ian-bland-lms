<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Company;
use App\Team;
use App\User;
use App\CourseMember;
use Yajra\Datatables\Datatables;
use Alert;

class TeamController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = @session('menuLabel')['user-management.teams'] ?: @session('menuLabel')['portal-management.teams'];
        $breadcrumbs = [
            '' => $title
        ];
        $companies = Company::getLists();
        return view('teams.index', compact('title', 'breadcrumbs', 'companies'));
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function anyData(Request $request)
    {
        $data = Team::select(
            'teams.id',
            'teams.created_at',
            'team_name',
            'companies.company_name',
            'users.first_name',
            'users.last_name',
            'teams.slug'
        )
        ->leftJoin('companies', 'companies.id', '=', 'teams.company_id')
        ->leftJoin('users', 'users.id', '=', 'teams.manager_user_id');

        if($request->company_id)
            $data->where('teams.company_id', $request->company_id);

        return Datatables::of($data)
                        ->editColumn('first_name', function ($data) {
                            return $data->first_name.' '.$data->last_name;
                        })
                        ->addColumn('action', function ($data) {
                            return
                                show_button('show', 'teams.show', $data->slug)
                                ." ".
                                show_button('edit', 'teams.edit', $data->slug)
                                ." ".
                                show_button('delete', 'teams.destroy', encrypt($data->id))
                            ;
                        })
                        ->rawColumns(['action'])
                        ->make(true);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = trans('controllers.add_new_team');
        $breadcrumbs = [
            route('teams.index') => trans('controllers.team_management'),
            '' => $title,
        ];
        $companies = Company::getLists();
        return view('teams.form', compact('title', 'breadcrumbs', 'companies'));
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
            'team_name' => 'required',
        ];
        $request->validate($rules);

        $record = new Team;
        $record = $this->save($record, $request);
        if($record)
        {
            Alert::success(__('messages.save_success'))->autoclose(3000);
            return redirect()->route('teams.index');
        }
        Alert::error(__('messages.save_failed'))->autoclose(3000);
        return redirect()->back()->withInput();
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $data = Team::findBySlug($slug);
        if($data && \Auth::user()->companyAccess($data->company_id))
        {
            $breadcrumbs = [
                route('teams.index') => trans('controllers.team_management'),
                '' => $data->team_name,
            ];
            $title = trans('controllers.details_of').$data->team_name;
            return view('teams.details', compact('title', 'breadcrumbs', 'data'));
        }
        Alert::error(__('messages.invalid_request'))->autoclose(3000);
        return redirect()->route('teams.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function edit($slug)
    {
        $data = Team::findBySlug($slug);
        if($data && \Auth::user()->companyAccess($data->company_id))
        {
            $breadcrumbs = [
                route('teams.index') => trans('controllers.team_management'),
                route('teams.show', $data->slug) => $data->team_name,
                '' => trans('controllers.edit'),
            ];
            $title = trans('controllers.edit').' '.$data->team_name;
            $companies = Company::getLists();
            return view('teams.form', compact('title', 'breadcrumbs', 'companies', 'data'));
        }
        Alert::error(__('messages.invalid_request'))->autoclose(3000);
        return redirect()->route('teams.index');
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
            'team_name' => 'required',
        ];
        $request->validate($rules);

        $record = Team::find($id);
        if($record && \Auth::user()->companyAccess($record->company_id))
        {
            $record = $this->save($record, $request);
            if($record)
            {
                Alert::success(__('messages.save_success'))->autoclose(3000);
                return redirect()->route('teams.index');
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
    public function save($record, $request)
    {
        $record->company_id = $request->company_id ?: \Auth::user()->company_id;
        $record->team_name = $request->team_name;
        $record->manager_user_id = $request->manager_user_id;
        if($record->id)
        {
            $record->slug = null;
            $record->updated_by = \Auth::id();
        }else{
            $record->created_by = \Auth::id();
        }
        if($record->save())
            return $record;
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
        $record = Team::find(decrypt($id));
        if($record && \Auth::user()->companyAccess($record->company_id))
        {
            $record->deleted_by = \Auth::id();
            $record->save();
            if($record->delete())
            {
                Alert::success(__('messages.delete_success'))->autoclose(3000);
                return redirect()->route('teams.index');
            }
            Alert::error(__('messages.delete_failed'))->autoclose(3000);
            return redirect()->route('teams.index');
        }
        Alert::error(__('messages.invalid_request'))->autoclose(3000);
        return redirect()->route('teams.index');
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function enrolledUsers($team_id)
    {
        $data = User::select('users.id', 'first_name', 'last_name', 'email', 'roles.role_name')
                    ->leftJoin('roles', 'roles.id', '=', 'role_id')
                    ->where('team_id', $team_id);

        return Datatables::of($data)
            ->addColumn('action', function ($data) {
                return '<button type="button" class="btn btn-danger btn-sm enrol" data-id="'
                        . $data->id
                        . '" data-action="unenroll"><i class="icon-close"></i> '
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
    public function unEnrolledUsers($company_id)
    {
        $data = User::select('users.id', 'first_name', 'last_name', 'email', 'roles.role_name')
                    ->leftJoin('roles', 'roles.id', '=', 'role_id')
                    ->where('users.company_id', $company_id)
                    ->whereNull('team_id');

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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function enroll(Request $request, $team_id)
    {
        $user = User::find($request->user_id);
        if($request->action == 'enroll')
            $user->team_id = $team_id;
        elseif($request->action == 'unenroll')
            $user->team_id = null;
        if($user->save())
            $msg = $request->action == 'enroll' ? trans('controllers.user_enrolled') : trans('controllers.user_unenrolled');

        return @$msg ?: trans('controllers.error');
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function enrolledCourses($team_id)
    {
        $data = CourseMember::select(
                        'courses.title',
                        'users.first_name',
                        'users.last_name',
                        'course_members.created_by AS enrolled_by'
                    )
                    ->leftJoin('courses', 'courses.id', '=', 'course_members.course_id')
                    ->leftJoin('users', 'users.id', '=', 'course_members.created_by')
                    ->where('course_members.team_id', $team_id)
                    ->distinct('course_members.course_id');

        return Datatables::of($data)
            ->editColumn('enrolled_by', function ($data) {
                $name = $data->first_name.' '.$data->last_name;
                return $name;
            })
            ->make(true);
    }

    public function companyTeamList(Request $request){

        if($request->company_id !="" && $request->company_id > 0)
          $teamUsers = User::select('id','first_name', 'last_name')->where("company_id", $request->company_id)->get();
        else {
          $teamUsers = User::select('id','first_name', 'last_name')->where("company_id", '!=', null)->get();
         }

         return response()->json($teamUsers);

    }

}
