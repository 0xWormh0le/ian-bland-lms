<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use App\Module;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::user()->isSysAdmin()) {
            return view('dashboard.system.index');
        } else {
            if (Auth::user()->roleUser->is_learner && Auth::user()->role_id !=1) {
                return $this->learnerDashboard();
            } else {
                return $this->clientDashboard();
            }
        }

        return view('home');
    }

    /**
     * Show Client Admin Dashboard
     */
    public function clientDashboard()
    {
        $currMonth = date('n', time());

        $courseEnrolledResult = \App\CourseCompany::select(
                          'course_companies.id',
                          'course_companies.course_id'
                        )
                        ->join('courses', 'courses.id', '=', 'course_id')
                        ->whereNull('courses.deleted_at')
                        ->where('course_companies.company_id', \Auth::user()->company_id);
        $courseEnrolled  = $courseEnrolledResult->count();
        $users = \App\User::select('id')
                        ->where('company_id', \Auth::user()->company_id)
                        ->where('role_id', '!=', 0)
                        ->where('role_id', '!=', 1)
                        ->count();

        $participantsResult = \App\CourseUser::join('courses', 'courses.id', '=', 'course_users.course_id')
                        ->join('users', 'users.id', '=', 'course_users.user_id')
                        ->whereNull('courses.deleted_at')
                        ->where('users.company_id', \Auth::user()->company_id);

        $participants = $participantsResult->count();

        /*
        $completed = \App\CourseUser::select('course_users.id')
                        ->join('courses', 'courses.id', '=', 'course_users.course_id')
                        ->join('users', 'users.id', '=', 'course_users.user_id')
                        ->where('users.company_id', \Auth::user()->company_id)
                        ->where('course_users.completed', true)
                        ->count();
        */

        /*
        $courseUserIds = $participantsResult->pluck('course_users.id')->unique();
        $userIds = $participantsResult->pluck('course_users.user_id')->unique();
        $userCouseIds = $participantsResult->pluck('course_id')->unique();

        $moduleIds = Module::whereIn('course_id', $userCouseIds)->where("type", "Elearning")->pluck("id");

        $completed = 0 ;

        if (count($userIds) > 0) {
          foreach ($userIds as $ukey => $uval) {
            if (count($userCouseIds) > 0) {
              foreach($userCouseIds as $key=>$val) {
                $courseStatusResult = course_completion_rules_result($val, $uval);

                if($courseStatusResult['complete'] == 1) {
                  $completed++;
                }
              }
            }
          }
        }
        */

        $company_id = Auth::user()->company_id;
        $completed = \App\CourseUser::when($company_id, function ($query, $company_id) {
          return $query->join('users', 'users.id', '=', 'course_users.user_id')
                      ->where('users.company_id', $company_id);
        })
          ->whereNull('course_users.deleted_at')
          ->where('course_users.active', 1)
          ->avg('completion_percentage');

        $completed = round($completed);

        $statLabels =
        $courseStatValue =
        $userStatValue =
        $participantStatValue =
        $completedStatValue = [];

        for ($i = $currMonth - 6; $i <= $currMonth; $i++) {
            $statLabels[] = date('F', mktime(0, 0, 0, $i, 10));

            $res = \App\CourseCompany::select('company_id')
                        ->join('courses', 'courses.id', '=', 'course_id')
                        ->whereNull('courses.deleted_at')
                        ->where('course_companies.company_id', \Auth::user()->company_id)
                        ->where(\DB::raw('month(course_companies.updated_at)'), $i)
                        ->where(\DB::raw('year(course_companies.updated_at)'), date('Y'))
                        ->count();

            $courseStatValue[] = $res;

            $res = \App\User::select('id')
                        ->where('company_id', \Auth::user()->company_id)
                        ->where(\DB::raw('month(created_at)'), $i)
                        ->where(\DB::raw('year(created_at)'), date('Y'))
                        ->where('role_id', '!=', 0)
                        ->where('role_id', '!=', 1)
                        ->count();
            $userStatValue[] = $res;

            $res = \App\CourseUser::select('course_users.id')
                        ->join('courses', 'courses.id', '=', 'course_users.course_id')
                        ->join('users', 'users.id', '=', 'course_users.user_id')
                        ->whereNull('courses.deleted_at')
                        ->whereNull('users.deleted_at')
                        ->where('users.company_id', \Auth::user()->company_id)
                        ->where(\DB::raw('month(course_users.enrol_date)'), $i)
                        ->where(\DB::raw('year(course_users.enrol_date)'), date('Y'))
                        ->count();
            $participantStatValue[] = $res;



            /*$res = \App\CourseUser::select('course_users.id')
                        ->join('courses', 'courses.id', '=', 'course_users.course_id')
                        ->join('users', 'users.id', '=', 'course_users.user_id')
                        ->where('users.company_id', \Auth::user()->company_id)
                        ->where(\DB::raw('month(course_users.completion_date)'), $i)
                        ->where(\DB::raw('year(course_users.completion_date)'), date('Y'))
                        ->where('course_users.completed', true)
                        ->count();*/
                        
            /*
            $res = \App\CourseUser::join('courses', 'courses.id', '=', 'course_users.course_id')
                        ->join('users', 'users.id', '=', 'course_users.user_id')
                        ->whereNull('courses.deleted_at')
                        ->whereNull('users.deleted_at')
                        ->where('users.company_id', \Auth::user()->company_id)
                        ->where(\DB::raw('month(course_users.completion_date)'), $i)
                        ->where(\DB::raw('year(course_users.completion_date)'), date('Y'));

            $courseUserIds = $res->pluck('course_users.id')->unique();
            $userCouseIds = $res->pluck('course_id')->unique();

            $moduleIds = Module::whereIn('course_id', $userCouseIds)->where("type", "Elearning")->pluck("id");

            $completedv1 = \App\CourseResult::whereIn('courseuser_id', $courseUserIds)
                                            ->whereIn('module_id', $moduleIds)
                                            ->where('complete_status', 'Completed')
                                            ->count();
            $completedStatValue[] = $completedv1;
            */

            $company_id = Auth::user()->company_id;
            $complete_rate = \App\CourseUser::when($company_id, function ($query, $company_id) {
              return $query->join('users', 'users.id', '=', 'course_users.user_id')
                          ->where('users.company_id', $company_id);
            })
            ->whereNull('course_users.deleted_at')
            ->where(\DB::raw('month(enrol_date)'), '<=', $i)
            ->where(\DB::raw('year(enrol_date)'), '<=', date('Y'))
            ->where('course_users.active', 1)
            ->avg('completion_percentage');

            $completedStatValue[] = round($complete_rate);
        }

        $coursesList = \App\CourseCompany::select('courses.id', 'courses.title')
                        ->join('courses', 'courses.id', '=', 'course_id')
                        ->whereNull('courses.deleted_at')
                        ->where('course_companies.company_id', \Auth::user()->company_id)
                        ->pluck('courses.title', 'courses.id');

        $courses =
        $courseCompleted =
        $courseIncomplete =
        $courseCompletedNumber =
        $courseIncompleteNumber = [];
        $totalCompleted = $totalIncomplete = 0;

        foreach ($coursesList as $id => $title) {
            $courses[] = $title;

            $res = \App\CourseUser::join('courses', 'courses.id', '=', 'course_users.course_id')
                        ->join('users', 'users.id', '=', 'course_users.user_id')
                        ->whereNull('courses.deleted_at')
                        ->whereNull('users.deleted_at')
                        ->where('users.company_id', \Auth::user()->company_id)
                        ->where('course_users.course_id', $id)->get();

            $courseTotalUser = 0;
            $courseCompleteTotal = 0 ;

            for ($r = 0; $r < count($res); $r++) {
              $courseTotalUser++;
              $courseComResult = course_completion_rules_result($id, $res[$r]->user_id);

              if ($courseComResult['complete'] == 1) {
                $courseCompleteTotal++;
              }
            }

            if ($courseTotalUser > 0) {
              $courseCompletedNumber[] = $courseCompleteTotal;
              if($courseTotalUser > $courseCompleteTotal) {
                $courseIncompleteNumber[] = $courseTotalUser - $courseCompleteTotal;
              } else {
                $courseIncompleteNumber[] = 0 ;
              }

              $courseCompleted[] = (float)(($courseCompleteTotal / $courseTotalUser) * 100) ;
              $courseIncomplete[] = (float)((($courseTotalUser - $courseCompleteTotal)  / $courseTotalUser) * 100);
            } else {
              $courseCompleted[] = 0 ;
              $courseIncomplete[] = 0 ;
            }
        }

        $teams = \App\Team::where('company_id', \Auth::user()->company_id)->get();

        return view('dashboard.client.index',
                compact(
                  'courseEnrolled',
                  'statLabels',
                  'courseStatValue',
                  'users',
                  'userStatValue',
                  'participants',
                  'participantStatValue',
                  'completed',
                  'completedStatValue',
                  'courses',
                  'courseCompleted',
                  'courseIncomplete',
                  'totalCompleted',
                  'totalIncomplete',
                  'teams',
                  'courseCompletedNumber',
                  'courseIncompleteNumber'
                )
              );
    }

    public function teamResult($slug)
    {
        $team = \App\Team::findBySlug($slug);
        if($team)
        {
            $title = $team->team_name.' '.trans('controllers.member_result');
            $breadcrumbs = [
                route('home') => trans('controllers.team_results'),
                '' => $team->team_name,
            ];

            $users = \App\User::where('team_id', $team->id)->get();

            return view('dashboard.client.team-results', compact('users', 'title', 'breadcrumbs'));
        }
        Alert::error(__('messages.invalid_request'));
        return redirect('/');
    }

    /**
     * Show Learner Dashboard
     */
    public function learnerDashboard()
    {
		$completedModules = array();

		$enrolledCoursesResult = \App\CourseUser::join('courses', 'courses.id', '=', 'course_users.course_id')
                                 ->whereNull('courses.deleted_at')
                                 ->where('user_id', \Auth::id());
        $userCouseIds = $enrolledCoursesResult->pluck('course_id')->unique();
        $courseUserIds = $enrolledCoursesResult->pluck('course_users.id')->unique();


      //  $moduleIds = Module::whereIn('course_id', $userCouseIds)->where("type", "Elearning")->pluck("id");
        $completedCourses = 0;
        $incompleteCourses = 0;
        $completeCourseArr = array();
        for($c=0;$c<count($userCouseIds);$c++)
        {
           $courseStatusResult = course_completion_rules_result($userCouseIds[$c], \Auth::id());

          if($courseStatusResult['complete'] == 1)
          {
              $completedCourses++;
              $completeCourseArr[] = $userCouseIds[$c];
            }
          else {
                  $incompleteCourses++;
                }
        }



      /*  $completedCourses = \App\CourseResult::whereIn('courseuser_id', $courseUserIds)
                                              ->whereIn('module_id', $moduleIds)
                                              ->where('complete_status', 'Completed')
                                              ->count();*/


        $enrolledCourses = $enrolledCoursesResult->count();

        /*$inProgressCourses = \App\CourseUser::select('course_users.id')
                                ->join('courses', 'courses.id', '=', 'course_users.course_id')
                                ->where('user_id', \Auth::id())
                                ->where('completion_percentage', '>', 0)
                                ->where('course_users.completed', false)
                                ->count();*/

       $inProgressCourses = \App\CourseResult::join('course_users', 'course_users.id', 'course_results.courseuser_id')
         ->whereNotIn('course_users.course_id', $completeCourseArr)
         ->whereIn('courseuser_id',$courseUserIds)
         ->where("score", '>', 0)->where("score", '<', 0)->count();
        $notStarted = $enrolledCourses - ($inProgressCourses + $completedCourses);

       //$inProgressCourses =   $incompleteCourses ;

      /* \App\CourseResult::whereIn('courseuser_id', $courseUserIds)
                                              ->whereIn('module_id', $moduleIds)
                                              ->where('complete_status', 'incomplete')
                                              ->count();*/


        $today = \Carbon\Carbon::today()->format("Y-m-d");
      /*  $dueCourses = \App\CourseUser::select('course_users.course_id')
                                ->join('courses', 'courses.id', '=', 'course_users.course_id')
                                ->where('user_id', \Auth::id())
                                ->where('deadline_date', '<', $today)
                                ->count();
*/

        $mycourse = \App\CourseUser::where("user_id", \Auth::id())->get();
        $dueCourses = 0 ;

        if(count($mycourse) > 0)
          foreach($mycourse as $mcourse)
          {
            $course = $mcourse->course;
            if($course)
            {
              $modules = \App\Module::where('course_id', $course->id)->pluck('id');

              $result = \App\CourseResult::where('courseuser_id', $mcourse->id)
                                ->whereIn('module_id', $modules)
                                ->where('complete_status', 'Completed')->pluck('id');

              if(count($modules) != count($result) )
              if(!in_array($course->id, $completeCourseArr))
              {

              $overdue = 0 ;
              $today = \Carbon\Carbon::today()->format("d/m/Y");

              if($course && \Auth::user()->company_id)
              {
                $result = \App\CourseCompany::select('deadline')
                                  ->where('company_id', \Auth::user()->company_id)
                                  ->where('active', true)
                                  ->where('course_id', $course->id)
                                  ->first();
                if($result && $result->deadline !="")
                {

                  $duration = explode(" ",$result->deadline);

                if($mcourse->start_date != "")
                  {
                    $start_date = str_replace("/","-",$mcourse->start_date);
                    $start = \Carbon\Carbon::createFromFormat('d-m-Y', $start_date)->format('Y-m-d') ;
                    $start = \Carbon\Carbon::createFromFormat('Y-m-d', $start);
                  }
                else
                 $start = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s',  $mcourse->enrol_date);

                 switch($duration[1])
                 {
                   case 'day' :
                    $start->addDay($duration[0]); break;
                   case 'week' :
                    $start->addWeek($duration[0]);  break;
                   case 'month' :
                    $start->addMonth($duration[0]);  break;
                   case 'year' :
                    $start->addYear($duration[0]);  break;
                 }

                  if((\Carbon\Carbon::today() > \Carbon\Carbon::parse($start)))
                  {
                    $dueCourses = 1;
                  }

                }
               }
             }
            }
          }



       $completedElearning = $completedCourses;
        /* \App\CourseResult::select('course_results.id')
                                ->leftJoin('course_users', 'course_users.id', '=', 'course_results.courseuser_id')
                                ->join('courses', 'courses.id', '=', 'course_users.course_id')
                                ->where('course_users.user_id', \Auth::id())
                                ->where('course_results.complete_status', 'Completed')
                                ->count();
*/

        if($completedElearning > 0)
        {

            $avg = \App\CourseResult::select(\DB::raw('avg(score) as avg_score'), \DB::raw('SEC_TO_TIME(ROUND(AVG(TIME_TO_SEC(total_time))))  as avg_time'))
                                ->leftJoin('course_users', 'course_users.id', '=', 'course_results.courseuser_id')
                                ->join('courses', 'courses.id', '=', 'course_users.course_id')
                                ->whereNull('courses.deleted_at')
                                ->where('course_users.user_id', \Auth::id())
                                ->where('course_results.complete_status', 'Completed')
                                ->first();


            $avg_score = @$avg->avg_score ?: 0.0;
            $avg_time = @$avg->avg_time ?: '00:00:00';
        }
        else
        {
            $avg_score = 0.0;
            $avg_time = '00:00:00';
        }

        $month = $enrolled = $completed = $incomplete = [];
        for($i = 1; $i<=12; $i++)
        {
            $month[] = date('M', mktime(0, 0, 0, $i, 10));
            $enrolled[] = \App\CourseUser::select('course_users.id')
                        ->join('courses', 'courses.id', '=', 'course_users.course_id')
                        ->whereNull('courses.deleted_at')
                        ->where('course_users.user_id', \Auth::id())
                        ->where(\DB::raw('month(course_users.enrol_date)'), $i)
                        ->where(\DB::raw('year(course_users.enrol_date)'), date('Y'))
                        ->count();


            $completedCoursesG = 0;
            $incompleteCoursesG = 0;

          /*  $completedCoursesG +=  \App\SCORMReport::join('scorm', 'scorm.id', 'scorm_report.course')
                                   ->whereIn('scorm.course_id', $userCouseIds)
                                   ->where('scorm_report.user', \Auth::id())
                                   ->where('scorm_report.complete_status', 'completed')
                                   ->where(\DB::raw('month(scorm_report.updated_at)'), $i)
                                   ->where(\DB::raw('year(scorm_report.updated_at)'), date('Y'))
                                   ->count();


            $incompleteCoursesG +=  \App\SCORMReport::join('scorm', 'scorm.id', 'scorm_report.course')
                                   ->whereIn('scorm.course_id', $userCouseIds)
                                   ->where('scorm_report.user', \Auth::id())
                                   ->where('scorm_report.complete_status', 'incomplete')
                                   ->where(\DB::raw('month(scorm_report.updated_at)'), $i)
                                   ->where(\DB::raw('year(scorm_report.updated_at)'), date('Y'))
                                   ->count();
                                   */
          for($c=0; $c < count($userCouseIds); $c++)
          {
            $courseResult = $this->courseCompletionRulesResult($userCouseIds[$c], \Auth::id(), $i);

            if($courseResult['complete'] == 1)
            {
              $completedCoursesG++;
            }
            else {
              $incompleteCoursesG++;
            }
          }

           $completed[] = $completedCoursesG;
           /*\App\CourseUser::select('course_users.id')
                        ->join('courses', 'courses.id', '=', 'course_users.course_id')
                        ->join('course_results', 'course_results.courseuser_id', '=', 'course_users.id')
                        ->where('course_users.user_id', \Auth::id())
                        ->where(\DB::raw('month(course_results.completion_date)'), $i)
                        ->where(\DB::raw('year(course_results.completion_date)'), date('Y'))
                        ->where('course_results.complete_status', 'Completed')
                        ->whereIn('course_results.module_id', $moduleIds)
                        ->count();*/
          $incomplete[] = $incompleteCoursesG ;
          /*\App\CourseUser::select('course_users.id')
                       ->join('courses', 'courses.id', '=', 'course_users.course_id')
                       ->join('course_results', 'course_results.courseuser_id', '=', 'course_users.id')
                       ->where('course_users.user_id', \Auth::id())
                       ->where(\DB::raw('month(course_results.updated_at)'), $i)
                       ->where(\DB::raw('year(course_results.updated_at)'), date('Y'))
                       ->where('course_results.complete_status', 'incomplete')
                       ->whereIn('course_results.module_id', $moduleIds)
                       ->count();*/

        }

        $trans = [
          '@MY_COURSES' => '<a href="/my-courses">My Courses</a>',
          '@MY_CERTIFICATES' => '<a href="/my-certificates">My Certificates</a>',
          '@MY_SCHEDULES' => '<a href="/my-schedules">My Schedules</a>',
          '@MY_TICKETS' => '<a href="/my-tickets">My Tickets</a>',
          '@SECURITY_TRAIN' => '<a href="mailto:support@securitytrain.me">support@securitytrain.me</a>',
          '@SUBMIT_TICKET' =>
            '<div class="row justify-content-center pt-4">' .
            '<div class="d-flex flex-wrap justify-content-center bg-info p-3 align-items-center shadow rounded">'.
            '    <div class="text-center">' .
            '    <i class="fa fa-life-ring pr-3" style="font-size: 70px"></i>' .
            '</div>' .
            '<div class="p-2">' .
            '    <h4 >Have any problems with your courses?</h4>' .
            '    <p class="mb-0">Submit a ticket and we\'ll get back to you as soon as we can.</p>' .
            '</div>' .
            '<div  class="pt-2 pb-2">' .
            '    <a href="javascript:void()"  class="btn btn-lg btn-outline-light ml-3 mr-3">SUBMIT A TICKET</a>' .
            '</div>' .
            '</div>'
        ];
        $welcomeTemplate = strtr(welcome_template(auth()->user()), $trans);

        return view('dashboard.learner.index', compact(
            'completedCourses',
            'dueCourses',
            'enrolledCourses',
            'inProgressCourses',
            'completedModules',
            'welcomeTemplate',
            'completedElearning',
            'avg_score',
            'avg_time',
            'month',
            'enrolled',
            'completed',
            'incomplete',
            'notStarted'
        ));
    }



    private function courseCompletionRulesResult($course_id, $user_id=0, $month)
    {
       $returnResult = array("complete" => 0, "percentage" => 0.00);

       if($user_id == 0) $user_id = \Auth::id();

       $user = \App\User::find($user_id);
       $courseConfig = \App\CourseConfig::where("company_id", $user->company_id)->where("course_id", $course_id)->first();

       if(!$courseConfig)
       {
         $courseConfig = \App\CourseConfig::whereNull("company_id")->where("course_id", $course_id)->first();
       }


       $courseUser = \App\CourseUser::myCourse($course_id, $user_id);


       $courseModules = \App\Module::where('course_id', $course_id)->where('type', 'Elearning')->get();

      if($courseModules && count($courseModules) == 0)
        {
          return $returnResult;
        }

      if ($courseConfig) {
        if ($courseConfig->completion_rule == "all" || $courseConfig->completion_rule == "any") {
         //  $courseModules = \App\Module::where('course_id', $course_id)->where('type', 'Elearning')->get();
           $completeCount = 0;
           if($courseUser)
           for($cm=0;$cm<count($courseModules);$cm++)
           {
             $result = \App\CourseResult::where('courseuser_id', $courseUser->id)
             ->where('module_id', $courseModules[$cm]->id)
             ->where(\DB::raw('month(course_results.completion_date)'), $month)
             ->where(\DB::raw('year(course_results.completion_date)'), date('Y'))->first();
 
           //  $result = \App\CourseResult::getModuleResult($courseUser->id, $courseModules[$cm]->id);
 
             if($result && $result->complete_status == 'Completed')
             {
                $completeCount++;
             }
           }
 
           if ($courseConfig->compoletion_rule == "all") {
             if ($completeCount == count($courseModules)) {
               $returnResult['complete'] = 1;
               $returnResult['percentage'] = 100;
             }
           } else if ($completeCount > 0) {
             $returnResult['complete'] = 1;
             $returnResult['percentage'] = 100;
           }
 
        } else {
            $moduleRequireStatus = 0;
            $percentRequireStatus = 0;
            $percentCompleted = 0;
 
             if($courseConfig->completion_modules !="")
             {
                  $moduleRequired = explode(",", $courseConfig->completion_modules);
                  $requireCount = 0;
                  if($courseUser)
                  for($mr=0;$mr < count($moduleRequired); $mr++)
                  {
                   // $result = \App\CourseResult::getModuleResult($courseUser->id, $moduleRequired[$mr]);
 
                    $result = \App\CourseResult::where('courseuser_id', $courseUser->id)
                    ->where('module_id', $moduleRequired[$mr])
                    ->where(\DB::raw('month(course_results.completion_date)'), $month)
                    ->where(\DB::raw('year(course_results.completion_date)'), date('Y'))->first();
 
 
                    if($result && $result->complete_status == 'Completed')
                    {
                       $requireCount++;
                    }
                  }
 
                  if($requireCount == count($moduleRequired))
                  {
                    $moduleRequireStatus = 1;
                  }
 
             }
             else{
               //  $moduleRequireStatus = 1;
             }
 
            if($courseConfig->completion_percentage !="" && $courseConfig->completion_percentage > 0)
            {
 
                $percentCompleted = $this->coursePercent($course_id, $courseUser, $month);
 
                 if($percentCompleted >= $courseConfig->completion_percentage)
                 {
                    $percentRequireStatus = 1;
                 }
            }
            else {
               //  $percentRequireStatus = 1;
            }
 
 
           if($moduleRequireStatus == 1 && $percentRequireStatus == 1)
           {
             $returnResult['complete'] = 1;
             $returnResult['percentage'] = $this->coursePercent($course_id, $courseUser, $month);
           }
        }
      }



       if(!$courseConfig || ($courseConfig->completion_modules == "" && ($courseConfig->completion_percentage =="" || $courseConfig->completion_percentage==0)) ){
         $percent = $this->coursePercent($course_id, $courseUser, $month);
         $returnResult['percentage'] = $percent;
         if($percent == 100)
         {
           $returnResult['complete'] = 1;
         }
       }

      return $returnResult;
    }

    private function coursePercent($course_id, $courseUser, $month)
    {

      $courseModules = \App\Module::where('course_id', $course_id)->where('type', 'Elearning')->get();
      $completeCount = 0;
      $percentCompleted = 0;
     if($courseUser)
      for($cm=0;$cm<count($courseModules);$cm++)
      {
        $result = \App\CourseResult::where('courseuser_id', $courseUser->id)
        ->where('module_id', $courseModules[$cm]->id)
        ->where(\DB::raw('month(course_results.completion_date)'), $month)
        ->where(\DB::raw('year(course_results.completion_date)'), date('Y'))->first();

      //  $result = \App\CourseResult::getModuleResult($courseUser->id, $courseModules[$cm]->id);

        if($result && $result->complete_status == 'Completed')
        {
           $completeCount++;
        }
      }

       $total = count($courseModules);
       if($total > 0)
        $percentCompleted = ($completeCount / $total ) * 100 ;
      return $percentCompleted;
    }
}
