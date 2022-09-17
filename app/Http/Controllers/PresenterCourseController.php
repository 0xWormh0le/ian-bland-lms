<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Course;
use App\CourseMember;
use App\CourseUser;
use App\CourseResult;
use App\Module;
use App\Elearning;
use App\ElearningUser;
use Yajra\Datatables\Datatables;
use Auth, Alert;
use App\SCORMDispatchAPI\SCORMDispatchService;
use BigBlueButton\BigBlueButton;
use BigBlueButton\Parameters\CreateMeetingParameters;
use BigBlueButton\Parameters\JoinMeetingParameters;
use BigBlueButton\Parameters\EndMeetingParameters;
use BigBlueButton\Parameters\GetMeetingInfoParameters;
use BigBlueButton\Parameters\GetRecordingsParameters;

class PresenterCourseController extends Controller
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
        $title = trans('controllers.presenter_schedule');
        $schedules = Module::getPresenterClass();
        return view('presenter-courses.index', compact('title','schedules'));
    }

    /**
     * Webex Detail.
     *
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function webex($id)
    {
      /*
        $id = decrypt($id);
        $webex = Webex::find($id);

        if($webex)
        {
            // Set SCORM Registration ID for each Elearning modules
            $elearnings = Elearning::where('course_id', $webex->course_id)
                                    ->get();
            $elearningUserIds = [];
            foreach($elearnings as $elearning)
            {
                $elearningUser = ElearningUser::where('course_id', $webex->course_id)
                                                ->where('company_id', $webex->company_id)
                                                ->where('elearning_id', $elearning->id)
                                                ->where('module_id', $webex->module_id)
                                                ->where('user_id', Auth::id())
                                                ->where('is_presenter', true)
                                                ->first();
                if(!$elearningUser)
                    $elearningUser = new ElearningUser;
                if(!$elearning->scorm_regid)
                {
                    $regId = md5($webex->id);
                    $courseId = $elearning->scorm_id;
                    $learnerId = Auth::user()->email;
                    $learnerFirstName = Auth::user()->first_name;
                    $learnerLastName = Auth::user()->last_name;

                    $scormService = new SCORMDispatchService;
                    $courseService = $scormService->getCourseService();
                    if($courseService->CreateRegistration(
                        $regId, $courseId, $learnerId, $learnerFirstName, $learnerLastName)
                    )
                    {
                        $elearningUser->course_id = $webex->course_id;
                        $elearningUser->company_id = $webex->company_id;
                        $elearningUser->elearning_id = $elearning->id;
                        $elearningUser->module_id = $webex->module_id;
                        $elearningUser->user_id = Auth::id();
                        $elearningUser->is_presenter = true;
                        $elearningUser->scorm_regid = $regId;
                        $elearningUser->save();
                    }
                }
                $elearningUserIds[$elearning->id] = $elearningUser->id;
            }

            $title = trans('controllers.presenter_schedule').' - '. $webex->title;
            $breadcrumbs = [
                route('presenter-courses.index') => trans('controllers.presenter_schedule'),
                '' => $webex->title,
            ];
            return view('presenter-courses.webex', compact('title', 'breadcrumbs', 'webex', 'elearnings', 'elearningUserIds'));
        }
        Alert::error(__('messages.invalid_request'))->autoclose(3000);
        return redirect()->route('presenter-courses.index');
        */
    }


    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function webexUsers($webex_id)
    {
      /*
        $webex = Webex::find($webex_id);
        $members = CourseMember::where('course_id', $webex->course_id)
                                ->where('company_id', $webex->company_id)
                                ->pluck('id');
        $users = CourseUser::select(
                        'course_users.user_id','users.first_name', 'users.last_name'
                        )
                        ->leftJoin('users', 'users.id', '=', 'course_users.user_id')
                        ->whereIn('course_member_id', $members);

        return Datatables::of($users)

            ->addColumn('join', function ($users) use($webex) {
                $webexUser = WebexUser::where('webex_id', $webex->id)
                                ->where('user_id', $users->user_id)
                                ->first();
                if($webexUser){
                    if($webexUser->status == 'Join')
                        return '<span class="badge badge-success">'.$webexUser->status.'</span>';
                    elseif($webexUser->status == 'Logout')
                        return '<span class="badge badge-secondary">'.$webexUser->status.'<br/> '.trans('controllers.at').' '.$webexUser->ended_at.'</span>';
                }
                else
                    return '<span class="badge badge-danger">'.trans('controllers.not_join').'</span>';
            })
            ->addColumn('status', function ($users) use($webex) {
                $courseUser = CourseUser::where('user_id', $users->user_id)
                                ->where('course_id', $webex->course_id)
                                ->first();
                if($courseUser)
                {
                    $courseResult = CourseResult::where('courseuser_id', $courseUser->id)
                                                ->where('module_id', $webex->module_id)
                                                ->first();
                }
                if(@$courseResult && $courseResult->satisfied_status == 'passed')
                    return '<span class="badge badge-success">'.trans('controllers.passed').'</span>';
                else
                    return '<span class="badge badge-danger">'.trans('controllers.not_passed').'</span>';
            })
            ->addColumn('action', function ($users) {
                return '<input type="checkbox" name="webexUserId[]" value="'.$users->user_id.'">';
            })
            ->rawColumns(['join','status','action'])
            ->make(true);
            */
    }

    /**
     * Update Webex User status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function webexUserUpdate(Request $request, $webex_id)
    {
       /*
        $webex = Webex::find($webex_id);

        $userIds = json_decode($request->userIds, true);
        foreach($userIds as $user_id)
        {
            $courseUser = CourseUser::where('user_id', $user_id)
                                ->where('course_id', $webex->course_id)
                                ->first();
            if($courseUser)
            {
                $courseResult = CourseResult::where('courseuser_id', $courseUser->id)
                                            ->where('module_id', $webex->module_id)
                                            ->first();
                if(!$courseResult)
                    $courseResult = new CourseResult;
                $courseResult->courseuser_id = $courseUser->id;
                $courseResult->module_id = $webex->module_id;
                if(@$courseResult->satisfied_status !== 'passed')
                {
                    $courseResult->complete_status = 'completed';
                    $courseResult->satisfied_status = 'passed';
                    $courseResult->completion_date = date('Y-m-d H:i:s');
                }else{
                    $courseResult->complete_status = '';
                    $courseResult->satisfied_status = '';
                    $courseResult->completion_date = null;
                }
                $courseResult->updated_by = Auth::id();
                $courseResult->save();

                CourseUser::updateResult($webex->course_id, $user_id);
            }
        }
        return json_encode(['status' => 'success']);
        */
    }


    /**
     * Handle Module Launch
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function launchWebex(Request $request)
    {
      /*
        $webex = Webex::find($request->id);
        $module = $webex->module;

        $bbb = new BigBlueButton();
        $meetingID = $module->slug.$webex->id;
        $name = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        $password = md5($webex->id);

        $getMeetingInfoParams = new GetMeetingInfoParameters($meetingID, '', $password);
        $response = $bbb->getMeetingInfo($getMeetingInfoParams);
        if ($response->getReturnCode() == 'FAILED') {

            $meetingName = $webex->title;
            $urlLogout = route('webex.launch.save', encrypt($webex->id));
            $duration = $webex->duration;
            if($webex->duration_type == 'Hours')
                $duration = $webex->duration * 60;

            $createMeetingParams = new CreateMeetingParameters($meetingID, $meetingName);
            $createMeetingParams->setAttendeePassword($password);
            $createMeetingParams->setModeratorPassword($password);
            $createMeetingParams->setDuration($duration);
            $createMeetingParams->setLogoutUrl($urlLogout);

            $response = $bbb->createMeeting($createMeetingParams);
            if ($response->getReturnCode() == 'FAILED') {
                return trans('controllers.room_not_created');
            }
        }

        $joinMeetingParams = new JoinMeetingParameters($meetingID, $name, $password);
        $joinMeetingParams->setRedirect(true);
        $url = $bbb->getJoinMeetingURL($joinMeetingParams);

        if($url)
        {
            // Register as Webex Online user
            $webexUser = WebexUser::userInfo($webex->id, Auth::id());
            if(!$webexUser)
                $webexUser = new WebexUser;
            $webexUser->webex_id = $webex->id;
            $webexUser->user_id = Auth::id();
            $webexUser->joined_at = date('Y-m-d H:i:s');
            $webexUser->status = 'Join';
            $webexUser->save();

            return json_encode([
                'status' => 'success',
                'url' => $url,
            ]);
        }
        */

    }

    /**
     * Handle Module Launch Save
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function webexLaunchSave($id)
    {
      /*  $webex = Webex::find(decrypt($id));

        $webexUser = WebexUser::where('webex_id', $webex->id)
                                ->where('user_id', Auth::id())
                                ->first();
        $webexUser->status = 'Logout';
        $webexUser->ended_at = date('Y-m-d H:i:s');
        $webexUser->save();

        if(Auth::id() !== $webex->instructor_user_id)
            return redirect()->route('my-courses.show', $webex->course->slug);
        return redirect()->route('presenter-courses.webex', $id);
        */
    }




    /**
     * Handle Module Launch
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function launchElearning(Request $request)
    {
        $webex = Webex::find($request->webex_id);
        $elearning = Elearning::find($request->id);
        if($elearning && $webex)
        {
            $elearningUser = ElearningUser::getPresenterId($elearning->course_id, $webex->company_id, $elearning->id, Auth::id(), $webex->module_id);

            if($elearningUser)
            {
                $redirectUrl = route('presenter-courses.launch.elearning.save', [
                    'webex_id' => encrypt($webex->id),
                    'id' => encrypt($elearning->id),
                ]);

                $scormService = new SCORMDispatchService;
                $courseService = $scormService->getCourseService();
                $launchUrl = $courseService->GetLaunchUrl($elearningUser->scorm_regid,$redirectUrl);
                if($launchUrl)
                    return json_encode([
                        'status' => 'success',
                        'url' => $launchUrl,
                    ]);
            }
        }

        return json_encode([
            'status' => 'error',
            'msg' => __('messages.invalid_request')
        ]);
    }

    /**
     * Course Result Feedback.
     *
     * @param string $webex_id
     * @param string $elearning_id
     * @return \Illuminate\Http\Response
     */
    public function launchElearningSave($webex_id, $elearning_id)
    {
        $webex_id = decrypt($webex_id);
        $elearning_id = decrypt($elearning_id);
        $webex = Webex::find($webex_id);
        $elearning = Elearning::find($elearning_id);
        if($webex && $elearning)
        {
            $elearningUser = ElearningUser::getPresenterId($elearning->course_id, $webex->company_id, $elearning->id, Auth::id(), $webex->module_id);


            if($elearningUser && $elearningUser->scorm_regid)
            {
                $scormService = new SCORMDispatchService;
                $courseService = $scormService->getCourseService();
                $scormResult = $courseService->GetRegistrationResult($elearningUser->scorm_regid);
                $elearningUser->complete_status = $scormResult->getComplete();
                $elearningUser->satisfied_status = $scormResult->getSuccess();
                $elearningUser->total_time = $scormResult->getTotalTime();
                $elearningUser->score = $scormResult->getScore();
                if($scormResult->getComplete() == 'completed')
                    $elearningUser->completion_date = date('Y-m-d H:i:s');
                if($elearningUser->save())
                {
                    Alert::success('Elearning result saved')->autoclose(3000);
                    return redirect()->route('presenter-courses.webex', encrypt($webex_id));
                }
            }
        }
    }


    /**
     * Classroom Detail.
     *
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function classroom($id)
    {
      /*  $id = decrypt($id);
        $classroom = Classroom::find($id);

        if($classroom)
        {
            $title = trans('controllers.presenter_schedule') .' - '. $classroom->title;
            $breadcrumbs = [
                route('presenter-courses.index') => trans('controllers.classroom_schedules'),
                '' => $classroom->title,
            ];
            return view('presenter-courses.classroom', compact('title', 'breadcrumbs', 'classroom'));
        }
        Alert::error(__('messages.invalid_request'))->autoclose(3000);
        return redirect()->route('presenter-courses.index');*/
    }


    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function classroomUsers($classroom_id)
    {
      /*
        $classroom = Classroom::find($classroom_id);
        $members = CourseMember::where('course_id', $classroom->course_id)
                                ->where('company_id', $classroom->company_id)
                                ->pluck('id');
        $users = CourseUser::select(
                        'course_users.user_id','users.first_name', 'users.last_name'
                        )
                        ->leftJoin('users', 'users.id', '=', 'course_users.user_id')
                        ->whereIn('course_member_id', $members);

        return Datatables::of($users)

            ->addColumn('status', function ($users) use($classroom) {
                $courseUser = CourseUser::where('user_id', $users->user_id)
                                ->where('course_id', $classroom->course_id)
                                ->first();
                if($courseUser)
                {
                    $courseResult = CourseResult::where('courseuser_id', $courseUser->id)
                                                ->where('module_id', $classroom->module_id)
                                                ->first();
                }
                if(@$courseResult && $courseResult->satisfied_status == 'passed')
                    return '<span class="badge badge-success">'.trans('controllers.passed').'</span>';
                else
                    return '<span class="badge badge-danger">'.trans('controllers.not_passed').'</span>';
            })
            ->addColumn('action', function ($users) {
                return '<input type="checkbox" name="classroomUserId[]" value="'.$users->user_id.'">';
            })
            ->rawColumns(['join','status','action'])
            ->make(true);
            */
    }

    /**
     * Update Webex User status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function classroomUserUpdate(Request $request, $classroom_id)
    {
      /*
        $classroom = Classroom::find($classroom_id);

        $userIds = json_decode($request->userIds, true);
        foreach($userIds as $user_id)
        {
            $courseUser = CourseUser::where('user_id', $user_id)
                                ->where('course_id', $classroom->course_id)
                                ->first();
            if($courseUser)
            {
                $courseResult = CourseResult::where('courseuser_id', $courseUser->id)
                                            ->where('module_id', $classroom->module_id)
                                            ->first();
                if(!$courseResult)
                    $courseResult = new CourseResult;
                $courseResult->courseuser_id = $courseUser->id;
                $courseResult->module_id = $classroom->module_id;
                if(@$courseResult->satisfied_status !== 'passed')
                {
                    $courseResult->complete_status = 'completed';
                    $courseResult->satisfied_status = 'passed';
                    $courseResult->completion_date = date('Y-m-d H:i:s');
                }else{
                    $courseResult->complete_status = '';
                    $courseResult->satisfied_status = '';
                    $courseResult->completion_date = null;
                }
                $courseResult->updated_by = Auth::id();
                $courseResult->save();

                CourseUser::updateResult($classroom->course_id, $user_id);
            }
        }
        return json_encode(['status' => 'success']);
        */
    }

}
