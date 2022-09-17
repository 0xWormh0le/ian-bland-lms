<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Course;
use App\Module;
use App\Elearning;
use App\CourseMember;
use App\CourseUser;
use App\CourseResult;
use App\CourseCategory;
use App\User;
use App\Team;
use Alert, Auth, DB;
use App\Role;
use Carbon\Carbon;
use Yajra\Datatables\Datatables;
//use Illuminate\Http\Response;
use Illuminate\Support\Facades\Response;
use App\Http\Repositories\LearnerReport;

class ReportController extends Controller
{

    /**
     * Chart Report index.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = trans("controllers.reports");
        $breadcrumbs = [
            '' => $title,
        ];

        $chartTypes = [
            'Column',
            'Column3d',
            // 'Column Stacked',
            'Bar',
            // 'Bar Stacked',
            // 'Line',
            'Pie',
            // 'Area'
        ];
        $dataValues = [
            // 'number_of_courses' => 'Number of Courses',
            // 'number_of_participants' => 'Number of Participants',
            // 'percentage' => 'Completion Percentage',
            'avg_score' => 'Score Average'
        ];
        $dataOptions = [
            // 'course' => 'Course',
            'learner' => 'Learner',
            'team' => 'Team',
            // 'enrolled' => 'Enrolled Date',
            // 'completion' => 'Completion Status',
        ];

        $defaultColumns = ['learner', 'module', 'score', 'completion'];
        $courses = \App\CourseCompany::getCourseByCompany(\Auth::user()->company_id);
        $teams = \App\Team::where('company_id', \Auth::user()->company_id)->orderBy('team_name')->get();
        $scores = [ 'all' => 'All Scores',
                    'average' => 'Average Score',
                    'between' => 'Score Between',
                    'lower_than' => 'Lower than',
                    'higher_than' => 'Higher than'
        ];


        return view('reports.index',
          compact(
            'title',
            'breadcrumbs',
            'chartTypes',
            'dataOptions',
            'dataValues',
            'defaultColumns',
            'courses',
            'teams',
            'scores'
          )
        );
    }

    /**
     * Generate chart report
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generate(Request $request)
    {

        // dd($request->all());

        $company_id = Auth::user()->company_id;
        $company = \App\Company::find($company_id);

        $filterCourse = @$request->filter_course ?: [];
        $filterTeam = @$request->filter_team ?: [];
        $filterLearner = @$request->filter_learner ?: [];
        $filterScore = @$request->filter_score ?: [];

        $scoreBetween = $request->score_between;
        $scoreBetweenTo = $request->score_between_to;
        $scoreLowerThan = $request->score_lower_than;
        $scoreHigherThan = $request->score_higher_than;

        $filterEnrolledDate = @$request->enrolled_date ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->enrolled_date)->format('Y-m-d') : null;
        $filterEnrolledBetween = @$request->enrolled_between ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->enrolled_between)->format('Y-m-d') : null;
        $filterEnrolledBetweenTo = @$request->enrolled_between_to ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->enrolled_between_to)->format('Y-m-d') : null;

        $filterModules = $request->modules;
        $filterStatus = $request->status;
        $filterCompletion = $request->completion;
        $filterCompletionRange = $request->completion_range;
        $filterCompletionRangeTo = $request->completion_range_to;
        $filterCompletionStatus = $request->completion_status;
        $filterSuccessStatus = $request->success_status;

        $chartActive = $request->chart_active;
        $tableActive = $request->table_active;

        // Generate Chart if ON
        if($chartActive == "on")
        {
            $chartType = $request->chart_type;
            $chartTitle = $request->chart_title;
            $chartSubtitle = $request->chart_subtitle;
            $chartValues = $request->chart_values ?: 'avg_score';
            $chartSeries = $request->chart_series ?: 'learner';
            $chartCategories = $request->chart_categories;

            if($chartSeries == 'courses'){
                $series = \App\Course::select('id', 'title')
                                ->whereIn('id', $filterCourse)
                                ->pluck('title', 'id')
                                ->toArray();
            }
            elseif($chartSeries == 'team')
            {
                $series = \App\Team::select('id', 'team_name')
                                    ->whereCompanyId($company_id)
                                    ->whereIn('id', $filterTeam)
                                    ->pluck('team_name', 'id')
                                    ->toArray();
            }
            elseif($chartSeries == 'learner')
            {
                $users = \App\User::select('id', 'first_name', 'last_name')
                                ->whereCompanyId($company_id)
                                ->whereIn('team_id', $filterTeam)
                                ->whereIn('id', $filterLearner)
                                ->orderBy('team_id')
                                ->orderBy('first_name')
                                ->get();
                $series = [];
                foreach($users as $r)
                {
                    $series[$r->id] = $r->first_name.' '.$r->last_name;
                }
            }


            if(in_array('all', $filterScore)){
                if($chartSeries == 'learner')
                {
                    $users = \App\User::select('id', 'first_name', 'last_name')
                                    ->whereCompanyId($company_id)
                                    ->whereIn('team_id', $filterTeam)
                                    ->whereIn('id', $filterLearner)
                                    ->orderBy('team_id')
                                    ->orderBy('first_name')
                                    ->get();
                    $categories = [];
                    foreach($users as $r)
                    {
                        $categories[$r->id] = $r->first_name.' '.$r->last_name;
                    }
                }
                elseif($chartSeries == 'team')
                {
                    $categories =  \App\Team::select('id', 'team_name')
                                    ->whereCompanyId($company_id)
                                    ->whereIn('id', $filterTeam)
                                    ->pluck('team_name', 'id')
                                    ->toArray();
                }

                $series = \App\Module::select('id', 'title')
                                ->where('course_id', $filterCourse);
                if(count($filterModules)>0)
                    $series->whereIn('id', $filterModules);

                $series = $series->pluck('title', 'id')
                                ->toArray();
            }
            else{
                $categories = ['Data'];
            }
            // dd($categories, $series);

            $dataset = [];

            foreach($series as $seriesId => $seriesLabel)
            {
                $data = [];
                foreach($categories as $categoriesId => $categoriesLabel)
                {

                    $result = CourseResult::leftJoin('course_users', 'course_users.id', '=', 'course_results.courseuser_id');

                    if(in_array('all', $filterScore))
                    {
                        if($chartSeries == 'learner')
                            $result->select('course_results.score as value');
                        elseif($chartSeries == 'team')
                            $result->select(\DB::raw("CAST(AVG(course_results.score) as decimal(10,2)) as value"));
                    }else{
                        $result->select(\DB::raw("CAST(AVG(course_results.score) as decimal(10,2)) as value"))
                                ->whereNotNull('course_results.score');
                    }

                    $result->leftJoin('users', 'users.id','=','course_users.user_id')
                            ->leftJoin('courses', 'courses.id', '=', 'course_users.course_id')
                            ->where('users.is_suspended', false)
                            ->whereNull('users.deleted_at')
                            ->whereNull('courses.deleted_at')
                            ->where('courses.id', $filterCourse);

                    if(in_array('all', $filterScore))
                    {
                        if($chartSeries == 'team')
                            $result->where('users.team_id', $categoriesId);
                        elseif($chartSeries == 'learner')
                            $result->where('users.id', $categoriesId);

                        $result->where('module_id', $seriesId);
                    }
                    else{
                        if($chartSeries == 'team')
                            $result->where('users.team_id', $seriesId);
                        elseif($chartSeries == 'learner')
                            $result->where('users.id', $seriesId);
                    }

                    if($company_id)
                        $result->where('users.company_id', $company_id);
                    if(count($filterLearner) > 0)
                        $result->whereIn('users.id', $filterLearner);
                    if(count($filterTeam) > 0)
                        $result->whereIn('users.team_id', $filterTeam);
                    if($filterEnrolledDate)
                        $result->where('course_users.enrol_date', '=', $filterEnrolledDate);
                    if($filterEnrolledBetween)
                        $result->where('course_users.enrol_date', '>=', $filterEnrolledBetween);
                    if($filterEnrolledBetweenTo)
                        $result->where('course_users.enrol_date', '<=', $filterEnrolledBetweenTo);

                    if($scoreBetween)
                        $result->where('course_results.score', '>=', $scoreBetween);
                    if($scoreBetweenTo)
                        $result->where('course_results.score', '<=', $scoreBetweenTo);
                    if($scoreLowerThan)
                        $result->where('course_results.score', '<', $scoreLowerThan);
                    if($scoreHigherThan)
                        $result->where('course_results.score', '>', $scoreHigherThan);


                    $result = $result->first();

                    if(in_array('all', $filterScore))
                    {
                        $data[] = @(double)$result->value ?: 0;
                    }else{
                        if((double)$result->value <> 0)
                        {
                            $dataset[] = [
                                'name' => $seriesLabel,
                                'y' => (double)$result->value
                            ];
                        }
                    }
                }
                if(in_array('all', $filterScore))
                {
                    $dataset[] = [
                        'name' => $seriesLabel,
                        'data' => $data
                    ];
                }
            }
            // dd($dataset, $categories);

            if(in_array('all', $filterScore))
            {
                $chartResultSeries = $dataset;
                $withCategories = true;
            }
            else{
                $chartResultSeries = [[
                    'showInLegend' => false,
                    'name' => ucwords($chartSeries),
                    'colorByPoint' => true,
                    'data' => $dataset,
                ]];
                $withCategories = false;
            }
            $chartResultSeries = json_encode($chartResultSeries);
            if(count($categories) > 1)
                $categories = implode("', '", array_values($categories));
            else
                $categories = ucwords($chartCategories);
            if(strpos($chartType, '3d') === false)
            {
                $chart3d = false;
            }else {
                $chartType = str_replace('3d', '', $chartType);
                $chart3d = true;
            }
        }
        // dd($chartResultSeries, $categories);

        if($tableActive == "on")
        {
            $columnTitle = [
                'learner' => 'Learner Name',
                'team' => 'Team',
                'course' => 'Course',
                'module' => 'Module',
                'enrolled' => 'Enrolled',
                'percentage' => 'Completion %',
                'completion' => 'Status',
                'score' => 'Score'
            ];
            $columns = json_decode(stripslashes($request->table_columns));
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
                elseif($c == 'module')
                    $select[] = 'modules.title as module';
                elseif($c == 'enrolled')
                    $select[] = 'course_users.enrol_date as enrolled';
                elseif($c == 'percentage')
                    $select[] = 'course_users.completion_percentage as percentage';
                elseif($c == 'completion')
                    $select[] = 'course_results.complete_status as completion';
                elseif($c == 'score')
                    $select[] = 'course_results.score as score';

            }

            $data = CourseResult::select($select)
                                ->leftJoin('course_users', 'course_users.id', '=', 'course_results.courseuser_id')
                                ->leftJoin('users', 'users.id', '=', 'course_users.user_id')
                                ->leftJoin('teams', 'teams.id', '=', 'users.team_id')
                                ->leftJoin('courses', 'courses.id', '=', 'course_users.course_id')
                                ->leftJoin('modules', 'modules.id', '=', 'course_results.module_id')
                                ->whereNull('modules.deleted_at')
                                ->whereNull('users.deleted_at')
                                ->whereNull('teams.deleted_at')
                                ->whereNull('course_users.deleted_at')
                                ->whereNull('courses.deleted_at');
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
        }



        return view('reports.chart.results',
          compact(
            'chartActive',
            'chartType',
            'chart3d',
            'chartTitle',
            'chartSubtitle',
            'company',
            'withCategories',
            'categories',
            'chartResultSeries',
            'chartCategories',
            'tableActive',
            'header',
            'records'
          )
        );
    }


    public function suReport(Request $request)
    {
      $filter = $request->filter;
      $filter_team         = $request->input('team', 'none');
      $filter_department   = $request->input('department', 'none');
      $filter_overdue      = $request->input('overdue', 'none');
      $filter_department   = strtolower($filter_department);

      if ($filter != "archive") {
        $filter = "active";
      }

      $withTrash = $filter == "archive";
      $sysAdmin = auth()->user()->isSysAdmin();
      $learnerIds = Role::where("is_learner", 1)->pluck('id');

      if ($sysAdmin) {
        $team = Team::get();
        $department = User::select('department')
          ->whereNotNull('department')
          ->distinct('department')
          ->pluck('department');
      } else {
        $companyUsers = User::when($withTrash, function ($query) {
          return $query->withTrashed();
        })->where('company_id', auth()->user()->company_id)
          ->whereIn('role_id', $learnerIds)
          ->get();

        $team = [];
        $department = [];

        foreach ($companyUsers as $cu) {
          $team[] = $cu->team_id;
          
          if ($cu->department) {
            $department[] = strtolower($cu->department);
          }
        }

        $team = Team::whereIn('id', collect($team)->unique()->all())->get();
        $department = collect($department)->unique()->all();
      }

      $user_filter = [
        'team' => $filter_team,
        'department' => $filter_department,
        'overdue' => $filter_overdue
      ];

      $users = $this->_reportUserData($user_filter, $withTrash);
      $learners = 0;
      $courseAssignment = 0;
      $completedCourse = 0;
      $overdue = 0;

      foreach ($users as $user) {
        $learners++;
        $courseAssignment += $user['course_assigned'];
        $completedCourse += $user['course_completed'];
        $overdue += $user['overdue'];
      }

      $title = trans("controllers.user_reports");
      $breadcrumbs = [
        '' => $title,
      ];
      
      return view('reports.super-admin',
        compact(
          'title',
          'breadcrumbs',
          'learners',
          'courseAssignment',
          'completedCourse',
          'overdue',
          'filter',
          'users',

          'team',
          'department',
          'filter_team',
          'filter_department',
          'filter_overdue'
        )
      );
    }

    public function _reportUserData($filter, $withTrash)
    {
      $user = auth()->user();
      
      $query =
        'SELECT'.
        '  `users`.`first_name` AS first_name, '.
        '  `users`.`last_name` AS last_name, '.
        '  `users`.`email` AS email, '.
        '  `users`.`department` AS department, '.
        '  `users`.`last_login_at` AS last_login_at, '.
        '  `roles`.`role_name` AS role_name, '.
        '  `teams`.`team_name` AS team, '.
        '  `course_users`.`id` AS id, '.
        '  `course_users`.`user_id` AS user_id, '.
        '  `course_users`.`completed` AS completed, '.
        '  COUNT(`course_users`.`user_id`) AS course_assigned, '.

        '  SUBSTRING(deadline, 1, LOCATE(" ", deadline) - 1) AS deadline_value, '.
        '  SUBSTRING(deadline, LOCATE(" ", deadline) + 1) AS deadline_unit, '.
        '  IFNULL(start_date, enrol_date) AS start_base_date '.
        'FROM `course_users` '.
        'JOIN `users` ON `users`.`id` = `course_users`.`user_id` '.
        'JOIN `courses` ON `courses`.`id` = `course_users`.`course_id` '.
        'LEFT JOIN `teams` ON `users`.`team_id` = `teams`.`id` '.
        'LEFT JOIN `roles` ON `roles`.`id` = `users`.`role_id` '.
        'LEFT JOIN `course_companies` ON '.
        '  `course_companies`.`course_id` = `course_users`.`course_id` '.
        '  AND `course_companies`.`company_id` = `users`.`company_id` '.

        'WHERE TRUE '.
        ' AND `course_companies`.`deleted_at` IS NULL ';

      if ($user->isClientAdmin()) {
        $query .= ' AND `users`.`company_id` = ' . $user->company_id . ' ';
      }

      if (!$withTrash) {
        $query .= ' AND `users`.`deleted_at` IS NULL '.
          'AND `course_users`.`deleted_at` IS NULL '.
          'AND `courses`.`deleted_at` IS NULL '.
          'AND `users`.`active` = 1 AND `users`.`is_suspended` = 0 ';
      }

      if ($filter['overdue'] == 'yes') {
        $query .= 'AND `course_users`.`completed` = 0 ';
      }

      if ($filter['team'] != 'none') {
        $query .= sprintf('AND `users`.`team_id` = "%s" ', $filter['team']);
      }

      if ($filter['department'] != 'none') {
        $query .= sprintf('AND `users`.`department` = "%s" ', $filter['department']);
      }

      $query .= 'GROUP BY `course_users`.`user_id`';

      $overdue_date =
        'CASE WHEN deadline_unit="day" THEN DATE_ADD(start_base_date, INTERVAL deadline_value DAY) '.
        '   WHEN deadline_unit="week" THEN DATE_ADD(start_base_date, INTERVAL deadline_value WEEK) '.
        '   WHEN deadline_unit="month" THEN DATE_ADD(start_base_date, INTERVAL deadline_value MONTH) '.
        '   ELSE DATE_ADD(start_base_date, INTERVAL deadline_value YEAR) END';
      
      $query =
        'SELECT '.
        '  t.first_name, '.
        '  t.last_name, '.
        '  t.email, '.
        '  t.department, '.
        '  t.team, '.
        '  t.user_id, '.
        '  t.last_login_at, '.
        '  t.role_name, '.
        '  COUNT(t1.id) AS course_completed, '.
        '  t.course_assigned, '.
        '  IF(`t`.`completed` = "0" AND ' . $overdue_date . ' < NOW(), TRUE, FALSE) AS overdue '.
        'FROM (' . $query . ') AS t '.
        'LEFT JOIN `course_users` AS t1 ON `t`.`user_id` = `t1`.`user_id` AND `t1`.`completed` = 1 '.
        'WHERE TRUE ';

      if ($filter['overdue'] == 'yes') {
        $query .= 'AND (`t`.`completed` = "0" AND ' . $overdue_date . ' < NOW()) = TRUE ';
      } else if ($filter['overdue'] == 'no') {
        $query .= 'AND (`t`.`completed` = "0" AND ' . $overdue_date . ' < NOW()) = FALSE ';
      }

      if (!$withTrash) {
        $query .= 'AND `t1`.`deleted_at` IS NULL ';
      }

      $query .= 'GROUP BY `t`.`id`';

      $users = \DB::select($query);
      $result = [];
      
      foreach ($users as $u) {
        $result[] = [
          'id' => $u->user_id,
          'user' => $u->first_name . ' ' . $u->last_name,
          'last_name' => $u->last_name,
          'email' => $u->email,
          'department' => $u->department,
          'last_login_at' => $u->last_login_at,
          'role_name' => $u->role_name,
          'team' => $u->team,
          'course_assigned' => $u->course_assigned,
          'course_completed' => $u->course_completed,
          'overdue' => $u->overdue
        ];
      }
      return $result;
    }

    public function reportUserData(Request $request)
    {
      $filter = $request->filter;
      $filter_team         = $request->input('team', 'none');
      $filter_department   = $request->input('department', 'none');
      $filter_overdue      = $request->input('overdue', 'none');      

      if ($filter != "archive") {
        $filter = "active";
      }
      
      $users = $this->_reportUserData([
        'team' => $filter_team,
        'department' => $filter_department,
        'overdue' => $filter_overdue
      ], $filter == "archive");

      if ($request->csv) {
        $path = public_path().'/report';
        $filename = "user_report" . str_random(15) . ".csv";
        
        if (!file_exists($path)) {
          mkdir($path, 0755, true);
        }

        $handle = fopen($path .'/'. $filename, 'w+');
        fputcsv($handle, array(
          trans("modules.user"),
          trans("modules.email"),
          trans("modules.team"),
          trans("modules.department"),
          trans("modules.enrolled"),
          trans("modules.completed"),
          trans("modules.overdue")
        ));
        
        foreach ($users as $user) {
          fputcsv($handle, [
            $user['user'],
            $user['email'],
            $user['team'],
            $user['department'],
            $user['course_assigned'],
            $user['course_completed'],
            $user['overdue']
          ]);
        }

        fclose($handle);

        $headers = array(
            'Content-Type' => 'text/csv',
        );

        return response()
                ->download($path . '/' . $filename, 'user_report.csv')
                ->deleteFileAfterSend(true);
      }

      return Datatables::of($users)
        ->addColumn('action', function ($user) use ($filter) {
          return '<a href="' .
            route('reports.learneradmin.index', [
              encrypt($user['id']),
              'none',
              'none',
              $filter
            ]) .
            '" class="btn btn-sm" title="View User Details">' .
            '<i class="fa fa-file-o" aria-hidden="true"></i></a>';
        })->rawColumns(['action'])
          ->make(true);
    }


    public function learnerReport(Request $request, LearnerReport $learnerReport)
    {
        $id = decrypt($request->id);
        $filter = $request->filter;
        $export = $request->export;
        $type = $request->type;

        $res = $learnerReport->handle(compact('id', 'filter', 'export', 'type'));
        
        if ($export == 'csv') {
          return $res;
        }
        
        $data = User::find($id);
        $title = trans("controllers.user_reports");
        $breadcrumbs = [
            route('reports.superadmin.index', ['filter' => $filter]) => trans("controllers.user_reports"),
            '' => $data->first_name . ' ' . $data->last_name,
        ];

        return view(
          'users.details',
          array_merge(compact('title', 'breadcrumbs', 'data'), $res)
        );
    }

    public function learnerStatisticsData(Request $request, LearnerReport $learnerReport)
    {
      $filter = $request->filter;
      $userId = decrypt($request->id) ;

      if($filter == "archive")
      {
        $courseAssignment = CourseUser::withTrashed()->where("user_id", $userId)->pluck("id");
      }
      else {
        $courseAssignment = CourseUser::where("user_id", $userId)->pluck("id");
      }

      $from =  Carbon::now()->format("Y-m-d");
      $to =  Carbon::now()->format("Y-m-d");

      switch($request->type)
      {
        case "today":
             $from =  Carbon::now()->format("Y-m-d");
        break;
        case "yesterday":
             $from =  Carbon::now()->yesterday()->format("Y-m-d");
             $to =  Carbon::now()->yesterday()->format("Y-m-d");
        break;
        case "week":
             $from = Carbon::now()->subWeek()->format("Y-m-d");
        break;
        case "month":
             $from = Carbon::now()->subMonth()->format("Y-m-d");
        break;
        case "year":
             $from = Carbon::now()->subYear()->format("Y-m-d");
        break;
        case "period":
              $date = explode("#", $request->date_range);
              $from = Carbon::createFromFormat("d/m/Y", $date[0])->format("Y-m-d") ;
              $to =  Carbon::createFromFormat("d/m/Y", $date[1])->format("Y-m-d") ;
        break;

      }

      $loginCount = array();
      $courseCompletionCount = array();
      $yLabel = array();

      $i = 0 ;

      while($i==0)
      {
        if(strtotime($from) <= strtotime($to))
        {


          if($request->type == "month" || $request->type == "year" || $request->type == "period")
          {
            if($request->type == "year")
            {
              $from_ext = Carbon::parse($from)->addMonth()->format("Y-m-d");
            }
            elseif($request->type == "period")
            {
              $days = 1;
              if($request->day_range > 0)
                  $days = $request->day_range;

              $from_ext = Carbon::parse($from)->addDays($days)->format("Y-m-d");
            }
            else
             $from_ext = Carbon::parse($from)->addDays('3')->format("Y-m-d");

             if($filter == "archive")
             {

               $loginCount[] = User::withTrashed()
                                  ->where('id', $userId)
                                  ->where('last_login_at','>=',$from)
                                  ->where('last_login_at','<=',$from_ext)
                                  ->count();


               $enrollCount[] = \App\CourseUser::withTrashed()->whereIn('id',$courseAssignment)
                                       ->where('course_users.enrol_date', '>', $from)
                                       ->where('course_users.enrol_date', '<', $from_ext)
                                       ->count();
              /* $courseCompletionCount[] = CourseResult::withTrashed()->whereIn('courseuser_id',$courseAssignment)-
                                          >where('completion_date', '>=', $from)
                                          ->where('completion_date', '<=', $from_ext)
                                          ->where("score", 100)
                                          ->count();*/
               $period = $this->generateDateRange(Carbon::parse($from), Carbon::parse($from_ext));


