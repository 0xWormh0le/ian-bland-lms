<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Course;
use App\Module;
use App\Elearning;
use App\Document;
use App\CourseUser;
use App\CourseResult;
use App\CourseResultHistory;
use App\CourseCategory;
use Auth, Alert, Carbon\Carbon;
use App\SCORMDispatchAPI\SCORMDispatchService;
use BigBlueButton\BigBlueButton;
use BigBlueButton\Parameters\CreateMeetingParameters;
use BigBlueButton\Parameters\JoinMeetingParameters;
use BigBlueButton\Parameters\EndMeetingParameters;
use BigBlueButton\Parameters\GetMeetingInfoParameters;
use BigBlueButton\Parameters\GetRecordingsParameters;

class MyCourseController extends Controller
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

        $title = session('menuLabel')['my-courses'];
        $breadcrumbs = ['' => $title];
        $mycourses = CourseUser::getByUser(Auth::id());

        if(count($mycourses) == 0)
        {
            $deletedcourses = CourseUser::withTrashed()->where('user_id', Auth::id())->get();
            if(count($deletedcourses) == 0)
            {
                // Auto enrolled to team's courses
                $team_id = Auth::user()->team_id;
                if($team_id)
                {
                    $courseMembers = \App\CourseMember::where('team_id', $team_id)->whereNull('user_id')->where('active', true)->get();
                    foreach($courseMembers as $courseMember)
                    {
                      $course = Course::where('id', $courseMember->course_id)->first();
                      if($course)
                      {
                        $courseUser = new CourseUser;
                        $courseUser->course_member_id = $courseMember->id;
                        $courseUser->course_id = $courseMember->course_id;
                        $courseUser->user_id = Auth::id();
                        $courseUser->role = 1;
                        $courseUser->active = true;
                        $courseUser->enrol_date = date('Y-m-d H:i:s');
                        $courseUser->enrolled_by = $courseMember->created_by;
                        $courseUser->created_by = Auth::id();
                        $courseUser->save();
                      }
                    }

                    $mycourses = CourseUser::getByUser(Auth::id());
                }
            }

        }
        $parentCategory = CourseCategory::where("parent", 0)->get();
        return view('my-courses.index', compact('mycourses', 'title', 'breadcrumbs', 'parentCategory'));
    }

    /**
     * Show Course Detail.
     *
     * @param string $slug
     * @return \Illuminate\Http\Response
     */

    public function show(Request $request, $slug)
    {
        try {
            $code = $request->code;

            if ($code) {
                if (decrypt($code) != auth()->user()->email) {
                    return abort(400, 'Invalid token');
                }
            }
        } catch (\Exception $e) {
            return abort(400, 'Invalid token');
        }
        
        try {
            $course = Course::findBySlug($slug);
            CourseUser::updateResult(@$course->id, Auth::id());

            $courseUser = CourseUser::myCourse(@$course->id, Auth::id());

            if ($course && $courseUser) {
                $config = $course->config;
                $modules = Module::getByCourse($course->id);
                $hasDocument = 0 ;
                $course_status = "Incomplete";
                $total_modules = 0;
                $course_module_complete_count = 0;

                foreach ($modules as $module) {
                    $result = CourseResult::getModuleResult($courseUser->id, $module->id);

                    if ($module->type == 'Elearning' &&
                        $result &&
                        $result->complete_status == "Completed") {
                        $course_module_complete_count++ ;
                    }

                    if ($module->type == 'Elearning') {
                        $total_modules++ ;
                        $module->detail = $this->_module($course, $module);
                    }
                    // Register in CourseResult if not exists
                    if (!$result) {
                        $result = new CourseResult;
                        $result->courseuser_id = $courseUser->id;
                        $result->module_id = $module->id;
                        $result->created_by = $courseUser->enrolled_by;
                        $result->save();
                    }

                    if ($module->type == 'Document') {
                        $hasDocument = 1 ;
                    }

                    // Create SCROM Registration if not exists
                    if ($module->type == 'Elearning' && !$result->scorm_regid) {

                        $elearning = Elearning::where('module_id', $module->id)->first();
                        if ($elearning && $elearning->scorm_id) {
                        /*  $scormService = new SCORMDispatchService;
                            $courseService = $scormService->getCourseService();

                            $regId = md5(uniqid(rand(), true));
                            $courseId = $elearning->scorm_id;
                            $learnerId = $courseUser->user->email;
                            $learnerFirstName = $courseUser->user->first_name;
                            $learnerLastName = $courseUser->user->last_name;
                            if($courseService->CreateRegistration(
                                $regId, $courseId, $learnerId, $learnerFirstName, $learnerLastName)
                            )
                            {
                                $result->scorm_regid = $regId;
                                $result->save();
                            }
                            */
                        }
                    }
                }



                $documets = array();
                if ($hasDocument == 1) {
                    $documets = Document::where("course_id", $course->id)
                                        ->where("company_id", \Auth::user()->company_id)
                                        ->get();
                }

                if ($course && \Auth::user()->company_id)  {
                    $result = \App\CourseCompany::select('deadline')
                                    ->where('company_id', \Auth::user()->company_id)
                                    ->where('active', true)
                                    ->where('course_id', $course->id)
                                    ->first();
                    if ($result && $result->deadline != "") {
                        $duration = explode(" ",$result->deadline);

                        if ($courseUser->start_date != "") {
                            $start_date = str_replace("/","-",$courseUser->start_date);
                            $start = Carbon::createFromFormat('d-m-Y', $start_date)->format('Y-m-d') ;
                            $start = Carbon::createFromFormat('Y-m-d', $start);
                        }
                        else {
                            $start = Carbon::createFromFormat('Y-m-d H:i:s',  $courseUser->enrol_date);
                        }

                        switch ($duration[1]) {
                            case 'day' :
                                $start->addDay($duration[0]); break;
                            case 'week' :
                                $start->addWeek($duration[0]);  break;
                            case 'month' :
                                $start->addMonth($duration[0]);  break;
                            case 'year' :
                                $start->addYear($duration[0]);  break;
                        }

                        $course->deadline_date = Carbon::parse($start)->format("d/m/Y");
                    }
                }



        /*     $percentage_complete = "0.00";
            if($total_modules > 0)
                $percentage_complete = $course_module_complete_count * 100 / $total_modules;*/

            $courseStatusResult = course_completion_rules_result($course->id, \Auth::id());
            $percentage_complete = $courseStatusResult['percentage'];

            if ($courseStatusResult['complete'] == 1) {
                $course_status = "Completed" ;
            }

            $title = trans('controllers.my_courses') .' - '. $course->title;
            $breadcrumbs = [
                route('my-courses.index') => trans('controllers.my_courses'),
                '' => $course->title,
            ];

            $course_start_date =$courseUser->start_date;
            $today = \Carbon\Carbon::today()->format("d/m/Y");
            $disable = 0 ;
            
            if ((strtotime($course_start_date) > strtotime($today))  && $courseUser->self_enroll == 0) {
                $disable = 1;
            }

            return view('my-courses.details',
                    compact(
                        'title',
                        'breadcrumbs',
                        'course',
                        'courseUser',
                        'modules',
                        'config',
                        'course_start_date',
                        'disable',
                        'documets',
                        'percentage_complete',
                        'course_status'
                    ));
            }
            Alert::error(__('messages.invalid_request'))->autoclose(3000);
            return redirect()->route('my-courses.index');
        } catch (\Exception $e) {
            return redirect()->route('home');
        }
    }

    protected function _module($course, $module)
    {
        if ($module->type == 'Elearning') {
            $detail = $module->elearning;
        }

        $courseUser = CourseUser::myCourse($course->id, Auth::id());

        $result = CourseResult::getModuleResult($courseUser->id, $module->id);
        $config = $course->config;
        $requiredModules = explode(',', $config->completion_modules);

        $isExpired = false;
        $launch = true;
        $previousModule = collect();

        if ($result && $result->complete_status == 'Completed') {
            $launch = false;
            
            if ($module->type == 'Elearning') {
                $launch = true;
            }
        } else {
            $launch = true;

            if ($launch) {

                // if Course's rule is Sequential, check the previous module completion first
                if ($config->transversal_rule == 'sequential') {
                    $previousModule = Module::where('course_id', $course->id)
                                            ->where('order_no', '<', $module->order_no)
                                            ->orderBy('order_no', 'desc')->first();
                    if ($previousModule) {
                        $previousResult = CourseResult::getModuleResult($courseUser->id, $previousModule->id);
                        if ($previousResult && $previousResult->complete_status == 'Completed') {
                            $launch = true;
                        } else {
                            $launch = false;
                        }
                    } else {
                        $launch = true;
                    }
                }
            }
        }

        return compact(
            'detail',
            'courseUser',
            'result',
            'launch',
            'isExpired',
            'previousModule'
        );
    }

    /**
     * Show Course's module details
     *
     * @param  string  $course_slug
     * @param  string  $module_slug
     * @return \Illuminate\Http\Response
     */
    public function module($course_slug, $module_slug)
    {

        $course = Course::findBySlug($course_slug);
        $module = Module::findBySlug($module_slug);

        if ($course && $module)
        {
            $data = $this->_module($course, $module);
            $detail     = $data['detail'];
            $courseUser = $data['courseUser'];
            $result     = $data['result'];
            $launch     = $data['launch'];
            $isExpired  = $data['isExpired'];
            $previousModule = $data['previousModule'];

            $title = trans('controllers.my_course_module_details')." - ".$module->title;
            $breadcrumbs = [
                route('my-courses.index') => trans('controllers.my_courses'),
                route('my-courses.show', $course->slug) => $course->title,
                '' => $module->title,
            ];

          //  echo "<pre>" ;
          //  print_r($result->histories) ; die;
            return view('my-courses.modules.'.strtolower($module->type),
                    compact(
                        'title',
                        'breadcrumbs',
                        'course',
                        'module',
                        'detail',
                        'courseUser',
                        'result',
                        'launch',
                        'isExpired',
                        'previousModule'
                    ));

        }
        Alert::error(__('messages.invalid_request'))->autoclose(3000);
        return redirect()->route('my-courses.index');
    }


    /**
     * Handle Module Launch
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function launch(Request $request)
    {
        $courseuser_id = $request->id;
        $module = Module::find($request->module_id);

        $courseresult = CourseResult::getModuleResult($courseuser_id, $module->id);
        if($courseresult)
        {
            if($module->type == 'Elearning')
            {
              /*
                if(!$courseresult->scorm_regid)
                {
                    $elearning = Elearning::where('module_id', $module->id)->first();
                    $courseUser = $courseresult->courseuser;
                    if($elearning && $elearning->scorm_id)
                    {
                        $scormService = new SCORMDispatchService;
                        $courseService = $scormService->getCourseService();

                        $regId = md5(uniqid(rand(), true));
                        $courseId = $elearning->scorm_id;
                        $learnerId = $courseUser->user->email;
                        $learnerFirstName = $courseUser->user->first_name;
                        $learnerLastName = $courseUser->user->last_name;
                        if($courseService->CreateRegistration(
                            $regId, $courseId, $learnerId, $learnerFirstName, $learnerLastName)
                        )
                        {
                            $courseresult->scorm_regid = $regId;
                            $courseresult->save();
                        }
                    }
                }

                $redirectUrl = route('my-courses.result', [
                    'slug' => $module->course->slug,
                    'id' => encrypt($courseresult->id),
                ]);

                $scormService = new SCORMDispatchService;
                $courseService = $scormService->getCourseService();
                $launchUrl = $courseService->GetLaunchUrl($courseresult->scorm_regid,$redirectUrl);
                if($launchUrl)
                    return json_encode([
                        'status' => 'success',
                        'url' => $launchUrl,
                    ]);
                    */
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
     * @param string $slug
     * @param string $result_id
     * @return \Illuminate\Http\Response
     */
    public function result($slug, $result_id)
    {


        $result_id = decrypt($result_id);
        $result = CourseResult::find($result_id);
        if($result)
        {
            if($result->scorm_regid)
            {
              /*
                $scormService = new SCORMDispatchService;
                $courseService = $scormService->getCourseService();
                $scormResult = $courseService->GetRegistrationResult($result->scorm_regid);

                $result->complete_status = $scormResult->getComplete();
                $result->satisfied_status = $scormResult->getSuccess();
                $result->total_time = $scormResult->getTotalTime();
                $result->score = $scormResult->getScore();
              */
              /*  if($scormResult->getComplete() == 'completed')
                    $result->completion_date = date('Y-m-d H:i:s');
                    */
                //$result->updated_by = @Auth::id();
                if($result->save())
                {
                    // Attempt log
                    $log = new CourseResultHistory;
                    $log->courseresult_id = $result->id;
                    $log->complete_status = $result->complete_status;
                    $log->satisfied_status = $result->satisfied_status;
                    $log->total_time = $result->total_time;
                    $log->score = $result->score;
                    if($result->complete_status == 'completed')
                        $log->completion_date = $result->completion_date;
                    $log->created_by = $result->created_by;
                    $log->updated_by = $result->updated_by;
                    $log->save();


                    $courseUser = CourseUser::find($result->courseuser_id);
                    if($courseUser)
                        CourseUser::updateResult($courseUser->course_id, $courseUser->user_id);

                    Alert::success(__('messages.result_saved'))->autoclose(3000);
                    return redirect()->route('my-courses.module', ['course' => $result->module->course->slug, 'module'=>$result->module->slug]);
                }
            }
        }
    }

    /**
     * Check if Stream presenter is logged in
     */
    public function getStreamPresenter(Request $request)
    {
        $presenterIsExists = \App\StreamSession::where('module_id', $request->module_id)->where('is_presenter', true)->first();
        if($presenterIsExists)
        {
            $lastOnline = new \DateTime($presenterIsExists->joined_at);
            $expired = new \DateTime('-3hours');
            if($lastOnline < $expired)
            {
                // Session expired, remove record
                $presenterIsExists->delete();
                $presenterIsExists = null;
            }
            else{
                // if presenter is userself
                if($presenterIsExists->user_id == Auth::id())
                    $presenterIsExists = null;
            }
        }

        return json_encode([
            'presenter' => @$presenterIsExists ? true : false
        ]);
    }


    /**
     * Handle Stream Launch
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function streamLaunch(Request $request)
    {
        $courseuser_id = $request->id;
        $module = Module::find($request->module_id);
        $user = Auth::user();
        // Get SCORM REG ID
        $courseresult = CourseResult::getModuleResult($courseuser_id, $module->id);

        if($courseresult)
        {

            if($request->type !== 'webex_only')
            {
                // log as presenter
                $logStream = \App\StreamSession::where('module_id', $module->id)->where('user_id', $user->id)->first();
                if(!$logStream)
                    $logStream = new \App\StreamSession;
                $logStream->company_id = $user->company_id;
                $logStream->user_id = $user->id;
                $logStream->course_id = $module->course_id;
                $logStream->module_id = $module->id;
                $logStream->joined_at = date('Y-m-d H:i:s');
                $logStream->is_presenter = true;
                $logStream->save();

                $scormredirectUrl = route('my-courses.stream.result', [
                    'slug' => $module->course->slug,
                    'id' => encrypt($courseresult->id),
                ]);
            /*    $scormService = new SCORMDispatchService;
                $courseService = $scormService->getCourseService();
                $scormlaunchUrl = $courseService->GetLaunchUrl($courseresult->scorm_regid,$scormredirectUrl);
               */
            }

            if($request->type == 'course_stream' || $request->type == 'webex_only')
            {
                if($request->type == 'webex_only')
                {
                    // log as participant
                    $logStream = \App\StreamSession::where('module_id', $module->id)->where('user_id', $user->id)->first();
                    if(!$logStream)
                        $logStream = new \App\StreamSession;
                    $logStream->company_id = $user->company_id;
                    $logStream->user_id = $user->id;
                    $logStream->course_id = $module->course_id;
                    $logStream->module_id = $module->id;
                    $logStream->joined_at = date('Y-m-d H:i:s');
                    $logStream->is_presenter = false;
                    $logStream->save();
                }

                $bbb = new BigBlueButton();
                $meetingID = $module->slug.$module->id;

                $name = $user->first_name . ' ' . $user->last_name;
                $presenterPassword = md5('presenter'.$module->id);
                $password = md5($module->id);

                $getMeetingInfoParams = new GetMeetingInfoParameters($meetingID, '', $password);
                $response = $bbb->getMeetingInfo($getMeetingInfoParams);
                if ($response->getReturnCode() == 'FAILED') {
                    $meetingName = $module->title;
                    $urlLogout = route('my-courses.module', [
                        'course' => $module->course->slug,
                        'module' => $module->slug,
                    ]);
                    $duration = 0;

                    $createMeetingParams = new CreateMeetingParameters($meetingID, $meetingName);
                    $createMeetingParams->setAttendeePassword($password);
                    $createMeetingParams->setModeratorPassword($presenterPassword);
                    $createMeetingParams->setDuration($duration);
                    $createMeetingParams->setLogoutUrl($urlLogout);

                    $response = $bbb->createMeeting($createMeetingParams);
                    if ($response->getReturnCode() == 'FAILED') {
                        return trans('controllers.room_not_created');
                    }
                }

                if($request->type == 'course_stream')
                    $password = $presenterPassword;

                $joinMeetingParams = new JoinMeetingParameters($meetingID, $name, $password);
                $joinMeetingParams->setRedirect(true);
                $webexUrl = $bbb->getJoinMeetingURL($joinMeetingParams);

            }

            return json_encode([
                'status' => 'success',
                'launchUrl' => @$scormlaunchUrl ?: '',
                'webexUrl' => @$webexUrl ?: ''
            ]);
        }

        return json_encode([
            'status' => 'error',
            'msg' => __('messages.invalid_request')
        ]);
    }

    public function streamResult($slug, $result_id)
    {
        $course = Course::findBySlug($slug);
        $result_id = decrypt($result_id);
        $result = CourseResult::find($result_id);
        $completion_date = date('Y-m-d H:i:s');
        if($result)
        {
            if($result->scorm_regid)
            {
            /*    $scormService = new SCORMDispatchService;
                $courseService = $scormService->getCourseService();
                $scormResult = $courseService->GetRegistrationResult($result->scorm_regid);
             */
                $courseuser_ids = CourseUser::where('course_id', $course->id)->pluck('id');
                foreach($courseuser_ids as $courseuser_id)
                {
                    $courseUser = CourseUser::find($courseuser_id);

                    $course_result = CourseResult::where('courseuser_id', $courseuser_id)
                                        ->where('module_id', $result->module_id)
                                        ->first();
                    if(!$course_result)
                        $course_result = new CourseResult;
                    $course_result->courseuser_id = $courseUser->id;
                    $course_result->module_id = $result->module_id;

                  /*  $course_result->complete_status = $scormResult->getComplete();
                    $course_result->satisfied_status = $scormResult->getSuccess();
                    $course_result->total_time = $scormResult->getTotalTime();
                    $course_result->score = $scormResult->getScore();

                    if($scormResult->getComplete() == 'completed')
                        $course_result->completion_date = $completion_date;
                    $course_result->updated_by = @Auth::id();
                    */
                    if($course_result->save())
                    {
                        // Attempt log
                        $log = new CourseResultHistory;
                        $log->courseresult_id = $course_result->id;
                        $log->complete_status = $course_result->complete_status;
                        $log->satisfied_status = $course_result->satisfied_status;
                        $log->total_time = $course_result->total_time;
                        $log->score = $course_result->score;
                        if($course_result->complete_status == 'completed')
                            $log->completion_date = $course_result->completion_date;
                        $log->created_by = $course_result->created_by;
                        $log->updated_by = $course_result->updated_by;
                        $log->save();

                        $courseUser = CourseUser::find($course_result->courseuser_id);
                        if($courseUser)
                            CourseUser::updateResult($courseUser->course_id, $courseUser->user_id);

                    }
                }

            }
            Alert::success(__('messages.result_saved'))->autoclose(3000);
            return redirect()->route('my-courses.module', ['course' => $slug, 'module'=>$result->module->slug]);
        }
    }

}
