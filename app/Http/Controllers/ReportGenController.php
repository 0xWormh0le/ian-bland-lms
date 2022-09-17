<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Course;
use App\Module;
use App\Elearning;
use App\CourseMember;
use App\CourseUser;
use Alert, Auth, DB;

class ReportGenController extends Controller
{

    /**
     * Report Enrollment index.
     *
     * @return \Illuminate\Http\Response
     */
    public function enrollment()
    {
        $title = session('menuLabel')['reports'];
        $breadcrumbs = [
            '' => $title,
        ];
        
        $defaultColumns = ['learner', 'team', 'course', 'enrolled', 'percentage', 'completion'];
        $learners = \App\CourseUser::getEnrolledUsers(\Auth::user()->company_id);
        $teams = \App\Team::where('company_id', \Auth::user()->company_id)->orderBy('team_name')->get();

        return view('reports.enrollment.index', compact('title', 'breadcrumbs', 'defaultColumns', 'learners', 'teams'));
    }

    /**
     * Generate enrollment report
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function enrollmentGenerate(Request $request)
    {
        $columnTitle = [
            'learner' => 'Learner Name', 
            'team' => 'Team', 
            'course' => 'Course', 
            'enrolled' => 'Enrolled', 
            'percentage' => 'Completion %', 
            'completion' => 'Status'
        ];

        $company_id = Auth::user()->company_id;
        $columns = json_decode(stripslashes($request->columns));

        if($request->filter_learner)
            $filter_learner = $request->filter_learner;
        if($request->filter_team)
            $filter_team = $request->filter_team;
        if($request->filter_enrolledfrom)
            $filter_enrolledfrom = \Carbon\Carbon::createFromFormat('d/m/Y', $request->filter_enrolledfrom)->format('Y-m-d');
        if($request->filter_enrolledto)
            $filter_enrolledto = \Carbon\Carbon::createFromFormat('d/m/Y', $request->filter_enrolledto)->format('Y-m-d');
    

        $header = $select = [];
        foreach($columns as $c)
        {
            $header[$c] = $columnTitle[$c];
            if($c == 'learner')
                $select[] = DB::raw('CONCAT(first_name, " ", last_name) AS learner');
            elseif($c == 'team')
                $select[] = 'teams.team_name as team';
            elseif($c == 'course')
                $select[] = 'courses.title as course';
            elseif($c == 'enrolled')
                $select[] = 'course_users.enrol_date as enrolled';
            elseif($c == 'percentage')
                $select[] = 'course_users.completion_percentage as percentage';
            elseif($c == 'completion')
                $select[] = 'course_users.completed as completion';
        }

        $data = CourseUser::select($select)
                            ->leftJoin('users', 'users.id', '=', 'course_users.user_id')
                            ->leftJoin('teams', 'teams.id', '=', 'users.team_id')
                            ->leftJoin('courses', 'courses.id', '=', 'course_users.course_id');
        if($company_id)
            $data->where('users.company_id', $company_id);
        if(isset($filter_learner))
            $data->whereIn('users.id', $filter_learner);
        if(isset($filter_team))
            $data->whereIn('users.team_id', $filter_team);
        if(isset($filter_enrolledfrom))
            $data->where('course_users.enrol_date', '>=', $filter_enrolledfrom);
        if(isset($filter_enrolledto))
            $data->where('course_users.enrol_date', '<=', $filter_enrolledto);


        $records = $data->orderBy('learner')
                            ->get()
                            ->toArray();
                        
        
        $chartData = CourseUser::select(
                                DB::raw('COUNT(completed) as qty'), 'completed'
                            )
                            ->leftJoin('users', 'users.id', '=', 'course_users.user_id');

        if($company_id)
            $chartData->where('users.company_id', $company_id);
        if(isset($filter_learner))
            $chartData->whereIn('users.id', $filter_learner);
        if(isset($filter_team))
            $chartData->whereIn('users.team_id', $filter_team);
        if(isset($filter_enrolledfrom))
            $chartData->where('course_users.enrol_date', '>=', $filter_enrolledfrom);
        if(isset($filter_enrolledto))
            $chartData->where('course_users.enrol_date', '<=', $filter_enrolledto);

        $chartData = $chartData->groupBy('completed')
                            ->orderBy('completed', 'DESC')
                            ->get()
                            ->toArray();
        
        $data = $labels = [];
        if(count($chartData) == 1)
        {
            if($chartData[0]['completed'] == 0)
                $data = [0, $chartData[0]['qty']];
            else
                $data = [$chartData[0]['qty'], 0];
        }
        else{
            foreach($chartData as $c)
            {
                $data[] = $c['qty'];
            }
        }
        $labels = ['Complete', 'Incomplete'];
        $backgroundColor = ['#36A2EB', '#FF6384'];

        $chart = [
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => $backgroundColor,
                ],
            ],
            'labels' => $labels
        ];
        $chart = json_encode($chart);
        return view('reports.enrollment.results', compact('header', 'records', 'chart'));
    }

    
}