                $courseCompletion = 0 ;
               for($c=0;$c<count($courseAssignment);$c++)
               {
                 for($p=0;$p<count($period);$p++)
                 {
                   $courseUser = CourseUser::withTrashed()->where("id", $courseAssignment[$c])->first();
                   $result =  $LearnerReport->courseCompletionRulesResult(
                      $courseUser->course_id,
                      $userId,
                      Carbon::parse($period[$p])->format("d"),
                      Carbon::parse($period[$p])->format("m"),
                      Carbon::parse($period[$p])->format("Y"),
                      $filter
                    );

                   if($result['complete'] == 1)
                     $courseCompletion++;
                 }
               }
               $courseCompletionCount[]  = $courseCompletion;
             }
             else {
               $loginCount[] = User::where('id', $userId)
                                  ->where('last_login_at','>=',$from)
                                  ->where('last_login_at','<=',$from_ext)->count();


               $enrollCount[] = \App\CourseUser::whereIn('id',$courseAssignment)
                                       ->where('course_users.enrol_date', '>', $from)
                                       ->where('course_users.enrol_date', '<', $from_ext)
                                       ->count();
            /*  $courseCompletionCount[] = CourseResult::whereIn('courseuser_id',$courseAssignment)-
                                         >where('completion_date', '>=', $from)
                                         ->where('completion_date', '<=', $from_ext)
                                         ->where("score", 100)
                                         ->count();*/
                $period = $this->generateDateRange(Carbon::parse($from), Carbon::parse($from_ext));

               $courseCompletion = 0 ;

              for($c=0;$c<count($courseAssignment);$c++)
              {
                for($p=0;$p<count($period);$p++)
                {
                  $courseUser = CourseUser::where("id", $courseAssignment[$c])->first();
                  $result =  $LearnerReport->courseCompletionRulesResult(
                    $courseUser->course_id,
                    $userId,
                    Carbon::parse($period[$p])->format("d"),
                    Carbon::parse($period[$p])->format("m"),
                    Carbon::parse($period[$p])->format("Y"),
                    $filter
                  );

                  if($result['complete'] == 1)
                    $courseCompletion++;
                }
              }

              $courseCompletionCount[]  = $courseCompletion;

             }


            $period = $this->generateDateRange(Carbon::parse($from), Carbon::parse($from_ext));

            $yLabel[] = Carbon::parse($from)->format("d-m-Y");

            $from = $from_ext;

          }
          else
          {

            if($filter == "archive")
            {
            $enrollCount[] = \App\CourseUser::withTrashed()->whereIn('id',$courseAssignment)
                                    ->where(\DB::raw('day(course_users.enrol_date)'), Carbon::parse($from)->format("d"))
                                    ->where(\DB::raw('month(course_users.enrol_date)'), Carbon::parse($from)->format("m"))
                                    ->where(\DB::raw('year(course_users.enrol_date)'), Carbon::parse($from)->format("Y"))
                                    ->count();
            $loginCount[] = User::withTrashed()->where('id', $userId)->where('last_login_at', $from)->count();
          //  $courseCompletionCount[] = CourseResult::withTrashed()->whereIn('courseuser_id',$courseAssignment)->where('completion_date', $from)->where("score", 100)->count();

           $courseCompletion = 0 ;
          for($c=0;$c<count($courseAssignment);$c++)
          {
            $courseUser = CourseUser::withTrashed()->where("id", $courseAssignment[$c])->first();
            $result =  $LearnerReport->courseCompletionRulesResult(
              $courseUser->course_id,
              $userId,
              Carbon::parse($from)->format("d"),
              Carbon::parse($from)->format("m"),
              Carbon::parse($from)->format("Y"),
              $filter
            );

            if($result['complete'] == 1)
              $courseCompletion++;

          }

          $courseCompletionCount[] = $courseCompletion;

            }
            else {
              $enrollCount[] = \App\CourseUser::whereIn('id',$courseAssignment)
                                      ->where(\DB::raw('day(course_users.enrol_date)'), Carbon::parse($from)->format("d"))
                                      ->where(\DB::raw('month(course_users.enrol_date)'), Carbon::parse($from)->format("m"))
                                      ->where(\DB::raw('year(course_users.enrol_date)'), Carbon::parse($from)->format("Y"))
                                      ->count();
              $loginCount[] = User::where('id', $userId)->where('last_login_at', $from)->count();
            //  $courseCompletionCount[] = CourseResult::whereIn('courseuser_id',$courseAssignment)->where('completion_date', $from)->where("score", 100)->count();

              $courseCompletion = 0 ;
             for($c=0;$c<count($courseAssignment);$c++)
             {
               $courseUser = CourseUser::where("id", $courseAssignment[$c])->first();
               $result =  $LearnerReport->courseCompletionRulesResult(
                 $courseUser->course_id,
                 $userId,
                 Carbon::parse($from)->format("d"),
                 Carbon::parse($from)->format("m"),
                 Carbon::parse($from)->format("Y"),
                 $filter
              );

               if($result['complete'] == 1)
                 $courseCompletion++;

             }

             $courseCompletionCount[] = $courseCompletion;
            }



            $yLabel[] = Carbon::parse($from)->format("d-m-Y");
            $from = Carbon::parse($from)->addDay()->format("Y-m-d");
          }

        }
        else {
          $i = 1;
          break;
        }
      }


      $yLabel= implode(",", $yLabel);
      $loginCount= implode(",", $loginCount);
      $enrollCount= implode(",", $enrollCount);
      $courseCompletionCount= implode(",", $courseCompletionCount);

      $result = array("yLabel"=> $yLabel,
             "loginCount"=>$loginCount,
             "enrollCount"=>$enrollCount,
             "courseCompletionCount" => $courseCompletionCount);

      return response()->json($result);

    }

    public function logs(Request $request)
    {
      $companies = \App\Company::getLists();
      $title = @session('menuLabel')['reports.login-log'];
      return view('reports.log', compact('companies', 'title'));
    }

    public function learnerCourses(Request $request)
      {
        $filter = $request->filter;
        $encUserId = $request->id;
        $userId = decrypt($request->id) ;
        if($filter == "archive"){
          $user = User::withTrashed()->where("id", $userId)->first();
        }
        else {
          $user = User::find($userId);
        }

        $title = trans("controllers.learner_course");
        $breadcrumbs = [
            route('reports.superadmin.index',['filter' => $filter]) => trans("controllers.reports"),
            '' => $title,
        ];
        return view(
          'reports.learner-course',
          compact(
            'title',
            'breadcrumbs',
            'user',
            'encUserId',
            'filter'
          )
        );

      }

    public function learnerCoursesData(Request $request)
      {
        $userId = decrypt($request->id) ;
        $filter = $request->filter;


        if($filter == "archive"){
          $user = User::withTrashed()->where("id", $userId);
          $userCourse = CourseUser::withTrashed()->select(
            'course_users.enrol_date',
            'course_users.start_date',
            'course_results.complete_status',
            'course_results.satisfied_status',
            'course_results.completion_date',
            'course_results.score',
            'course_results.total_time',
            'courses.title',
            'courses.duration',
            DB::raw('modules.title as mod_title')
          )
            ->leftJoin('course_results','course_results.courseuser_id', 'course_users.id')
            ->join('courses', 'courses.id', 'course_users.course_id')
            ->leftJoin('modules', 'modules.id','course_results.module_id')
            ->where('course_users.user_id',$userId)->distinct()->get();
        }
        else {
          $user = User::find($userId);
          $userCourse = CourseUser::select(
            'course_users.enrol_date',
            'course_users.start_date',
            'course_results.complete_status',
            'course_results.satisfied_status',
            'course_results.completion_date',
            'course_results.score',
            'course_results.total_time',
            'courses.title',
            'courses.duration',
            DB::raw('modules.title as mod_title')
          )
            ->leftJoin('course_results','course_results.courseuser_id', 'course_users.id')
            ->join('courses', 'courses.id', 'course_users.course_id')
            ->leftJoin('modules', 'modules.id','course_results.module_id')
            ->where('course_users.user_id',$userId)->distinct()->get();
        }

          $filterResult = array();
          for($uc=0; $uc< count($userCourse); $uc++)
          {
            if($userCourse[$uc]->enrol_date != "")
            $userCourse[$uc]->enrol_date = Carbon::parse($userCourse[$uc]->enrol_date)->format("d-m-Y");
            if($userCourse[$uc]->completion_date !="")
            $userCourse[$uc]->completion_date = Carbon::parse($userCourse[$uc]->completion_date)->format("d-m-Y");

          }
             return Datatables::of($userCourse)
                            ->make(true);
      }

    protected function _courseQuery($param)
    {
      $overdue = $param['overdue'] ?? 'none';
      $completed = $param['completed'] ?? false;
      $withTrash = $param['withTrash'] ?? false;
      $percourse = $param['percourse'] ?? false;

      $user = auth()->user();
      
      if ($overdue == 'none') {
        $query = 'SELECT COUNT(*) AS count ';
        if ($percourse) {
          $query .= ', `course_users`.`course_id` ';
        }
        $query .= 'FROM `course_users` '.
          'JOIN `users` ON `users`.`id` = `course_users`.`user_id` '.
          'JOIN `courses` ON `courses`.`id` = `course_users`.`course_id` ';
      } else {
        $query =
          'SELECT'.
          '  SUBSTRING(deadline, 1, LOCATE(" ", deadline) - 1) AS deadline_value, '.
          '  SUBSTRING(deadline, LOCATE(" ", deadline) + 1) AS deadline_unit, '.
          '  IFNULL(start_date, enrol_date) AS start_base_date, '.
          '  course_users.completed AS completed, '.
          '  course_users.course_id AS course_id '.
          'FROM `course_users` '.
          'JOIN `users` ON `users`.`id` = `course_users`.`user_id` '.
          'JOIN `courses` ON `courses`.`id` = `course_users`.`course_id` '.
          'LEFT JOIN `course_companies` ON '.
          '  `course_companies`.`course_id` = `course_users`.`course_id` '.
          '  AND `course_companies`.`company_id` = `users`.`company_id` ';
      }
      
      $query .= 'WHERE `users`.`active` = 1 AND `users`.`is_suspended` = 0 ';

      if ($user->isClientAdmin()) {
        $query .= 'AND users.company_id = ' . $user->company_id . ' ';
      }

      if (!$withTrash) {
        $query .= ' AND `users`.`deleted_at` IS NULL '.
          'AND `course_users`.`deleted_at` IS NULL '.
          'AND `courses`.`deleted_at` IS NULL ';
      }

      if ($overdue != 'none') {
        $query .= ' AND `course_companies`.`deleted_at` IS NULL ';
        if ($overdue == 'yes') {
          $query .= ' AND `course_users`.`completed` = 0 ';
        }
      }

      if ($completed) {
        $query .= ' AND `course_users`.`completed` =  1 ';
      }

      if ($overdue != 'none') {
        $overdue_date =
          'CASE WHEN deadline_unit="day" THEN DATE_ADD(start_base_date, INTERVAL deadline_value DAY) '.
          '   WHEN deadline_unit="week" THEN DATE_ADD(start_base_date, INTERVAL deadline_value WEEK) '.
          '   WHEN deadline_unit="month" THEN DATE_ADD(start_base_date, INTERVAL deadline_value MONTH) '.
          '   ELSE DATE_ADD(start_base_date, INTERVAL deadline_value YEAR) END';

        $q = 'SELECT COUNT(*) AS count ';
        if ($percourse) {
          $q .= ', course_id ';
        }
        $query = $q.'FROM ('.$query.') AS t ';

        if ($overdue == 'yes') {
          $query .= 'WHERE ' . $overdue_date . ' < NOW() ';
        } else {
          $query .= 'WHERE completed = 1 OR ' . $overdue_date . ' >= NOW() ';
        }
      }

      if ($percourse) {
        $query .= 'GROUP BY course_id';
      }

      return $query;
    }
    
    public function overdueEmail(Request $request)
    {
      $data = [
        'course_id' => $request->input('course', 0),
        'company_id' => auth()->user()->company_id
      ];
      dispatch(new \App\Jobs\SendEmail($data, 'CourseOverdue'));
    }

    public function courseReport(Request $request)
    {
        $filter = $request->filter;
        $filter_overdue = $request->input('overdue', 'none');
        $withTrash = $filter == "archive";
        $user = auth()->user();
        
        if ($filter != "archive") {
          $filter = "active";
        }

        if ($user->isSysAdmin()) {
          $courses = Course::when($withTrash, function ($query) {
            return $query->withTrashed();
          })->count();
        } else {
          $courses = \App\CourseCompany::leftJoin('courses', 'courses.id', '=', 'course_companies.course_id')
            ->where('company_id', $user->company_id)
            ->when(
              $withTrash,
              function ($query) {
                return $query->withTrashed();
              },
              function ($query) {
                return $query->whereNull('courses.deleted_at');
              }
            )
            ->count();
        }

        $courseAssignment = $this->_courseQuery([
          'overdue' => $filter_overdue,
          'completed' => false,
          'withTrash' => $withTrash
        ]);
        $courseAssignment = \DB::select($courseAssignment)[0]->count;

        if ($filter_overdue == 'yes') {
          $completedCourse = 0;
        } else {
          $completedCourse = $this->_courseQuery([
            'overdue' => $filter_overdue,
            'completed' => true,
            'withTrash' => $withTrash
          ]);
          $completedCourse = \DB::select($completedCourse)[0]->count;
        }

        if ($filter_overdue != 'no') {
          $overdue = $this->_courseQuery([
            'overdue' => 'yes',
            'withTrash' => $withTrash
          ]);
          $overdue = \DB::select($overdue)[0]->count;
        } else {
          $overdue = 0;
        }

        $title = trans("controllers.course_reports");
        $breadcrumbs = [
          '' => $title,
        ];

        return view('reports.courses',
          compact(
            'title',
            'breadcrumbs',
            'courses',
            'courseAssignment',
            'completedCourse',
            'overdue',
            'filter',
            'filter_overdue'
          )
        );
      }

      public function reportCourseData(Request $request)
      {
        $filter = $request->option;
        $filter_overdue = $request->input('overdue', 'none');
        $withTrash = $filter == "archive";

        if ($filter != "archive") {
          $filter = "active";
        }

        $enrolled = $this->_courseQuery([
          'overdue' => $filter_overdue,
          'completed' => false,
          'percourse' => true,
          'withTrash' => $withTrash
        ]);
        $enrolled = \DB::select($enrolled);

        if ($filter_overdue != 'yes') {
          $completedCourse = $this->_courseQuery([
            'overdue' => $filter_overdue,
            'completed' => true,
            'percourse' => true,
            'withTrash' => $withTrash
          ]);
          $completedCourse = collect(\DB::select($completedCourse));
        }

        if ($filter_overdue != 'no') {
          $overdueCourse = $this->_courseQuery([
            'overdue' => 'yes',
            'percourse' => true,
            'withTrash' => $withTrash
          ]);
          $overdueCourse = collect(\DB::select($overdueCourse));
        }

        $courses = [];
        
        foreach ($enrolled as $e) {
          $c = Course::when($withTrash, function ($query) {
            return $query->withTrashed();
          })->find($e->course_id);
          
          if ($c) {
            $course['id'] = $c->id;
            $course['title'] = $c->title;
            $course['course_id'] = $c->course_id;
            $course['category'] = optional($c->category)->title;
            $course['enrolled'] = $e->count;

            if ($filter_overdue == 'yes') {
              $course['complete'] = 0;
              $course['incomplete'] = 100;
            } else {
              $complete = $completedCourse->filter(function ($value) use ($e) {
                return $value->course_id == $e->course_id;
              })->first();
              $course['complete'] = $course['enrolled'] ? round(($complete ? $complete->count : 0) * 100 / $course['enrolled'], 2) : 0;
              $course['incomplete'] = round(100 - $course['complete'], 2);
            }

            if ($filter_overdue == 'no') {
              $course['overdue'] = 0;
            } else {
              $overdue = $overdueCourse->filter(function ($value) use ($e) {
                return $value->course_id == $e->course_id;
              })->first();
              $course['overdue'] = $overdue ? $overdue->count : 0;
            }

            $courses[] = $course;
          }
        }

        return Datatables::of($courses)
          ->addColumn('action', function ($c) use ($filter) {
            return '<a href="'.
              route('reports.course.statistic',[
                encrypt($c['id']),
                $filter
              ]).
              '" class="btn btn-sm" title="View course statisitics">'.
              '<i class="fa fa-users" aria-hidden="true"></i></a>';
          })
          ->addColumn('course_id', function ($c) {
            return $c['course_id'];
          })
          ->rawColumns(['action', 'enrolled', 'complete', 'incomplete', 'overdue', 'course_id'])
          ->make(true);
      }

      public function reportCourseStatistic(Request $request)
      {
          $filter       = $request->filter;
          $encCourseId  = $request->id;
          $company_id   = auth()->user()->company_id;
          $id           = decrypt($request->id) ;

          $filter_status       = $request->input('status', 'none');
          $filter_team         = $request->input('team', 'none');
          $filter_department   = $request->input('department', 'none');
          $filter_overdue      = $request->input('overdue', 'none');
          $filter_department   = strtolower($filter_department);

          if ($filter != "archive") {
            $filter = "active";
          }
          
          $withTrash = $filter == "archive";
          $course = Course::when($withTrash, function ($query) {
            return $query->withTrashed();
          })->find($id);

          $courseCategory = CourseCategory::when($withTrash, function ($query) {
            return $query->withTrashed();
          })->find($course->category_id);

          $courseSubCategory = CourseCategory::when($withTrash, function ($query) {
            return $query->withTrashed();
          })->find($course->sub_category_id);

          $result = $this->_courseUserData(
            $id,
            auth()->user()->company_id,
            $withTrash,
            [
              'status' => $filter_status,
              'team' => $filter_team,
              'department' => $filter_department,
              'overdue' => $filter_overdue
            ]
          );

          $courseAssignment = $result['courseAssignment'];
          $totalUserEnroll = $result['totalUserEnroll'];
          $completedCourse = $result['completedCourse'];
          $overdue = $result['overdue'];
          $completeCourseUserIds = $result['completeCourseUserIds'];
          $team = $result['team'];
          $department = $result['department'];
          $courseUsers = $result['courseUsers'];

          $courseResult = CourseResult::when($withTrash, function ($query) {
            return $query->withTrashed();
          })->whereIn('courseuser_id', $courseAssignment);
          
          $courseNotComplete = (clone $courseResult)->whereNotIn('courseuser_id', $completeCourseUserIds);
          $courseProgress = (clone $courseNotComplete)->where("score", ">", 0)->count();
          $courseNotPass = (clone $courseNotComplete)->where("satisfied_status", 'Failed')->count();
          $totalUserScore = (clone $courseResult)->select(DB::raw("max(course_results.score) as mscore"))->groupBy('courseuser_id')->get();
          // $courseResult =  CourseResult::withTrashed()->select('total_time')->get();
          $courseResult = $courseResult->select(DB::raw('SEC_TO_TIME( SUM( TIME_TO_SEC( `total_time` ) ) ) AS timeSum'))->first();

          $total_time = "00:00:00";

          if ($courseResult && $courseResult->timeSum != "") {
            $total_time = $courseResult->timeSum;
          }

          $totalPassScore = 0 ;
          $remaining = 100 ;

          if (count($totalUserScore) > 0) {
            $totalScore = count($courseAssignment) * 100 ;
            $totalSum = 0 ;
            for ($tUS = 0; $tUS < count($totalUserScore); $tUS++) {
              $totalSum += $totalUserScore[$tUS]->mscore;
            }

            $totalPassScore = ($totalSum * 100 )/$totalScore;
            $remaining = 100 - $totalPassScore;
          }

/*
          for ($cr=  0; $cr < count($courseResult); $cr++) {
              $times = explode("." , $courseResult[$cr]['total_time']);
              $time = explode(":" , $times[0]);

              if (count($time) > 1) {
                $ntimes = $time[0] . ":" . $time[1] . ":" . $time[2];
                $total_time += (int)strtotime($ntimes);
              }
          }
*/

          $monthData = array();
          $monthLabel = array();
          $startDate = Carbon::now()->startOfMonth()->format("Y-m-d h:i:s");
          $endDate = Carbon::now()->endOfMonth()->format("Y-m-d h:i:s");
          $monthLabel[] = Carbon::now()->format("F").'-'.Carbon::now()->format("Y");

          $monthData[] = CourseUser::when(
            $withTrash,
            function ($query) {
              return $query->withTrashed();
            },
            function ($query) {
              return $query->leftJoin('users', 'users.id', '=', 'course_users.user_id')
                ->whereNull('users.deleted_at')
                ->where('users.active', true)
                ->where('users.is_suspended', false);
            }
          )->where("course_id", $id)
            ->where('enrol_date', '>=', $startDate)
            ->where('enrol_date', '<=', $endDate)
            ->count();

          for ($m = 1; $m < 12; $m++) {
            $startDate = Carbon::now()
                              ->startOfMonth()
                              ->subMonths($m)
                              ->format("Y-m-d h:i:s");
            $endDate = Carbon::now()
                              ->endOfMonth()
                              ->subMonths($m)
                              ->format("Y-m-d h:i:s");

            $monthData[] = CourseUser::where("course_id", $id)
                                      ->where('enrol_date', '>=', $startDate)
                                      ->where('enrol_date', '<=', $endDate)
                                      ->count();
            $monthLabel[] = Carbon::now()->subMonths($m)->format("F") . '-' . Carbon::now()->subMonths($m)->format("Y");
          }

          $monthData = array_reverse($monthData);
          $monthLabel = array_reverse($monthLabel) ;
          $today = Carbon::now()->format("Y-m-d");
          $yLabel = array($today) ;
          
          //$trainingTime = "00:00:00";
          //if($total_time > 0)
          $trainingTime =  $total_time ; //date("H:i:s", $total_time);

          $title = $course->title;
          $breadcrumbs = [
              route('reports.course',['filter' => $filter]) => trans("controllers.course_reports"),
              '' => $title,
          ];

          return view('reports.course-statistic',
            compact(
              'title',
              'breadcrumbs',
              'course',
              'courseCategory',
              'courseSubCategory',
              'totalPassScore',
              'remaining',
              'monthData',
              'monthLabel',
              'yLabel',
              'totalUserEnroll',
              'overdue',
              'team',
              'department',
              'courseProgress',
              'courseNotPass',
              'completedCourse',
              'trainingTime',
              'encCourseId',
              'filter',

              'filter_status',
              'filter_team',
              'filter_department',
              'filter_overdue',
              'courseUsers'
            )
          );
      }


      public function courseUsers(Request $request)
        {
          $filter = $request->filter;
          $encCourseId = $request->id;
          $courseId = decrypt($request->id) ;
        //  $course = CourseUser::select('users.first_name')->join('users', 'users.id', 'course_users.user_id')->where('course_id', $courseId);
          if($filter == "archive")
          {
            $course = Course::withTrashed()->where('id', $courseId)->first();
          }
          else {
            $filter = "active";
            $course = Course::where('id', $courseId)->first();
          }

          $title = trans("controllers.course_users");
          $breadcrumbs = [
              route('reports.course',['filter' => $filter]) => trans("controllers.courses"),
              '' => $title,
          ];



          return view('reports.course-users', compact('title', 'breadcrumbs', 'course', 'encCourseId', 'filter'));

        }

      protected function _courseUserData($course_id, $company_id, $withTrash, $filter)
      {
        $courseAssignment = [];
        $totalUserEnroll = 0;
        $completedCourse = 0;
        $overdue = 0;
        $team = [];
        $department = [];
        $courseUsers = [];
        $completeCourseUserIds = array();

        $query =
          'SELECT'.
          '  `users`.`first_name` AS first_name, '.
          '  `users`.`last_name` AS last_name, '.
          '  `users`.`email` AS email, '.
          '  `users`.`team_id` AS team_id, '.
          '  `users`.`department` AS department, '.
          '  DATE_FORMAT(`course_users`.`enrol_date`, "%d-%m-%Y") AS enrolled_date, '.
          '  DATE_FORMAT(`course_users`.`completion_date`, "%d-%m-%Y") AS completion_date, '.
          '  `course_users`.`id` AS id, '.
          '  `course_users`.`user_id` AS user_id, '.
          '  `course_users`.`completed` AS completed, '.
          '  `course_users`.`course_id` AS course_id, '.
          '  AVG(`course_results`.`score`) AS score, '.
          '  SEC_TO_TIME( SUM( TIME_TO_SEC( `course_results`.`total_time` ) ) ) AS total_time, '.
          '  COUNT(`course_results`.`id`) AS result_count, '.

          '  SUBSTRING(deadline, 1, LOCATE(" ", deadline) - 1) AS deadline_value, '.
          '  SUBSTRING(deadline, LOCATE(" ", deadline) + 1) AS deadline_unit, '.
          '  IFNULL(start_date, enrol_date) AS start_base_date '.
          'FROM `course_users` '.
          'JOIN `users` ON `users`.`id` = `course_users`.`user_id` '.
          'JOIN `courses` ON `courses`.`id` = `course_users`.`course_id` '.
          'LEFT JOIN `course_companies` ON '.
          '  `course_companies`.`course_id` = `course_users`.`course_id` '.
          '  AND `course_companies`.`company_id` = `users`.`company_id` '.
          'LEFT JOIN `course_results` ON `course_results`.`courseuser_id` = `course_users`.`id` '.
          // 'LEFT JOIN `modules` ON `modules`.`id` = `course_results`.`module_id` '.

          'WHERE TRUE '.
          ' AND `course_users`.`course_id` = ' . $course_id . ' '.
          // ' AND (`modules`.`type` = "Elearning" OR `modules`.`type` IS NULL '.
          ' AND `course_companies`.`deleted_at` IS NULL ';

        if ($company_id) {
          $query .= ' AND `users`.`company_id` = ' . $company_id . ' ';
        }

        if (!$withTrash) {
          $query .= ' AND `users`.`deleted_at` IS NULL '.
            'AND `course_users`.`deleted_at` IS NULL '.
            'AND `courses`.`deleted_at` IS NULL '.
            'AND `course_results`.`deleted_at` IS NULL '.
            'AND `users`.`active` = 1 AND `users`.`is_suspended` = 0 ';
        }

        if ($filter['overdue'] == 'yes') {
          $query .= 'AND `course_users`.`completed` = 0 ';
        }

        if ($filter['status'] == 'complete') {
          $query .= 'AND `course_users`.`completed` = 1 ';
        } else if ($filter['status'] == 'incomplete') {
          $query .= 'AND `course_users`.`completed` = 0 ';
        }

        if ($filter['team'] != 'none') {
          $query .= sprintf('AND `users`.`team_id` = "%s" ', $filter['team']);
        }

        if ($filter['department'] != 'none') {
          $query .= sprintf('AND `users`.`department` = "%s" ', $filter['department']);
        }

        $query .= 'GROUP BY `course_users`.`id`';

        $overdue_date =
          'CASE WHEN deadline_unit="day" THEN DATE_ADD(start_base_date, INTERVAL deadline_value DAY) '.
          '   WHEN deadline_unit="week" THEN DATE_ADD(start_base_date, INTERVAL deadline_value WEEK) '.
          '   WHEN deadline_unit="month" THEN DATE_ADD(start_base_date, INTERVAL deadline_value MONTH) '.
          '   ELSE DATE_ADD(start_base_date, INTERVAL deadline_value YEAR) END';
        
        $query =
          'SELECT '.
          '  t.id, '.
          '  t.first_name, '.
          '  t.last_name, '.
          '  t.email, '.
          '  t.department, '.
          '  t.team_id, '.
          '  t.enrolled_date, '.
          '  t.user_id, '.
          '  t.completed, '.
          '  t.result_count, '.
          '  t.completion_date AS completion_date, '.
          '  t.score, t.total_time, COUNT(`passed_results`.`id`) AS passed_count, '.
          '  IF(completed = "0" AND ' . $overdue_date . ' < NOW(), TRUE, FALSE) AS overdue '.
          'FROM (' . $query . ') AS t '.
          'LEFT JOIN `course_results` AS `passed_results` ON `passed_results`.`courseuser_id` = `t`.`id` '.
          '  AND `passed_results`.`satisfied_status` = "Passed" '.
          'WHERE TRUE ';

        if ($filter['overdue'] == 'yes') {
          $query .= 'AND (`t`.`completed` = "0" AND ' . $overdue_date . ' < NOW()) = TRUE ';
        } else if ($filter['overdue'] == 'no') {
          $query .= 'AND (`t`.`completed` = "0" AND ' . $overdue_date . ' < NOW()) = FALSE ';
        }
        
        $query .= 'GROUP BY `t`.`id`';

        $users = \DB::select($query);
        $totalUserEnroll = count($users);

        foreach ($users as $u) {
          $data = [
            'first_name' => $u->first_name,
            'last_name' => $u->last_name,
            'email' => $u->email,
            'department' => $u->department,
            'enrolled_date' => $u->enrolled_date,
            'completion_date' => $u->completion_date,
            'complete_status' => $u->completed ? trans('modules.completed') : trans('modules.incomplete'),
            'score' => $u->score,
            'total_time' => $u->total_time,
            'user_id' => $u->user_id,
            'overdue' => $u->overdue
          ];

          if ($u->team_id) {
            $team[] = $u->team_id;
          }

          if ($u->department) {
            $department[] = strtolower($u->department);
          }

          if ($u->result_count) {
            if ($u->result_count > $u->passed_count) {
              $data['satisfied_status'] = trans('modules.failed');
            } else {
              $data['satisfied_status'] = trans('modules.passed');
            }
          } else {
            $data['satisfied_status'] = trans('modules.not_started');
          }

          
          if ($u->completed) {
            $completeCourseUserIds[] = $u->id;
            $completedCourse++;
          }
          
          if ($u->overdue) {
            $overdue++;
          }

          $courseAssignment[] = $u->id;
          $courseUsers[] = $data;
        }

          $team = Team::whereIn('id', collect($team)->unique()->all())->get();
          $department = collect($department)->unique()->all();

          return compact(
            'courseAssignment',
            'totalUserEnroll',
            'completedCourse',
            'overdue',
            'completeCourseUserIds',
            'team',
            'department',
            'courseUsers'
          );
      }

      
      public function courseUserData(Request $request)
      {
          $filter = $request->option;
          $courseId = decrypt($request->id);

          $status       = $request->input('status', 'none');
          $team         = $request->input('team', 'none');
          $department   = $request->input('department', 'none');
          $overdue      = $request->input('overdue', 'none');
          $department   = strtolower($department);

          $withTrash = $filter == "archive";
          $result = $this->_courseUserData(
            $courseId,
            auth()->user()->company_id,
            $withTrash,
            [
              'status' => $status,
              'team' => $team,
              'department' => $department,
              'overdue' => $overdue
            ]
          );

          $courseUsers = $result['courseUsers'];

          if ($request->csv) {
            $path = public_path().'/report';
            $filename = "course_report" . str_random(15) . ".csv";
            
            if (!file_exists($path)) {
              mkdir($path, 0755, true);
            }
    
            $handle = fopen($path .'/'. $filename, 'w+');
            fputcsv($handle, array(
              trans("modules.first_name"),
              trans("modules.last_name"),
              trans("modules.email"),
              trans("modules.department"),
              trans("modules.enrolled_date"),
              trans("modules.completion_status"),
              trans("modules.status"),
              trans("modules.score"),
              trans("modules.total_time"),
              trans("modules.completion_date")
            ));

            foreach ($courseUsers as $user) {
              fputcsv($handle, [
                $user['first_name'],
                $user['last_name'],
                $user['email'],
                $user['department'],
                $user['enrolled_date'],
                $user['complete_status'],
                $user['satisfied_status'],
                $user['score'],
                $user['total_time'],
                $user['completion_date']
              ]);
            }
    
            fclose($handle);
    
            $headers = array(
                'Content-Type' => 'text/csv',
            );

            $course = Course::when($withTrash, function ($query) {
              return $query->withTrashed();
            })->find($courseId);
    
            return response()
                    ->download($path.'/'.$filename, 'course_report_' . $course->title . '.csv')
                    ->deleteFileAfterSend(true);
          }

          return Datatables::of($courseUsers)
            ->addColumn('action', function ($courseUsers) use ($filter) {
                return '<a href="' . route('users.show', [
                  'id' => encrypt($courseUsers['user_id']),
                ]) . '" class="btn btn-sm" title="view user details">
                <i class="fa fa-user" aria-hidden="true"></i>
                </a>';
            })
            ->rawColumns(['action'])
            ->make(true);
        }

        public function reportUserStatistic(Request $request)
        {

                $filter = $request->option;
                $id = decrypt($request->id) ;
                $userId = $request->id;
                $courseId = $request->course_id;


            if($filter == "archive")
            {
                $user = User::withTrashed()->where("id", $id)->first();
                $course = Course::withTrashed()->where("id", $courseId)->first();
            }
            else {
              $user = User::find($id);
              $course = Course::find($courseId);

            }



                $title = trans("controllers.user_course_reports");
                $breadcrumbs = [
                    route('reports.course',['filter' => $filter]) => trans("controllers.courses"),
                    route('reports.course.users', [encrypt($courseId), $filter]) => trans("controllers.course_users"),
                    '' => $title,
                ];

                $complete = 0 ;
                $passed = 0 ;
                $score = 0 ;
                $time = 0;
                $attempts = 0 ;
                $enrolDate ="";


                if($filter == "archive")
                {
                  $courseAssignment = CourseUser::withTrashed()
                                        ->where("course_id", $courseId)
                                        ->where("user_id", $id)->pluck("course_users.id");
                  $courseEroll = CourseUser::withTrashed()->select('enrol_date')->where("id", $courseAssignment)->first();

                }
                else {
                  $courseAssignment = CourseUser::
                                    leftJoin('users', 'users.id', '=', 'course_users.user_id')
                                    ->whereNull('users.deleted_at')
                                    ->where('users.active', true)
                                    ->where('users.is_suspended', false)
                                    ->where("course_users.course_id", $courseId)
                                    ->where("course_users.user_id", $id)->pluck("course_users.id");
                  if(count($courseAssignment) > 0)
                  {
                  $courseEroll = CourseUser::select('enrol_date')
                                    ->leftJoin('users', 'users.id', '=', 'course_users.user_id')
                                    ->whereNull('users.deleted_at')
                                    ->where('users.active', true)
                                    ->where('users.is_suspended', false)
                                    ->where("course_users.id", $courseAssignment)->first();
                                  }

                }


               if(@$courseEroll)
                $enrolDate =  Carbon::parse($courseEroll->enrol_date)->format('d-m-Y') ;

            //    $complete = CourseResult::whereIn('courseuser_id',$courseAssignment)->where("score", 100)->count();
            //    $passed = CourseResult::whereIn('courseuser_id',$courseAssignment)->where("satisfied_status", 'Passed')->count();
            //    $attempts = CourseResult::whereIn('courseuser_id',$courseAssignment)->count();

              //  $scoreResult = CourseResult::select(DB::raw('max(score) as score'))->where('courseuser_id',$courseAssignment)->groupBy("courseuser_id")->first();
            /*   if($scoreResult)
                $score = $scoreResult->score;
                $courseResult =  CourseResult::select('total_time')->whereIn('courseuser_id',$courseAssignment)->get();
                $total_time = 0;

                for($cr=0;$cr<count($courseResult);$cr++)
                {

                    $times = explode(".",$courseResult[$cr]['total_time']);
                    if(count($times) > 1)
                    {
                      $time = explode(":",$times[0]);
                       if(count($time) > 1)
                        {
                          $ntimes = $time[0].":".$time[1].":".$time[2] ;
                          $total_time += (int)strtotime($ntimes);
                       }
                    }

                }

                $time = "00:00:00";
                if($total_time > 0)
                 $time =  date("H:i:s", $total_time);
                  */

            //     $score_label = CourseResult::select(DB::raw('max(score) as score'))->whereIn('courseuser_id',$courseAssignment)->groupBy('updated_at')->orderBy('updated_at', 'desc')->pluck('score');
              //   $date_label = CourseResult::whereIn('courseuser_id',$courseAssignment)->groupBy('updated_at')->orderBy('updated_at', 'desc')->pluck('updated_at');

                return view('reports.user-statistic',
                  compact(
                    'title',
                    'breadcrumbs',
                    'user',
                    'course',
                    'enrolDate',
                    'userId',
                    'courseId',
                    'filter'
                  )
                );

        }

        public function courseUserHistoryData(Request $request)
        {

          $filter = $request->option;
          $id = decrypt($request->id) ;
          $courseId = $request->course_id;

          if($filter == "archive")
          {
            $courseAssignment = CourseUser::withTrashed()
                                          ->where("course_id", $courseId)
                                          ->where("user_id", $id)
                                          ->pluck("id");

            $userCourseResultData = CourseResult::withTrashed()
                                    ->select(
                                      'modules.title',
                                      'complete_status',
                                      'satisfied_status',
                                      'completion_date',
                                      'score',
                                      'total_time'
                                    )
                                     ->leftJoin('modules', 'modules.id', 'course_results.module_id')
                                     ->whereIn('course_results.courseuser_id',$courseAssignment)
                                     ->where('completion_date', '!=', '')
                                  //   ->groupBy('updated_at')
                                  //   ->orderBy('updated_at', 'desc')
                                     ->get();
          }
          else {
            $courseAssignment = CourseUser::leftJoin('users', 'users.id', '=', 'course_users.user_id')
                                              ->whereNull('users.deleted_at')
                                              ->where('users.active', true)
                                              ->where('users.is_suspended', false)
                                              ->where("course_users.course_id", $courseId)
                                              ->where("course_users.user_id", $id)
                                              ->pluck("course_users.id");

            $userCourseResultData = CourseResult::select(
                                      'modules.title',
                                      'complete_status',
                                      'satisfied_status',
                                      'completion_date',
                                      'score',
                                      'total_time'
                                    )
                                     ->leftJoin('modules', 'modules.id', 'course_results.module_id')
                                     ->whereIn('course_results.courseuser_id',$courseAssignment)
                                     ->where('completion_date', '!=', '')
                                  //   ->groupBy('updated_at')
                                  //   ->orderBy('updated_at', 'desc')
                                     ->get();
          }



   //print_r($userCourseResultData) ; die;

          return Datatables::of($userCourseResultData)
                      ->editColumn('completion_date', function ($userCourseResultData) {
                        return $userCourseResultData->completion_date
                                ? Carbon::parse($userCourseResultData->completion_date)->format("d-m-Y")
                                : '';
                      })
                      ->editColumn('title', function ($userCourseResultData) {
                        return $userCourseResultData->title;
                      })
                      ->make(true);

        }

        private function generateDateRange(Carbon $start_date, Carbon $end_date)
        {
            $dates = [];

            for($date = $start_date->copy(); $date->lte($end_date); $date->addDay()) {
                $dates[] = $date->format('Y-m-d');
            }

            return $dates;
        }
}
