<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiController extends Controller
{
    
    /**
     * Get Roles lists.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getRoles(Request $request)
    {
        $data = \App\Role::select('id', 'role_name', 'company_id', 'is_learner');

        if($request->has('company_id'))
        {
            $data->where('company_id', $request->company_id)
                    ->orWhereNull('company_id')
                    ->where('is_client', 1);
        }else{
            $data->where('company_id', \Auth::user()->company_id);
        }
        
        $data = $data->orderBy('company_id', 'desc')->orderBy('role_name')->get();
        $lists = [];
        foreach($data as $r)
        {
            $lists[] = [
                'id' => $r->id,
                'is_learner' => @$r->is_learner ? 1 : 0,
                'role_name' => $r->role_name .(!$r->company_id ? ' (system default)':'')
            ];
        }
        return json_encode($lists);
    }

    /**
     * Get Teams lists.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getTeams(Request $request)
    {
        $data = \App\Team::select('id', 'team_name');
        if($request->has('company_id'))
            $data->where('company_id', $request->company_id);
        $data = $data->orderBy('team_name')->get();
        return json_encode($data);
    }


    /**
     * Get Users by Teams lists.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getUsersByTeams(Request $request)
    {
        $data = \App\User::select('id', 'first_name', 'last_name');
        if($request->has('team_id'))
            $data->whereIn('team_id', $request->team_id);
        $data = $data->orderBy('first_name')->get();
        return json_encode($data);
    }


    /**
     * Get Modules by Courses lists.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getModulesByCourses(Request $request)
    {
        $data = \App\Module::select('id', 'title', 'type');
        if($request->has('course_id'))
            $data->whereIn('course_id', $request->course_id);
        $data = $data->orderBy('course_id')->orderBy('order_no')->get();
        return json_encode($data);
    }

}
