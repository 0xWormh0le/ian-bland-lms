<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Role;
use App\Menu;
use App\User;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use Alert;

class RoleController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('users.roles.index')->with([
            'companies' => \App\Company::getLists(),
            'title' => @session('menuLabel')['user-management.roles'] ?: @session('menuLabel')['portal-management.role-access']
        ]);
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function anyData(Request $request)
    {
        $data = Role::select('roles.id', 'role_name', 'roles.created_at', 'company_id', 'is_client')
                    ->whereCompanyId(\Auth::user()->company_id);

        return Datatables::of($data)
                    ->editColumn('role_name', function ($data) {
                        $html = $data->role_name;
                        if(!\Auth::user()->company_id && $data->is_client)
                        {
                            $html .= ' <small class="text-muted">'.trans("controllers.default_for_client").'</small>';
                        }
                        return $html;
                    })
                    ->addColumn('action', function ($data) {
                        return
                            show_button('show', 'roles.show', encrypt($data->id))
                            ." ".
                            show_button('edit', 'roles.edit', encrypt($data->id))
                            ." ".
                            show_button('delete', 'roles.destroy', encrypt($data->id))
                        ;
                    })
                    ->rawColumns(['role_name', 'action'])
                    ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $title = trans("controllers.add_new_role");
        $role = new Role();
        if($request->type == 'client' || \Auth::user()->company_id)
        {
            $roles = $role->clientRoles();
            $title .= ' '.trans("controllers.as_default_for_client");
            $is_client = true;
        }
        else{
            $roles = $role->systemRoles();
            $is_client = false;
        }

        $breadcrumbs = [
            route('roles.index') => trans("controllers.roles"),
            '' => $title,
        ];

        return view('users.roles.form', compact('title', 'roles', 'is_client'));
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
            'role_name' => 'required',
        ];
        $request->validate($rules);
        $record = new Role;
        $record = $this->save($record, $request);
        if($record)
        {
            Alert::success(__('messages.save_success'))->autoclose(3000);
            return redirect()->route('roles.show', encrypt($record->id));
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
    public function show($id)
    {
        $data = Role::find(decrypt($id));
        if($data && $data->company_id == \Auth::user()->company_id)
        {
            $breadcrumbs = [
                route('roles.index') => trans("controllers.roles"),
                '' => $data->role_name,
            ];
            $title = trans("controllers.details_of").$data->role_name;
            $viewOnly = true;

            $roles = $data->is_client ? $data->clientRoles() : $data->clientRoles();

            return view('users.roles.details', compact('title', 'breadcrumbs', 'data','roles',  'viewOnly'));
        }
        Alert::error(__('messages.invalid_request'));
        return redirect()->route('roles.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Role::find(decrypt($id));
        if($data && \Auth::user()->company_id == $data->company_id)
        {
            $breadcrumbs = [
                route('roles.index') => trans("controllers.roles"),
                route('roles.show', encrypt($data->id)) => $data->role_name,
                '' => trans("controllers.edit"),
            ];
            $title = trans("controllers.edit").' '.$data->role_name;

            $roles = $data->is_client ? $data->clientRoles() : $data->systemRoles();
            return view('users.roles.form', compact('title', 'breadcrumbs', 'roles', 'data'));
        }
        Alert::error(__('messages.invalid_request'));
        return redirect()->route('roles.index');
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
            'role_name' => 'required',
        ];

        $request->validate($rules);

        $record = Role::find(decrypt($id));
        if($record && \Auth::user()->companyAccess($record->company_id))
        {
            $record = $this->save($record, $request);
            if($record)
            {
                Alert::success(__('messages.save_success'))->autoclose(3000);
                return redirect()->route('roles.index');
            }
            Alert::error(__('messages.save_failed'))->autoclose(3000);
            return redirect()->back()->withInput();
        }
        Alert::error(__('messages.record_not_found'))->autoclose(3000);
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
        $record->company_id = \Auth::user()->company_id;
        $record->is_client = $request->is_client ? true : false;
        $record->role_name = $request->role_name;
        $record->role_access = $request->role_access;
        if(in_array('my-courses.index', explode(',', $request->role_access)))
            $record->is_learner = true;

        if($record->id)
            $record->updated_by = \Auth::id();
        else
            $record->created_by = \Auth::id();

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
        $record = Role::find(decrypt($id));
        if($record)
        {
            $record->deleted_by = \Auth::id();
            $record->save();
            if($record->delete())
            {
                $defaultRole = Role::where('role_name', 'Learner')->first();

                User::where("role_id", $record->id)->update(['role_id'=>$defaultRole->id]);

                Alert::success(__('messages.delete_success'))->autoclose(3000);
                return redirect()->route('roles.index');
            }
            Alert::error(__('messages.delete_failed'))->autoclose(3000);
            return redirect()->route('roles.index');
        }
        Alert::error(__('messages.record_not_found'))->autoclose(3000);
        return redirect()->route('roles.index');
    }
}
