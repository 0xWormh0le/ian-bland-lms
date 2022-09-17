<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CourseUser;
use App\Module;
use Auth, Alert;
use Carbon\Carbon;

class MyScheduleController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $events = [];

        $mycourses = CourseUser::getByUser(Auth::id());
        foreach($mycourses as $mycourse)
        {
            $course = \App\Course::find($mycourse->course_id);


            if($course && $course->deadline_date)
            {
                $events[] = \Calendar::event(
                            'Deadline of '.$course->title,
                    false,
                     Carbon::createFromFormat('d/m/Y H:i:s', $course->deadline_date.' 00:00:00'),
                     Carbon::createFromFormat('d/m/Y H:i:s', $course->deadline_date.' 23:59:59'),
                    null,
                    [
                        'color' => '#f86c6b',
                        'url' => route('my-courses.show', $course->slug),
                    ]
                );
            }

            if($course && \Auth::user()->company_id)
            {
              $result = \App\CourseCompany::select('deadline')
                                  ->where('company_id', \Auth::user()->company_id)
                                  ->where('active', true)
                                  ->where('course_id', $course->id)
                                  ->first();

             if($result && trim($result->deadline))
               {
                 $duration = explode(" ",$result->deadline);

               if($mycourse && $mycourse->start_date !="")
               {
                 $start_date = str_replace("/","-",$mycourse->start_date);
                 $start = Carbon::createFromFormat('d-m-Y', $start_date)->format('Y-m-d') ;
                 $end = Carbon::createFromFormat('Y-m-d', $start);
               }
               else {
                 $start = Carbon::createFromFormat('Y-m-d H:i:s', $mycourse->enrol_date);
                 $end =  Carbon::createFromFormat('Y-m-d H:i:s', $mycourse->enrol_date);
               }

                switch($duration[1])
                {
                  case 'day' :
                    $end->addDay($duration[0]-1); break;
                  case 'week' :
                    $end->addWeek($duration[0]-1); break;
                  case 'month' :
                    $end->addMonth($duration[0]-1); break;
                  case 'year' :
                   $end->addYear($duration[0]-1); break;
                }


                $events[] = \Calendar::event(
                            trans("modules.deadline_of").' '.$course->title,
                    true,
                     $end,
                     $end,
                    null,
                    [
                        'color' => '#f86c6b',
                        'url' => route('my-courses.show', $course->slug),
                    ]
                );

              }
            }


            if($course && \Auth::user()->company_id)
            {
              $result = \App\CourseCompany::select('deadline')
                                  ->where('company_id', \Auth::user()->company_id)
                                  ->where('active', true)
                                  ->where('course_id', $course->id)
                                  ->first();

    //    print_r(\Auth::user()->company_id."--".$course->id."--".$mycourse->enrol_date) ; die;
               if($result && trim($result->deadline))
               {


                $duration = explode(" ",$result->deadline);

                if($mycourse && $mycourse->start_date !="")
                {
                  $start_date = str_replace("/","-",$mycourse->start_date);
                  $start = Carbon::createFromFormat('d-m-Y', $start_date)->format('Y-m-d') ;
                  $end = Carbon::createFromFormat('Y-m-d', $start);
                }
                else {
                  $start = Carbon::createFromFormat('Y-m-d H:i:s', $mycourse->enrol_date);
                  $end =  Carbon::createFromFormat('Y-m-d H:i:s', $mycourse->enrol_date);
                }

               switch($duration[1])
               {
                 case 'day' :
                       $end->addDay($duration[0]); break;
                 case 'week' :
                       $end->addWeek($duration[0]); break;
                 case 'month' :
                       $end->addMonth($duration[0]); break;
                 case 'year' :
                       $end->addYear($duration[0]); break;
               }


                $events[] = \Calendar::event(
                            $course->title,
                            true,
                            $start,
                            $end,
                            null,
                            [
                                'color' => '#469fc3',
                                'url' => route('my-courses.show', $course->slug),
                            ]
                );

              }
            }


        }


        $calendar = \Calendar::addEvents($events);
        $title = session('menuLabel')['my-schedules'];

        return view('my-schedules.index', compact('calendar', 'title'));
    }



}
