<?php

namespace App\Http\Repositories;

use Illuminate\Http\Request;
use App\Course;
use App\Module;
use App\Elearning;
use App\CourseMember;
use App\CourseUser;
use App\CourseResult;
use App\CourseCategory;
use App\User;
use Alert, Auth, DB;
use App\Role;
use Carbon\Carbon;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Response;


class LearnerReport
{
    public function __construct()
    {
    }

    public function courseCompletionRulesResult($course_id, $user_id=0, $day, $month, $year, $filter)
    {
        $returnResult = array("complete" => 0, "percentage" => 0.00);

        if ($user_id == 0) {
          $user_id = \Auth::id();
        }

        if ($filter == "archive") {
          $user = \App\User::find($user_id);
          @$courseConfig = \App\CourseConfig::withTrashed()
                              ->where("company_id", $user->company_id)
                              ->where("course_id", $course_id)->first();

          if (!$courseConfig) {
            $courseConfig = \App\CourseConfig::withTrashed()
                              ->whereNull("company_id")
                              ->where("course_id", $course_id)->first();
          }

          $courseUser = \App\CourseUser::myCourseWithTrashed($course_id, $user_id);
          $courseModules = \App\Module::withTrashed()
                                      ->where('course_id', $course_id)
                                      ->where('type', 'Elearning')->get();
        } else {
          $user = \App\User::find($user_id);
          @$courseConfig = \App\CourseConfig::where("company_id", $user->company_id)->where("course_id", $course_id)->first();

          if (!$courseConfig) {
              $courseConfig = \App\CourseConfig::whereNull("company_id")->where("course_id", $course_id)->first();
          }

          $courseUser = \App\CourseUser::myCourse($course_id, $user_id);
          $courseModules = \App\Module::where('course_id', $course_id)
                                      ->where('type', 'Elearning')->get();
        }

        if ($courseModules && count($courseModules) == 0) {
          return $returnResult;
        }

        if ($courseConfig) {
          if ($courseConfig->completion_rule == "all" || $courseConfig->completion_rule == "any") {
          //  $courseModules = \App\Module::where('course_id', $course_id)->where('type', 'Elearning')->get();
              $completeCount = 0;
              if ($courseUser) {
                for ($cm = 0; $cm < count($courseModules); $cm++) {
                  if ($filter == "archive") {
                    $result = \App\CourseResult::withTrashed()
                                            ->where('courseuser_id', $courseUser->id)
                                            ->where('module_id', $courseModules[$cm]->id)
                                            ->where(\DB::raw('day(course_results.completion_date)'), $day)
                                            ->where(\DB::raw('month(course_results.completion_date)'), $month)
                                            ->where(\DB::raw('year(course_results.completion_date)'), $year)->first();
                  } else {
                      $result = \App\CourseResult::where('courseuser_id', $courseUser->id)
                                                ->where('module_id', $courseModules[$cm]->id)
                                                ->where(\DB::raw('day(course_results.completion_date)'), $day)
                                                ->where(\DB::raw('month(course_results.completion_date)'), $month)
                                                ->where(\DB::raw('year(course_results.completion_date)'), $year)->first();
                  }
      
                  //  $result = \App\CourseResult::getModuleResult($courseUser->id, $courseModules[$cm]->id);
      
                  if ($result && $result->complete_status == 'Completed') {
                    $completeCount++;
                  }
                }
              }
  
              if ($courseConfig->completion_rule == "all") {
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
  
              if ($courseConfig->completion_modules != "") {
                $moduleRequired = explode(",", $courseConfig->completion_modules);
                $requireCount = 0;

                if ($courseUser) {
                  for ($mr = 0; $mr < count($moduleRequired); $mr++) {
                    // $result = \App\CourseResult::getModuleResult($courseUser->id, $moduleRequired[$mr]);
                    if ($filter == "archive") {
                      $result = \App\CourseResult::withTrashed()
                                              ->where('courseuser_id', $courseUser->id)
                                              ->where('module_id', $moduleRequired[$mr])
                                              ->where(\DB::raw('day(course_results.completion_date)'), $day)
                                              ->where(\DB::raw('month(course_results.completion_date)'), $month)
                                              ->where(\DB::raw('year(course_results.completion_date)'), $year)->first();
                    } else {
                      $result = \App\CourseResult::where('courseuser_id', $courseUser->id)
                                              ->where('module_id', $moduleRequired[$mr])
                                              ->where(\DB::raw('day(course_results.completion_date)'), $day)
                                              ->where(\DB::raw('month(course_results.completion_date)'), $month)
                                              ->where(\DB::raw('year(course_results.completion_date)'), $year)->first();
                    }

                    if ($result && $result->complete_status == 'Completed') {
                        $requireCount++;
                    }
                  }
                }

                if ($requireCount == count($moduleRequired)) {
                  $moduleRequireStatus = 1;
                }
              } else {
                  //$moduleRequireStatus = 1;
              }
  
              if ($courseConfig->completion_percentage != "" &&
                  $courseConfig->completion_percentage > 0) {
  
                  $percentCompleted = $this->coursePercent($course_id, $courseUser,  $day, $month, $year, $filter);
  
                  if ($percentCompleted >= $courseConfig->completion_percentage) {
                      $percentRequireStatus = 1;
                  }
              } else {
                  //$percentRequireStatus = 1;
              }
  
              if ($moduleRequireStatus == 1 && $percentRequireStatus == 1) {
                $returnResult['complete'] = 1;
                $returnResult['percentage'] = $this->coursePercent($course_id, $courseUser,  $day, $month, $year, $filter);
              }
          }
        }



        if (!$courseConfig || ($courseConfig->completion_modules == "" && ($courseConfig->completion_percentage =="" || $courseConfig->completion_percentage==0)) )
        {
            $percent = $this->coursePercent($course_id, $courseUser, $day, $month, $year, $filter);
            $returnResult['percentage'] = $percent;

            if ($percent == 100) {
              $returnResult['complete'] = 1;
            }
        }

        return $returnResult;
    }

    public function coursePercent($course_id, $courseUser, $day, $month, $year, $filter)
    {

        if($filter == "archive")
        {
        $courseModules = \App\Module::withTrashed()->where('course_id', $course_id)->where('type', 'Elearning')->get();
        }
        else {
        $courseModules = \App\Module::where('course_id', $course_id)->where('type', 'Elearning')->get();

        }
        $completeCount = 0;
        $percentCompleted = 0;
        if($courseUser)
        for($cm=0;$cm<count($courseModules);$cm++)
        {

        if($filter == "archive")
        {
            $result = \App\CourseResult::withTrashed()
            ->where('courseuser_id', $courseUser->id)
            ->where('module_id', $courseModules[$cm]->id)
            ->where(\DB::raw('day(course_results.completion_date)'), $day)
            ->where(\DB::raw('month(course_results.completion_date)'), $month)
            ->where(\DB::raw('year(course_results.completion_date)'), $year)->first();
        }
        else {
            $result = \App\CourseResult::where('courseuser_id', $courseUser->id)
            ->where('module_id', $courseModules[$cm]->id)
            ->where(\DB::raw('day(course_results.completion_date)'), $day)
            ->where(\DB::raw('month(course_results.completion_date)'), $month)
            ->where(\DB::raw('year(course_results.completion_date)'), $year)->first();
        }


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

    public function handle($params)
    {
        $id     = $params['id'];
        $type   = $params['type'];
        $filter = $params['filter'];
        $export = $params['export'];

     if($filter == "archive")
     {
       $user = User::withTrashed()->where('id', $id)->first();
       $role = Role::withTrashed()->where("id", $user->role_id)->first() ;
       $courseAssignment = CourseUser::withTrashed()->where("user_id", $id)->pluck("id");
       $courseUsers = CourseUser::withTrashed()->where("user_id", $id)->get();
     }else {
       $user = User::find($id);
       $role = Role::where("id", $user->role_id)->first() ;
       $courseAssignment = CourseUser::where("user_id", $id)->pluck("id");
       $courseUsers = CourseUser::where("user_id", $id)->get();
     }


  //    $completedCourse = CourseResult::whereIn('courseuser_id',$courseAssignment)->where("score", 100)->count();

    //  $courseProgress = CourseResult::whereIn('courseuser_id',$courseAssignment)->where("score", '>', 0)->where("score", '<', 0)->count();


      $completedCourse = $courseCompletionCount= $inComplete = 0 ;
      $courseCompletedIds = array();
      for($cu=0;$cu < count($courseUsers); $cu++)
      {

        $courseComResult = course_completion_rules_result($courseUsers[$cu]->course_id, $courseUsers[$cu]->user_id, $filter);

        if($courseComResult['complete'] == 1){
          $completedCourse++;
          $courseCompletedIds[] = $courseUsers[$cu]->course_id;
        }
        else {
          $inComplete++;
        }
        $result =  $this->courseCompletionRulesResult(
                            $courseUsers[$cu]->course_id,
                            $courseUsers[$cu]->user_id,
                            date('d'),
                            date('m'),
                            date('Y'),
                            $filter
                    );

        if($result['complete'] == 1)
          $courseCompletionCount++;

      }


      $totalPassScore = 0 ;
      $remaining = 100 ;

       if($courseAssignment && count($courseAssignment) > 0)
       {
        $totalScore = count($courseAssignment);

        $totalPassScore = ($completedCourse * 100 )/$totalScore;
        $remaining = 100 - $totalPassScore;
      }


      if($filter == "archive")
      {
        $courseProgress = CourseResult::join('course_users', 'course_users.id', 'course_results.courseuser_id')
        ->withTrashed()
        ->whereNotIn('course_users.course_id', $courseCompletedIds)
        ->whereIn('courseuser_id',$courseAssignment)
        ->where("score", '>', 0)->where("score", '<', 0)->count();


        $courseNotPass = CourseResult::withTrashed()
                          ->whereIn('courseuser_id',$courseAssignment)
                          ->where("satisfied_status", 'Failed')
                          ->count();

      //  $courseResult =  CourseResult::select('total_time')->withTrashed()->whereIn('courseuser_id',$courseAssignment)->get();
        $total_time = "00:00:00";
        $courseResult =  CourseResult::withTrashed()
                          ->select(DB::raw('SEC_TO_TIME( SUM( TIME_TO_SEC( `total_time` ) ) ) AS timeSum'))
                          ->whereIn('courseuser_id', $courseAssignment)
                          ->first();


        $certification = \App\MyCertificate::getCertificates($id)->count();


        $totalUserScore = CourseResult::select(DB::raw("max(course_results.score) as mscore"))
                         ->withTrashed()
                         ->whereIn('courseuser_id',$courseAssignment)
                         ->groupBy('courseuser_id')->get();


      }
      else {
        $courseProgress = CourseResult::join('course_users', 'course_users.id', 'course_results.courseuser_id')
                          ->whereNotIn('course_users.course_id', $courseCompletedIds)
                          ->whereIn('courseuser_id',$courseAssignment)
                          ->where("score", '>', 0)
                          ->where("score", '<', 0)
                          ->count();


        $courseNotPass = CourseResult::whereIn('courseuser_id',$courseAssignment)
                          ->where("satisfied_status", 'Failed')
                          ->count();

      //  $courseResult =  CourseResult::select('total_time')->whereIn('courseuser_id',$courseAssignment)->get();
        $total_time = "00:00:00";
        $courseResult =  CourseResult::select(DB::raw('SEC_TO_TIME( SUM( TIME_TO_SEC( `total_time` ) ) ) AS timeSum'))
                          ->whereIn('courseuser_id', $courseAssignment)
                          ->first();


        $certification = \App\MyCertificate::getCertificates($id)->count();


        $totalUserScore = CourseResult::select(DB::raw("max(course_results.score) as mscore"))
                          ->whereIn('courseuser_id',$courseAssignment)
                          ->groupBy('courseuser_id')
                          ->get();


      }


/*
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

      } */
      if($courseResult && $courseResult->timeSum!="")
         $total_time = $courseResult->timeSum;

      $today = Carbon::now()->format("Y-m-d");

      if($filter == "archive")
      {
      $loginCount = User::where('id', $id)
                  ->withTrashed()
                  ->where(\DB::raw('day(users.last_login_at)'), date('d'))
                  ->where(\DB::raw('month(users.last_login_at)'), date('m'))
                  ->where(\DB::raw('year(users.last_login_at)'), date('Y'))
                  ->count();

      $enrolledCount = \App\CourseUser::where('user_id',$id)
                            ->withTrashed()
                            ->where(\DB::raw('day(course_users.enrol_date)'), date('d'))
                            ->where(\DB::raw('month(course_users.enrol_date)'), date('m'))
                            ->where(\DB::raw('year(course_users.enrol_date)'), date('Y'))
                            ->count();
      }
      else {
        $loginCount = User::where('id', $id)
                    ->where(\DB::raw('day(users.last_login_at)'), date('d'))
                    ->where(\DB::raw('month(users.last_login_at)'), date('m'))
                    ->where(\DB::raw('year(users.last_login_at)'), date('Y'))
                    ->count();

        $enrolledCount = \App\CourseUser::where('user_id',$id)
                              ->where(\DB::raw('day(course_users.enrol_date)'), date('d'))
                              ->where(\DB::raw('month(course_users.enrol_date)'), date('m'))
                              ->where(\DB::raw('year(course_users.enrol_date)'), date('Y'))
                              ->count();
      }

      $yLabel = array($today) ;


  //   $trainingTime = "00:00:00";
    // if($total_time > 0)
      $trainingTime =  $total_time ;//date("H:i:s", $total_time);

      $last_login =  Carbon::parse($user->last_login_at)->format('d-m-Y h:i:s');

      $user_id = encrypt($user->id);


      if($export == "csv")
      {
       if($type == "normal")
       {
        $path = public_path().'/report';
        $filename = "user_statistics_".$user->id.".csv";
        
        if (!file_exists($path)) {
          mkdir($path, 0755, true);
        }

        $handle = fopen($path .'/'. $filename, 'w+');
        fputcsv($handle, array(trans("modules.first_name"), trans("modules.last_name"),
                           trans("modules.email"), trans("modules.course_in_progress"),
                           trans("modules.course_not_complete"),
                           trans("modules.completed_courses"), trans("modules.training_time"),
                           trans("modules.certification"),trans("modules.last_login"), trans("modules.ip_address")
                      ));
        $last_login = "";
      if($user->last_login_at != "")
        $last_login = Carbon::parse($user->last_login_at)->format("d-m-Y h:i:s");

        $user_statistic = array(
          $user->first_name,
          $user->last_name,
          $user->email,
          $courseProgress,
          $courseNotPass,
          $completedCourse,
          $trainingTime,
          $certification,
          $last_login,
          $user->last_login_ip
        );
        fputcsv($handle, $user_statistic);

        fclose($handle);

        $headers = array(
            'Content-Type' => 'text/csv',
        );

        return response()
                ->download($path.'/'.$filename, 'user_statistics.csv')
                ->deleteFileAfterSend(true);
       }
       else if($type == "course")
       {
         if($filter == "archive")
         {
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
            ->leftjoin('modules', 'modules.id','course_results.module_id')
            ->where('course_users.user_id',$id)->get();
          }
        else {
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
            ->leftjoin('modules', 'modules.id','course_results.module_id')
            ->where('course_users.user_id',$id)->get();
        }

        $path = public_path().'/report';
        
        if (!file_exists($path)) {
          mkdir($path, 0755, true);
        }

        $filename = "user_course_statistics_".$user->id.".csv";
        $handle = fopen($path .'/'. $filename, 'w+');

        fputcsv($handle, array(
          trans("modules.first_name"),
          trans("modules.last_name"),
          trans("modules.course_title"),
          trans("modules.module"),
          trans("modules.enrolled_date"),
          trans("modules.start_date"),
          trans("modules.complete_status"),
          trans("modules.satisfied_status"),
          trans("modules.completion_date"),
          trans("modules.score"),
          trans("modules.total_time"),
          trans("modules.duration")
        ));


        for ($uc = 0; $uc < count($userCourse); $uc++) {
      //    $last_login = Carbon::parse($user->last_login)->format("d-m-Y h:i:s");
          if ($userCourse[$uc]->enrol_date != "") {
            $userCourse[$uc]->enrol_date = $userCourse[$uc]->enrol_date ? Carbon::parse($userCourse[$uc]->enrol_date)->format("d-m-Y h:i:s") : '';
          }

          if ($userCourse[$uc]->start_date != "") {
            // $userCourse[$uc]->start_date = Carbon::parse($userCourse[$uc]->start_date)->format("d-m-Y");
          }

          if ($userCourse[$uc]->completion_date != "") {
            $userCourse[$uc]->completion_date = $userCourse[$uc]->completion_date ? Carbon::parse($userCourse[$uc]->completion_date)->format("d-m-Y") : '';
          }

          $user_statistic = array(
            $user->first_name,
            $user->last_name,
            $userCourse[$uc]->title,
            $userCourse[$uc]->mod_title,
            $userCourse[$uc]->enrol_date,
            $userCourse[$uc]->start_date,
            $userCourse[$uc]->complete_status,
            $userCourse[$uc]->satisfied_status,
            $userCourse[$uc]->completion_date,
            $userCourse[$uc]->score,
            $userCourse[$uc]->total_time,
            $userCourse[$uc]->duration
          );
          
          fputcsv($handle, $user_statistic);
        }


        fclose($handle);

        $headers = array(
            'Content-Type' => 'text/csv',
        );

        return response()
                ->download($path.'/'.$filename, 'user_course_statistics.csv')
                ->deleteFileAfterSend(true);
       }
     }

      return compact(
        'user_id',
        'user',
        'role',
        'last_login',
        'totalPassScore',
        'remaining',
        'loginCount',
        'courseCompletionCount',
        'yLabel',
        'courseProgress',
        'courseNotPass',
        'completedCourse',
        'trainingTime',
        'certification',
        'enrolledCount',
        'filter'
      );
    }
}