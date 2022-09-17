<?php namespace App\Http\Controllers\Scorm;

use Illuminate\Support\MessageBag;
use App\Elearning;
use App\Helpers\libs\PclZip;
use App\Helpers\libs\SCORMLib;
use App\Helpers\System\SystemHelper;
use App\Http\Controllers\Controller;


use App\Repositories\CoursesRepo;
use App\Repositories\ElearningRepo;
use App\Repositories\ClassroomsRepo;

use App\SCORM;
use DB;

use App\SCORMReport;
use App\SCORMSCOs;
use App\SCORMTrack;
use App\User;
use Response;
use Exception;
use Config;
use File;
use App\CourseResult;
use App\CourseResultHistory;
use App\CourseUser;

use Illuminate\Support\Facades\Input;


class SCORMController extends Controller{


    /**
	 * Display a listing of the users.
	 *
	 * @return Response
	 */

	public function index() {

        header('Access-Control-Allow-Origin: *');

        $org_id = \Auth::user()->org_id;
        $courses = SCORM::where('org_id', '=', $org_id)->get();

        return Response::json(array(
            'error' => FALSE,
            'courses' => $courses->toArray()),
            200
        );
	}

    /**
     * Display the specified service.
     *
     * @param  int  $id
     * @return Response
     */
	public function show($id) {
		header('Access-Control-Allow-Origin: *');
	    return Response::json(array(
	        'status' => true,
	        'course' => $id),
	        200
	    );
	}

	/**
	* Remove the specified service from storage.
	*
	* @param  int  $id
	* @return Response
	*/
	public function destroy($id) {
		header('Access-Control-Allow-Origin: *');
		try {

            Elearning::where('scorm_id', '=', $id)->delete();
            SCORM::destroy($id);
            /*$scorm = SCORM::find($id);
            $course = $scorm->scormname;
            $scorm->delete();*/
		 	$course = "";
		    return Response::json(array(
		    	'id' => $id,
		    	'course' => $course,
		        'error' => false,
		        'message' => 'The scorm deleted successfully'),
		        200
		    );
		} catch ( Exception $ex) {
		    return Response::json(array(
		        'error' => true,
		        'message' => 'An error occurred while deleting a service - Please try again.'),
		        404
		    );
		}
	}

	public function playSCOAction($id, $sco, $user, $vs) {


		$scorm = SCORM::find($id);
        //$scos = SCORMSCOs::where('scormid', $scorm->id)->where('scoid', $sco)->where('parent', '<>', '/')->first(['launch']);
		$scos = DB::table('scorm_scoes')
				->where('scormid', $scorm->id)
				->where('scoid', $sco)
				->where('parent', '<>', '/')
				->select('launch')
				->get();

		$launch = $scos[0]->launch;
		//$launch = $scos->launch;
		$reference = $scorm->targetid;

        $host = ''; //str_replace("/index.php", "/", route('home'));
		if(strpos(route('home'), '/public/'))
			$host = substr(route('home'), 0, strpos(route('home'), '/public/') + 8 );
		else
			$host = '/';

		//$host = 'http://localhost/lms/public/';//Config::get('scorm.setting.host'); //TODO change URL
		$contents = 'uploads/'.\Auth::user()->org_id.'/';//Config::get('scorm.setting.contents');
		$src = $host . $contents . $reference . "/" . $launch;

		//Date:	Jun 12, 2017				return view("scorm.LMSLoader")->with('src', $src);
		//Fix: 	Pass $vs to enable loading SCORM 1.2 or 2004 API; otherwise wrong API is loaded by some content packages
		//Auth:	John Doyle, Syllametrics, jdoyle@syllametrics.com
		return view("scorm.LMSLoader")->with('src', $src)->with('version', $vs);
		//return $src;
	}

	public function trackAction($id, $sco, $user, $vs, $attempts, $mode) {

		$scorm_id = $id;
		if (isset($mode) && $mode == 'getSCORM') {

			$scoes_track = DB::table(DB::getTablePrefix().'scorm_scoes_track')
						->where('scormid', $id)
						->where('userid', $user)
						->select('*')
						->get();

			$lesson_status = "unknown";
			$lesson_success_status = "unknown";
			$lesson_total_time = "0000:00:00:00";
			$lesson_score_raw = "0.0";
			$lesson_score_max = "";
			$lesson_score_min = "";
			$lesson_suspend_data = "";
			$lesson_exit = "";
			$lesson_mode = "";
			$lesson_location = "";
			$lesson_credit = "";
			$lesson_entry = "";
			$lesson_exit = "";
			$lesson_comments = "";
			$lesson_launch_data = "";
			$lesson_score_scaled = 0;

			$users = User::findOrFail($user);

			for ( $i = 0; $i < count($scoes_track); $i ++ ) {
				switch ($scoes_track[$i]->elementname) {
					case "cmi.core.lesson_status":
					case "cmi.completion_status":
						$lesson_status = $scoes_track[$i]->elementvalue;
					break;
					case "cmi.core.session_time":
					case "cmi.session_time":
						$lesson_total_time = $scoes_track[$i]->elementvalue;
					break;
					case "cmi.core.score.raw":
					case "cmi.score.raw":
						$lesson_score_raw = $scoes_track[$i]->elementvalue;
					break;
					case "cmi.core.score.max":
					case "cmi.score.max":
						$lesson_score_max = $scoes_track[$i]->elementvalue;
					break;
					case "cmi.core.score.min":
					case "cmi.score.min":
						$lesson_score_min = $scoes_track[$i]->elementvalue;
					break;
					case "cmi.success_status":
						$lesson_success_status = $scoes_track[$i]->elementvalue;
					break;
					case "cmi.suspend_data":
						$lesson_suspend_data = $scoes_track[$i]->elementvalue;
					break;
					case "cmi.core.lesson_mode":
					case "cmi.mode":
						$lesson_mode = $scoes_track[$i]->elementvalue;
					break;
					case "cmi.core.lesson_location":
					case "cmi.location":
						$lesson_location = $scoes_track[$i]->elementvalue;
					case "cmi.core.credit":
					case "cmi.credit":
						$lesson_credit = $scoes_track[$i]->elementvalue;
					break;
					case "cmi.core.entry":
					case "cmi.entry":
						$lesson_entry = $scoes_track[$i]->elementvalue;
					break;
					case "cmi.core.exit":
					case "cmi.exit":
						$lesson_exit = $scoes_track[$i]->elementvalue;
					break;
					case "cmi.core.comments":
						$lesson_comments = $scoes_track[$i]->elementvalue;
					break;
					case "cmi.core.launch_data":
					case "cmi.launch_data":
						$lesson_launch_data = $scoes_track[$i]->elementvalue;
					break;
				}
			}

			if (isset($vs) && $vs == "SCORM_12") {

				$responses = array(
					'cmi_core_student_id' => $user,
					//'cmi_core_student_name' => $users[0]->first_name . " " . $users[0]->last_name,
					'cmi_core_student_name' => $users->first_name . " " . $users->last_name,
					'cmi_core_score_raw' => $lesson_score_raw,
					'cmi_core_score_max' => $lesson_score_max,
					'cmi_core_score_min' => $lesson_score_min,
					'cmi_score_scaled' => $lesson_score_scaled,
					'cmi_core_lesson_mode' => $lesson_mode,
					'cmi_core_lesson_location' => $lesson_location,
					'cmi_core_credit' => $lesson_credit,
					'cmi_core_exit' => $lesson_exit,
					'cmi_core_entry' => $lesson_entry,
					'cmi_suspend_data' => $lesson_suspend_data,
					'cmi_core_lesson_status' => $lesson_status,
					'cmi_comments' => $lesson_comments,
					'cmi_core_total_time' => $lesson_total_time,
					'cmi_launch_data' => $lesson_launch_data,
				);

			} else {

				$responses = array(
					'cmi_learner_id' => $user,
					'cmi_learner_name' => $users->first_name . " " . $users->last_name,
					'cmi_score_raw' => $lesson_score_raw,
					'cmi_score_max' => $lesson_score_max,
					'cmi_score_min' => $lesson_score_min,
					'cmi_score_scaled' => $lesson_score_scaled,
					'cmi_mode' => $lesson_mode,
					'cmi_location' => $lesson_location,
					'cmi_credit' => $lesson_credit,
					'cmi_exit' => $lesson_exit,
					'cmi_entry' => $lesson_entry,
					'cmi_suspend_data' => $lesson_suspend_data,
					'cmi_completion_status' => $lesson_status,
					'cmi_success_status' => $lesson_success_status,
					'cmi_total_time' => $lesson_total_time,
					'cmi_launch_data' => $lesson_launch_data,
				);

			}
			echo json_encode($responses);

		} else if (isset($mode) && $mode == 'putSCORM') {

            $scoes_track = [];
            $new_attempt = $attempts;

			try {

				$scoes_track = DB::table(DB::getTablePrefix().'scorm_scoes_track')
				         	->select(DB::raw('max(attempt) as a'))
							->where('scormid', $id)
							->where('userid', $user)
				         	->get();

			} catch(Exception $ex) {
				echo $ex;
			}

			if ($scoes_track[0]->a != null && $scoes_track[0]->a > 0) {
				$attempt = $scoes_track[0]->a;
			} else {
				$attempt = 1;
			}

			$input = Input::all();

			$user_score = 0;
			$passing_score = "";
			$completed_status = "Unknown";
			$satisfied_status = "Unknown";
			$total_time = "00:00:00";
			$session_time = "00:00:00";

			foreach($input as $key=>$value) {

				if ($value == 'null' || is_null($value)) {
					$value = '';
				}

				$cmi_key = str_replace("__", ".", $key);

				if ($cmi_key != 'scorm' &&
					$cmi_key != 'sco' &&
					$cmi_key != 'mode' &&
					$cmi_key != 'user' &&
					$cmi_key != 'vs') {

					if ($cmi_key == 'cmi.score.raw') {
						$user_score = $value;
					} else if ($cmi_key == 'cmi.score.scaled') {
						$user_score = $value * 100;
					}

					if ($cmi_key == 'cmi.core.score.max') {
						$passing_score = $value;
					} else if ($cmi_key == 'cmi.score.max') {
						$passing_score = $value;
					}

					if ($vs == "SCORM_12") {
						// SCORM 1.2
						if ($cmi_key == 'cmi.core.lesson_status') {
							$completed_status = $value;

							if (strtolower($value) == 'failed')
								$completed_status = 'Incomplete';
							else if (strtolower($value) == 'passed')
								$completed_status = 'Completed';

							if (strtolower($completed_status) == "completed") {
								$satisfied_status = "Passed";
							} else if (strtolower($completed_status) == "incomplete") {
								$satisfied_status = "Failed";
							}
						}

						if ($cmi_key == 'cmi.core.score.raw') {
							$user_score = $value;
						}

						if ($cmi_key == 'cmi.core.total_time') {
							$total_time = $value;
						}

					} else {
						// SCORM 2004
						if ($cmi_key == 'cmi.completion_status') {
							$completed_status = $value;
						}

						if ($cmi_key == 'cmi.success_status') {
							$satisfied_status = $value;
						}

						if ($cmi_key == 'cmi.score.scaled') {
							$user_score = $value * 100;
						}

						if ($cmi_key == 'cmi.total_time') {
							$total_time = $value;
						}

						if ($cmi_key == 'cmi.session_time') {
							$session_time = $value;
						}
					}

					$scos_track = DB::table(DB::getTablePrefix().'scorm_scoes_track')
								->where('scormid', $id)
								->where('scoid', $sco)
								->where('userid', $user)
								->where('attempt', $attempt)
								->where('elementname', $cmi_key)
								->select('id')
								->get();

					try {

						if (count($scos_track) > 0 &&
							$scos_track[0]->id > 0 ) {

							$t_id = $scos_track[0]->id;
							$track = SCORMTrack::find($t_id);
							$track->elementvalue = $value;
							$track->attempt = $new_attempt;
							$track->save();

						} else {

							$track = new SCORMTrack();
							$track->scormid = $id;
							$track->userid = $user;
							$track->scoid = $sco;
							$track->attempt = $new_attempt;
							$track->elementname = $cmi_key;
							$track->elementvalue = $value;
							$track->save();
						}

					} catch (Exception $ex) {
						echo json_encode($ex);
					}
				}
			}

			$message = "";

			try {

				if ($vs != "SCORM_12") {
					if ($total_time != "" && $total_time != "00:00:00" && $total_time != "P") {
						$total_time = $this->cmi_ISO80601Duration_sum($total_time, 1);
					} else if ($session_time != "" && $session_time != "00:00:00" && $session_time != "P") {
						$session_time = $this->cmi_ISO80601Duration_sum($session_time, 1);
					}

					if ($total_time == "00:00:00" || $total_time == "P")
						$total_time = $session_time;

					if (strtolower($satisfied_status) == "unknown") {
						if (strtolower($completed_status) == "completed") {
							$satisfied_status = "Passed";
						} else if (strtolower($completed_status) == "incomplete") {
							$satisfied_status = "Failed";
						}
					}
				}

				$scorm_report = DB::table(DB::getTablePrefix().'scorm_report')
		                    ->where('user', $user)
		                    ->where('course', $id)
		                    ->select('id')
							->get();

		        $message =  count($scorm_report);

				if (count($scorm_report) > 0) {
  				$report_id = $scorm_report[0]->id;
					$scorm_report = SCORMReport::find($report_id);
					$scorm_report->complete_status = $completed_status;
					$scorm_report->satisfied_status = $satisfied_status;
					$scorm_report->score = $user_score;
					$scorm_report->total_time = $total_time;
					$scorm_report->attempt = $attempt;
					$scorm_report->save();


				if (!is_null($completed_status) &&
					!is_null($scorm_id) &&
					trim($completed_status) != "" &&
					trim($scorm_id) !="" &&
					is_numeric($scorm_id) &&
					$scorm_id > 0) {

					$elearnings = Elearning::where('scorm_id', $scorm_id)->first();

					if($elearnings->module_id > 0)
					{
					$courseUser = CourseUser::myCourse($elearnings->course_id, \Auth::id());

					\App\CourseUser::updateResult($elearnings->course_id, \Auth::id());


				//	$course_result = new CourseResult();
					$course_result = CourseResult::where(["courseuser_id"=>$courseUser->id, "module_id" => $elearnings->module_id])->first();


					if ($course_result && $course_result->id > 0) {

						$course_result = CourseResult::find($course_result->id);

						$completion_date = "" ;
						if($completed_status == "Completed")
						{
							$completion_date = date("Y-m-d") ;
							$course_result->completion_date = $completion_date;
						}

						$course_result->courseuser_id = $courseUser->id;
						$course_result->module_id = $elearnings->module_id;
						$course_result->scorm_regid = $scorm_id;
						$course_result->complete_status = $completed_status;
						$course_result->satisfied_status = $satisfied_status;
						$course_result->score = $user_score;
						$course_result->total_time = $total_time;


						$course_result->save();

						$course_result_hostory = CourseResultHistory::where("courseresult_id", $course_result->id)
																->where("attempt",$attempts)->first();
						$cdate = null;
						if($completed_status == "Completed" && $completion_date !="")
						{
							$cdate = $completion_date;
						}

						if(!$course_result_hostory)
						{

							CourseResultHistory::insert([
								'courseresult_id' => $course_result->id,
								'complete_status' => $completed_status,
								'satisfied_status' => $satisfied_status,
								'score' =>  $user_score,
								'total_time' => $total_time,
								'attempt' => $attempts,
								'completion_date' => $cdate
							]);
						} else {
							CourseResultHistory::where("courseresult_id", $course_result->id)
												->where("attempt",$attempts)
												->update([
													'courseresult_id' => $course_result->id,
													'complete_status' => $completed_status,
													'satisfied_status' => $satisfied_status,
													'score' =>  $user_score,
													'total_time' => $total_time,
													'attempt' => $attempts,
													'completion_date' => $cdate
												]);
						}

						$lastResult = \App\CourseUser::where('course_id', $elearnings->course_id)->where('user_id', \Auth::id())->first();
						if ($completed_status == "Completed" && $lastResult->notify == 0 ) {
							$resultArr = course_completion_rules_result($elearnings->course_id, \Auth::id());

							if($resultArr['complete'] == 1)
							{
									$companyNotificationStatus = \App\CourseCompany::select('completion_notification')
																		->where('course_id', $elearnings->course_id)
																		->where('company_id', \Auth::user()->company_id)->first();
									if($companyNotificationStatus->completion_notification == "on") {
										$data = [
											'user_id' => auth()->user()->id,
											'course_id' => $elearnings->course_id
										];
										dispatch(new \App\Jobs\SendEmail($data, 'CourseCompletion'));
									}


									\App\CourseUser::where('course_id', $elearnings->course_id)
												->where('user_id', \Auth::id())
												->update(['notify' => 1]);

									$config = \App\CourseConfig::select('learning_path')
																->where('course_id',$elearnings->course_id)
																->first();


								if($config && $config->learning_path !="")
								{
									$learningCourseIds  = explode(",",$config->learning_path);

									for($l=0;$l<count($learningCourseIds);$l++)
									{

									/*	$companyEnrolled = \App\CourseCompany::where('course_id', $learningCourseIds[$l])
														->where('company_id', \Auth::user()->company_id)->first();

										if(!$companyEnrolled)
										{

											\App\CourseCompany::insert(['company_id'=> \Auth::user()->company_id,
															'course_id'=> $learningCourseIds[$l],
															"created_at" => \Carbon\Carbon::now()
														]);

										}
										*/
										$companyRequest = new \Illuminate\Http\Request([
														'action' => 'enroll',
																	'course_id' => $learningCourseIds[$l],
																	'company_id'   => \Auth::user()->company_id
														]);

										app('App\Http\Controllers\CourseCompanyController')->enroll($companyRequest);


										$request = new \Illuminate\Http\Request([
														'action' => 'enroll',
																	'type' => 'user',
																	'id'   => \Auth::id(),
																	'self_enroll' => 1
														]);

										app('App\Http\Controllers\CourseCompanyController')
											->enrollMember($request, $learningCourseIds[$l], \Auth::user()->company_id);
									}
								}
							}
						} else if ($completed_status != "Completed" ) {
							\App\CourseUser::where('course_id', $elearnings->course_id)
										->where('user_id', \Auth::id())
										->update(['notify' => 0]);
						}
					}
				}
			}
					$this->updateELearning($id, $user, $completed_status, $satisfied_status);

					$responses = array(
						'user' => $user,
						'course' => $id,
						'completed_status' => $completed_status,
						'satisfied_status' => $satisfied_status,
						'score' => $user_score,
						'message' => $message,
					);

					echo json_encode($responses);

				} else {
					$scorm_report = new SCORMReport();
					$scorm_report->user = $user;
					$scorm_report->course = $id;
					$scorm_report->complete_status = $completed_status;
					$scorm_report->satisfied_status = $satisfied_status;
					$scorm_report->score = $user_score;
					$scorm_report->total_time = $total_time;
					$scorm_report->attempt = $attempt;
					$scorm_report->save();

				if (!is_null($completed_status) &&
					!is_null($scorm_id) &&
					trim($completed_status) != "" &&
					trim($scorm_id) !="" &&
					is_numeric($scorm_id) &&
					$scorm_id > 0) {

					$elearnings = Elearning::where('scorm_id', $scorm_id)->first();

					if($elearnings->module_id > 0)
					{

					$courseUser = CourseUser::myCourse($elearnings->course_id, \Auth::id());

					\App\CourseUser::updateResult($elearnings->course_id, \Auth::id());

					//$course_result = new CourseResult();
					$course_result = CourseResult::where(["courseuser_id"=>$courseUser->id, "module_id" => $elearnings->module_id])->first();

					if($course_result && $course_result->id > 0)
					{
					$course_result = CourseResult::find($course_result->id);

					$completion_date = "" ;
					if($completed_status == "Completed")
					 $completion_date = date("Y-m-d") ;

					$course_result->courseuser_id = $courseUser->id;
					$course_result->module_id = $elearnings->module_id;
					$course_result->scorm_regid = $scorm_id;
					$course_result->complete_status = $completed_status;
					$course_result->satisfied_status = $satisfied_status;
					$course_result->score = $user_score;
					$course_result->total_time = $total_time;
					$course_result->completion_date = $completion_date;
					$course_result->save();

					$course_result_hostory = CourseResultHistory::where("courseresult_id", $course_result->id)
					                                            ->where("attempt",$attempts)->first();
					$cdate = null;
					  
					if($completed_status == "Completed" && $completion_date !="")
					{
						$cdate = $completion_date;
					}

					if(!$course_result_hostory)
					 {

						 CourseResultHistory::insert(['courseresult_id' => $course_result->id,
					                                 'complete_status' => $completed_status,
	                                         'satisfied_status' => $satisfied_status,
																					 'score' =>  $user_score,
																					 'total_time' => $total_time,
																					 'attempt' => $attempts,
	                                         'completion_date' => $cdate
																				 ]);
					 }
					 else {


						CourseResultHistory::where("courseresult_id", $course_result->id)
						                   ->where("attempt",$attempts)
															->update(['courseresult_id' => $course_result->id,
																				'complete_status' => $completed_status,
																				'satisfied_status' => $satisfied_status,
																			  'score' =>  $user_score,
																				'total_time' => $total_time,
																				'attempt' => $attempts,
																				'completion_date' => $cdate
																				]);

					 }

					 $lastResult = CourseResult::where('id', $course_result->id)->first();
					if($completed_status == "Completed" && $lastResult->notify == 0 )
					{
						 $resultArr = course_completion_rules_result($elearnings->course_id, \Auth::id());

						if($resultArr['complete'] == 1)
						 {
							 $companyNotificationStatus = \App\CourseCompany::select('completion_notification')->where('course_id', $elearnings->course_id)
 																 ->where('company_id', \Auth::user()->company_id)->first();
 							if($companyNotificationStatus->completion_notification == "on") {
								$data = [
									'user_id' => auth()->user()->id,
									'course_id' => $elearnings->course_id
								];
								dispatch(new \App\Jobs\SendEmail($data, 'CourseCompletion'));
							 }

							 \App\CourseUser::where('course_id', $elearnings->course_id)->where('user_id', \Auth::id())->update(['notify' => 1]);

							 $config = \App\CourseConfig::select('learning_path')->where('course_id',$elearnings->course_id)->first();


						 if($config && $config->learning_path !="")
						 {
								$learningCourseIds  = explode(",",$config->learning_path);

							 for($l=0;$l<count($learningCourseIds);$l++)
							 {

                 /*
								 $companyEnrolled = \App\CourseCompany::where('course_id', $learningCourseIds[$l])
																		->where('company_id', \Auth::user()->company_id)->first();

								 if(!$companyEnrolled)
								 {

									 \App\CourseCompany::insert(['company_id'=> \Auth::user()->company_id,
														'course_id'=> $learningCourseIds[$l],
														"created_at" => \Carbon\Carbon::now()
													]);

								 }
								 */

								 $companyRequest = new \Illuminate\Http\Request([
 									            'action' => 'enroll',
 															'course_id' => $learningCourseIds[$l],
 															'company_id'   => \Auth::user()->company_id
 											    ]);

 								app('App\Http\Controllers\CourseCompanyController')->enroll($companyRequest);


								 $request = new \Illuminate\Http\Request([
															 'action' => 'enroll',
															 'type' => 'user',
															 'id'   => \Auth::id(),
															 'self_enroll' => 1
													 ]);

								 app('App\Http\Controllers\CourseCompanyController')->enrollMember($request, $learningCourseIds[$l], \Auth::user()->company_id);

							 }
						 }
						}
					}
					else if($completed_status != "Completed" ){
						\App\CourseUser::where('course_id', $elearnings->course_id)->where('user_id', \Auth::id())->update(['notify' => 0]);
					 }
				 }



				}
       }
					$this->updateELearning($id, $user, $completed_status, $satisfied_status);

					$responses = array(
						'user' => $user,
						'course' => $id,
						'completed_status' => $completed_status,
						'satisfied_status' => $satisfied_status,
						'score' => $user_score,
						'message' => $message,
					);

					echo json_encode($responses);

				}
			} catch (Exception $ex) {
				$message = $ex;
			}
		}
	}

	private function cmi_ISO80601Duration_sum($duration, $precision = 100) {
	  	$secCount = $this->cmi_ISO8601Duration_to_timestamp($duration, $precision);
		$hours = str_pad(floor($secCount / (60*60)), 2, '0', STR_PAD_LEFT);
		$minutes = str_pad(floor(($secCount - $hours*60*60)/60), 2, '0', STR_PAD_LEFT);
		$seconds = str_pad(floor($secCount - ($hours*60*60 + $minutes*60)), 2, '0', STR_PAD_LEFT);
		return $hours . ":" . $minutes . ":" . $seconds;
	}

	private function cmi_ISO8601Duration_to_timestamp($duration, $precision = 100) {
		$CMI_YEAR = (365*4+1)/4*24*60*60;
		$CMI_MONTH = (365*4+1)/48*24*60*60;
		$CMI_DAY = 24*60*60;
		$CMI_HOUR = 60*60;
		$CMI_MINUTE = 60;
		$CMI_SECOND = 1;

		$pattern = "/P(\\d+Y)?(\\d+M)?(\\d+D)?(T?(\\d+H)?(\\d+M)?(\\d+([.]\\d+)?S)?)?/";
		preg_match($pattern, $duration, $matches);
		$timestamp = 0;

		if ($matches[1] != "")
			$timestamp = $timestamp + ((float)$matches[1] * $precision * $CMI_YEAR);
		else
			$timestamp = $timestamp + (0 * $precision * $CMI_YEAR);

		if ($matches[2] != "")
			$timestamp = $timestamp + ((float)$matches[2] * $precision * $CMI_MONTH);
		else
			$timestamp = $timestamp + (0 * $precision * $CMI_MONTH);

		if ($matches[3] != "")
			$timestamp = $timestamp + ((float)$matches[3] * $precision * $CMI_DAY);
		else
			$timestamp = $timestamp + (0 * $precision * $CMI_DAY);

		if ($matches[5] != "")
			$timestamp = $timestamp + ((float)$matches[5] * $precision * $CMI_HOUR);
		else
			$timestamp = $timestamp + (0 * $precision * $CMI_HOUR);

		if ($matches[6] != "")
			$timestamp = $timestamp + ((float)$matches[6] * $precision * $CMI_MINUTE);
		else
			$timestamp = $timestamp + (0 * $precision * $CMI_MINUTE);

		if ($matches[7] != "")
			$timestamp = $timestamp + ((float)$matches[7] * $CMI_SECOND);
		else
			$timestamp = $timestamp + ($CMI_SECOND);

		return $timestamp;
	}

	private function updateELearning($scorm_id, $user_id, $complete_status, $satisfied_status)
	{
		$completed = 0;

		if ($complete_status == 'completed') {
			$completed = 1;
		}

		if ($satisfied_status == 'passed') {
			$completed = 1;
		}

		$elearnings = Elearning::with('course', 'users')
								->where('scorm_id', $scorm_id)->get();

		foreach ($elearnings as $elearning) {
			/**@var Elearning $elearning */
			$course_id = $elearning->course->id;
			$company_id = \Auth::user()->company_id;
			/*$user = $elearning->course->registrants()->wherePivot('user_id', $user_id)
					->wherePivot('completed', 1)->wherePivot('status', 1)->first();*/

			$user = $elearning->course
						->registrants()
						->wherePivot('user_id', $user_id)
						->wherePivot('active', 1)
						->first();

			if ($user) {
				if ($completed == 1) {
					SystemHelper::finalizeELearning($user_id, $elearning->id);
				} else if (!$elearning->users->contains('id', $user_id)) {
					$elearning->users()->attach($user_id, [
						'company_id' => $company_id,
						'course_id' => $course_id,
						'complete_status' => $completed
					]);
				}
				
				$elearning->users()->detach($user_id);
				$elearning->users()->attach($user_id, [
					'company_id' => $company_id,
					'course_id' => $course_id,
					'complete_status' => $completed
				]);
			}
		}
	}

	public function deliveryAction($id, $user, $module=0) {


		$scorm = SCORM::findOrFail($id);

		$scos = DB::table(DB::getTablePrefix().'scorm_scoes')
				->where('scormid', $scorm->id)
				->where('launch', '<>', '')
				->select('scoid')
				->get();

		$sco = $scos[0]->scoid;

		//$scorm = SCORM::find($id);
        $version = $scorm->format;

		$deliverySCOs = DB::table('scorm_scoes')
				->where('scormid', $scorm->id)
				->where('scoid', $sco)
				->where('parent', '<>', '/')
				->select('launch')
				->get();

		$launch = $deliverySCOs[0]->launch;
		$reference = $scorm->targetid;

        $host = ''; //str_replace("/index.php", "/", route('home'));
		if(strpos(route('home'), '/public/'))
			$host = substr(route('home'), 0, strpos(route('home'), '/public/') + 8 );
		else
			$host = '/';

		//$host = 'http://localhost/lms/public/';//Config::get('scorm.setting.host'); //TODO change URL
		$contents = 'uploads/'.\Auth::user()->org_id.'/';//Config::get('scorm.setting.contents');
		$src = $host . $contents . $reference . "/" . $launch;

		//Date:	Jun 12, 2017				return view("scorm.LMSLoader")->with('src', $src);
		//Fix: 	Pass $vs to enable loading SCORM 1.2 or 2004 API; otherwise wrong API is loaded by some content packages
		//Auth:	John Doyle, Syllametrics, jdoyle@syllametrics.com

		$scoes_track = DB::table(DB::getTablePrefix().'scorm_scoes_track')
		         	->select(DB::raw('max(attempt) as a'))
					->where('scormid', $scorm->id)
					->where('userid', $user)
		         	->get();

		if ($scoes_track[0]->a != null && $scoes_track[0]->a > 0)
			$attempt = $scoes_track[0]->a + 1;
		else
			$attempt = 1;

		$scorm = $scorm->id;
		$data = compact('scorm', 'sco', 'version', 'user', 'attempt');
		$this->setInitialTrack($scorm, $sco, $user, $version, $attempt, $module);

		return view("scorm.LMSLoader")->with('src', $src)->with($data);
	}

	private function setInitialTrack($scorm, $sco, $user, $version, $attempt, $module) {

		$elearnings = Elearning::where('scorm_id', $scorm);

		if ($module > 0) {
			$elearnings->where('module_id', $module);
		}

		$elearnings = $elearnings->first();
		$module = $elearnings->module_id;
		$courseUser = CourseUser::myCourse($elearnings->course_id, \Auth::id());

		if ($courseUser) {
			$course_result = CourseResult::where([
				"courseuser_id" => $courseUser->id,
				"module_id" => $elearnings->module_id
			])->first();

			if ($course_result->complete_status == 'Completed') {
				return;
			}
		}			

		$completed_status = "incomplete";
		$satisfied_status = "unknown";
		$user_score = "0";
		$total_time = "0";

		if ($version == "SCORM_12") {
			$initialData = ["cmi.core.lesson_status" => $completed_status,
			    	"cmi.core.score.raw" => $user_score,
			    	"cmi.core.total_time" => $total_time
			];
		} else {
			$initialData = ["cmi.completion_status" => $completed_status,
					"cmi.success_status" => $satisfied_status,
			    	"cmi.score.raw" => $user_score,
			    	"cmi.total_time" => $total_time
			];
		}

		foreach ($initialData as $key => $value) {
			$sco_track = DB::table(DB::getTablePrefix().'scorm_scoes_track')
				->where('scormid', $scorm)
				->where('scoid', $sco)
				->where('userid', $user)
				->where('elementname', $key)
				->select('id')
				->get();

			if (count($sco_track) > 0 &&
				$sco_track[0]->id > 0 ) {

				$t_id = $sco_track[0]->id;
				$track = SCORMTrack::find($t_id);
				$track->attempt = $attempt;
			} else {
				$track = new SCORMTrack();
				$track->scormid = $scorm;
				$track->userid = $user;
				$track->scoid = $sco;
				$track->attempt = $attempt;
				$track->elementname = $key;
				$track->elementvalue = $value;
			}

			$track->save();
		}

		$scorm_report = DB::table(DB::getTablePrefix().'scorm_report')
                    ->where('user', $user)
                    ->where('course', $scorm)
                    ->select('id')
					->get();

		if (count($scorm_report) > 0) {
			$report_id = $scorm_report[0]->id;
			$scorm_report = SCORMReport::find($report_id);
			$scorm_report->complete_status = $completed_status;
			$scorm_report->satisfied_status = $satisfied_status;
			$scorm_report->score = $user_score;
			$scorm_report->total_time = $total_time;
			$scorm_report->attempt = $attempt;
			$scorm_report->save();

			$this->updateELearning($scorm, $user, $completed_status, $satisfied_status);
		} else {
			$scorm_report = new SCORMReport();
			$scorm_report->user = $user;
			$scorm_report->course = $scorm;
			$scorm_report->complete_status = $completed_status;
			$scorm_report->satisfied_status = $satisfied_status;
			$scorm_report->score = $user_score;
			$scorm_report->total_time = $total_time;
			$scorm_report->attempt = $attempt;
			$scorm_report->save();

			$this->updateELearning($scorm, $user, $completed_status, $satisfied_status);
		}

		if ($courseUser) {
			$course_result = CourseResult::where([
				"courseuser_id" => $courseUser->id,
				"module_id" => $elearnings->module_id
			])->first();

			if ($course_result && $course_result->id > 0) {
				$course_result = CourseResult::find($course_result->id);
			} else {
				$course_result = new CourseResult();
			}

			$course_result->courseuser_id = $courseUser->id;
			$course_result->module_id = $module;// $elearnings->module_id;
			$course_result->scorm_regid = $scorm;
			$course_result->complete_status = $completed_status;
			$course_result->satisfied_status = $satisfied_status;
			$course_result->score = $user_score;
			$course_result->total_time = $total_time;
			$course_result->save();

		/*	$course_result_history = new CourseResultHistory;
			$course_result_history->courseresult_id = $course_result->id;
			$course_result_history->complete_status = $completed_status;
			$course_result_history->satisfied_status = $satisfied_status;
			$course_result_history->score = $user_score;
			$course_result_history->total_time = $total_time;
			$course_result_history->save();*/
		}
	}

    /**
     * Add files and sub-directories in a folder to zip file.
     * @param string $folder
     * @param ZipArchive $zipFile
     * @param int $exclusiveLength Number of text to be exclusived from the file path.
     */
    private static function folderToZip($folder, &$zipFile, $exclusiveLength) {

	  $handle = opendir($folder);
	  
      while (false !== $f = readdir($handle)) {

        if ($f != '.' && $f != '..') {

          $filePath = "$folder/$f";
          // Remove prefix from file path before add to zip.
		  $localPath = substr($filePath, $exclusiveLength);
		  
          if (is_file($filePath)) {
            $zipFile->addFile($filePath, $localPath);
          } else if (is_dir($filePath)) {
            // Add sub-directory.
            $zipFile->addEmptyDir($localPath);
            self::folderToZip($filePath, $zipFile, $exclusiveLength);
          }
        }
	  }
	  
      closedir($handle);
    }

    /*
     * Create a random string
     * @author  XEWeb <>
     * @param $length the length of the string to create
     * @return $str the string
     */
    private static function randomString($length = 6) {
      $str = "";
      $characters = array_merge(range('A','Z'), range('a','z'), range('0','9'));
      $max = count($characters) - 1;
      for ($i = 0; $i < $length; $i++) {
        $rand = mt_rand(0, $max);
        $str .= $characters[$rand];
      }
      return $str;
    }

	private function RemoveExtension($filename) {
	    $file = substr($filename, 0,strrpos($filename,'.'));
	    return $file;
	}

	public function uploadAction() {
		header('Access-Control-Allow-Origin: *');

		/*
        $objElearning = Elearning::where('org_id', \Auth::user()->org_id);

        $course_id = 24;
        if ($course_id != 0)
            $objElearning->where('course_id', $course_id);

        $elearnings = $objElearning->get();

		$objCourse = new \lms\Repositories\CoursesRepo();
		$course = $objCourse->findOrFail($course_id, 'registrants');

		$evaluations = \lms\Evaluation::getData(null, $course_id);
		$objClassRoom = new \lms\Repositories\ClassroomsRepo();
		$classrooms = $objClassRoom->all($course_id);

		$courses = $objCourse->all(false, 'registrants', true, auth()->user()->rawCourses()->lists('course_id')->all());
		//$courses = $objCourse->all(true, 'registrants');
		return view('courses.show', compact('course', 'elearnings', 'evaluations', 'classrooms', 'courses'));
		*/
		include_once(app_path().'/Helpers/libs/scormlib.php');//.'/Helpers/libs/scormlib.php'
		include_once(app_path().'/Helpers/libs/settings.php');//.'/Helpers/libs/settings.php'
		include_once(app_path().'/Helpers/libs/LIB_parse.php');//.'/Helpers/libs/functions.php'


		$scormLib = new SCORMLib();

		$repository = public_path() . '/uploads/';//Config::get('scorm.setting.repository');
		$scorm_assets = public_path() . '/scorm_templates/scorm_12';
		$random_key = $scormLib->create_guid();
		$reference = $random_key;

		$results['status'] = FALSE;
		$results['message'] = "An error occurred while storing the contents - Please try again.";

		//TODO check uploaded file is zip
		//\Request::file('UploadedSCORM')->getClientOriginalExtension()
		$files = $_FILES['UploadedSCORM']['name'];

		$path_parts = pathinfo($_FILES['UploadedSCORM']['name']);
		$extension = strtolower($path_parts['extension']);

		$oldmask = umask(0);

		if (in_array($extension, $allowedExtensions) || $extension == 'zip') {

	        if(!file_exists($repository))
	            mkdir($repository, 0777);

			$src_dir = $repository . \Auth::user()->org_id . '/';

	        if(!file_exists($src_dir))
	            mkdir($src_dir, 0777);

			$file_name = basename($_FILES['UploadedSCORM']['name']);

			//$file_name = basename($_FILES['file']['name']);
			$full_path_name = $src_dir . $reference . '/' . $file_name;

	        if (! mkdir($src_dir . $reference, 0777)) {
	            return false;
	        }

			move_uploaded_file($_FILES['UploadedSCORM']['tmp_name'], $full_path_name);

			if (strtolower($extension) != 'mp4' && strtolower($extension) != 'zip') {
				$destpath = $src_dir . $reference . '/';

				// Convert to mp4 file START
				$ext = pathinfo($file_name, PATHINFO_EXTENSION);
				$name = $this->RemoveExtension($file_name);
				$destname = $src_dir . $reference . '/' . $name . '.mp4';

				$call = "ffmpeg -i " . $full_path_name . "  -vf scale=320:240 -c:v libx264 -preset fast -c:a aac " . $destname;

				include_once(app_path().'/Helpers/libs/ffmpeg_cmd.php');//.'/Helpers/libs/ffmpeg_cmd.php'

				$convert = (popen($convert_call, "r"));


		        if (ob_get_level() == 0)
		            ob_start();

		        while(!feof($convert)) {

		            $buffer = fgets($convert);
		            $buffer = trim(htmlspecialchars($buffer));

		            ob_flush();
		            flush();
		            sleep(0);
		        }

				pclose($convert);
				ob_end_flush();

				$extension = 'mp4';
				$full_path_name = $destname;
			}
			// Convert to mp4 file END

			$scormdir = $this->make_upload_directory($reference, $src_dir);
			$scormdir = $this->cleardoubleslashes($scormdir);
			$file_size = $_FILES['UploadedSCORM']['size'];

			$results['scormdir'] = $scormdir;
			$results['full_path_name'] = $full_path_name;
			$results['size'] = $file_size;

		    $path_parts = pathinfo($this->cleardoubleslashes($full_path_name));

		    $results['zippath'] = $path_parts["dirname"];       //The path of the zip file
		    $results['zipfilename'] = $path_parts["basename"];  //The name of the zip file
		    $results['extension'] = $path_parts["extension"];    //The extension of the file
		    $results['filename'] = $path_parts["filename"];
		    $popup_file = '';
		    $version = 'SCORM_12';

		    $results['status'] = true;

		    if ($path_parts["extension"] == 'mp4') {
		    	$zipFile = $scorm_assets;
		    	$fileTozip = $src_dir . $reference . '/' . str_replace(" ", "", $path_parts["filename"]) . '.zip';
				$files = glob($zipFile . '/*');

				$dest = $src_dir . $reference . '/';

				\Zipper::make($fileTozip)->add($files)->add($dest)->close();

				$full_path_name = $fileTozip;
			}

			if ($this->unzip_file($full_path_name, $scormdir)) {

				$manifest = $repository. \Auth::user()->org_id . '/' . $reference . '/imsmanifest.xml';
				$popup_file = $repository. \Auth::user()->org_id . '/' . $reference . '/index.html';

				$results['reference'] = $manifest;

				if ($extension == 'mp4') {

					$xml_data = file_get_contents($manifest);
					$xml_data = str_replace("%%ORG%%", $_POST["orgname"], $xml_data);
					$xml_data = str_replace("%%TITLE%%", $_POST["title"], $xml_data);
					$xml_data = str_replace("%%VIDNAME%%", $file_name, $xml_data);
					$xml_data = str_replace("%%ASSETID%%", $_POST["assetid"], $xml_data);

					$myfile = fopen($manifest, "w");

					fwrite($myfile, $xml_data);
					fclose($myfile);

					$video_file = $repository. \Auth::user()->org_id . '/' . $reference . '/video.html';
					$video_data = file_get_contents($video_file);
					$video_data = str_replace("%%VIDNAME%%", $file_name, $video_data);
					$video_data = str_replace("%%WIDTH%%", $_POST["width"], $video_data);
					$video_data = str_replace("%%HEIGHT%%", $_POST["height"], $video_data);
					$video_data = str_replace("%%ASSETID%%", $_POST["assetid"], $video_data);

					$myfile = fopen($video_file, "w");

					fwrite($myfile, $video_data);
					fclose($myfile);
				}

	            if (file_exists($manifest)) {
					$validation_manifest = $scormLib->checkManifestFile($manifest);

					if ($validation_manifest->resultFlag) {

						// Adding the SCORM data into lms_scorm table
						try {

							$objSCORM = new SCORM;

	                        $objSCORM->org_id = \Auth::user()->org_id;
	                        $objSCORM->course_id = Input::get("course_id");
							$objSCORM->scormname = $path_parts["filename"];
							$objSCORM->reference = $path_parts["basename"];
							$objSCORM->filesize = $file_size;
							$objSCORM->repository = $repository . \Auth::user()->org_id . '/' . $reference;
							$objSCORM->format = $validation_manifest->version;
							$objSCORM->targetid = $reference;

							if($objSCORM->save()){
	                            $elearning = new Elearning();
	                            $elearning->org_id = $objSCORM->org_id ;
	                            $elearning->course_id = $objSCORM->course_id ;
	                            $elearning->scorm_id = $objSCORM->id ;
	                            $elearning->title = $objSCORM->scormname ;
	                            $elearning->versions = is_numeric($ver = substr($objSCORM->format, stripos($objSCORM->format, 'SCORM_')+6)) ? $ver : null;
	                            $elearning->registrations = 0;
	                            $elearning->created_at = $objSCORM->created_at;
	                            $elearning->updated_at = $objSCORM->updated_at;
	                            $elearning->save();
	                        }

							// Getting the last PK
							$scorm = $objSCORM->id; //DB::getPdo()->lastInsertId();
							$results['status'] = TRUE;
							$results['scorm'] = $scorm;

							$version = $validation_manifest->version;
	                        $launch = $scormLib->parseSCORM($manifest, $scorm);

	                        if ($launch != "")
								$results['message'] = "[ " . $results['zipfilename'] . " ] File Stored Successfully.";
							else
								$results['message'] = "An error occurred while storing the contents - this is mostly an issue with the course you are trying to upload - Please try again.";

						} catch(Exception $ex) {
							$results['message'] = "An error occurred while storing the contents - this is mostly an issue with the course you are trying to upload - Please try again.";
						}

					} else {
						$results['message'] = "An error occurred while validating the contents - this is mostly an issue with the course you are trying to upload - Please try again.";
					}
				} else {
					$results['message'] = "There is no any IMSManifest.XML file - Please try again.";
				}
			}

		} else {
			$results['message'] = "It need to upload SCORM Package zip file or video mp4 file. - Please try again.";
		}

		umask($oldmask);

		if($results['status']) {
			/*
			if ($popup_file != "" && file_exists($popup_file)) {

				$style = "fullscreen=yes";

				$data = file_get_contents($popup_file);
				if ($version == 'SCORM_12')
					$data = str_replace("<title></title>", "<title></title><script src='../../../assets/js/scorm/api.js' type='text/javascript' language='javascript'></script>", $data);
				else
					$data = str_replace("<title></title>", "<title></title><script src='../../../assets/js/scorm/api_13.js' type='text/javascript' language='javascript'></script>", $data);

				$data = str_replace("window.open('popup.html');", "window.open('popup.html', 'SCORM Viewer', '$style');", $data);

				$data = str_replace("window.open(\"popup.html\");", "window.open('popup.html', 'SCORM Viewer', '$style');", $data);

				$myfile = fopen($popup_file, "w");

				fwrite($myfile, $data);
				fclose($myfile);
			}*/

			return redirect()->route('administration.courses.show', [$elearning->course_id])->with('type', 'success')->with('message', $results['message']);
			//return redirect()->route('elearnings.show', [$elearning->course_id, $elearning->id])->with('type', 'success')->with('message', $results['message']);
		}

		return back()->with('type', 'error')->with('message', $results['message']);
        return json_encode($results);
	}

	function unzip_file ($zipfile, $destination = '') {
		//Unzip one zip file to a destination dir
		//Both parameters must be FULL paths
		//If destination isn't specified, it will be the
		//SAME directory where the zip file resides.

	    //Extract everything from zipfile
	    $path_parts = pathinfo($this->cleardoubleslashes($zipfile));
	    $zippath = $path_parts["dirname"];       //The path of the zip file
	    $zipfilename = $path_parts["basename"];  //The name of the zip file
	    $extension = $path_parts["extension"];    //The extension of the file

	    //If no file, error
	    if (empty($zipfilename)) {
	        return false;
	    }

	    //If no extension, error
	    if (empty($extension)) {
	        return false;
	    }

	    //Clear $zipfile
	    $zipfile = $this->cleardoubleslashes($zipfile);

	    //Check zipfile exists
	    if (!file_exists($zipfile)) {
	        return false;
	    }

	    //If no destination, passed let's go with the same directory
	    if (empty($destination)) {
	        $destination = $zippath;
	    }

	    //Clear $destination
	    $destpath = rtrim($this->cleardoubleslashes($destination), "/");

	    //Check destination path exists
	    if (!is_dir($destpath)) {
	        return false;
	    }

	    //Check destination path is writable. TODO!!

	    //Everything is ready:
	    //    -$zippath is the path where the zip file resides (dir)
	    //    -$zipfilename is the name of the zip file (without path)
	    //    -$destpath is the destination path where the zip file will uncompressed (dir)

	    $list = array();
	    include_once(app_path().'/Helpers/libs/pclzip.lib.php');
	    $archive = new PclZip($this->cleardoubleslashes("$zippath/$zipfilename"));

	    /*if (!$list = $archive->extract(PCLZIP_OPT_PATH, $destpath,
	                                   PCLZIP_CB_PRE_EXTRACT, 'unzip_cleanfilename',
	                                   PCLZIP_OPT_EXTRACT_DIR_RESTRICTION, $destpath)) {
	        //return false;
	    }*/
        \Zipper::make($zipfile)->extractTo($destination);
	    return true;
	}

	//Replace 1 or more slashes or backslashes to 1 slash
	function cleardoubleslashes ($path) {
	    return preg_replace('/(\/|\\\){1,}/','/',$path);
	}

	function make_upload_directory($directory, $zipPath) {
		$currdir = $zipPath;
	    umask(0000);
	    if (!file_exists($currdir)) {
	        if (! mkdir($currdir, 0777)) {
	            return false;
	        }
	    }

	    $dirarray = explode('/', $directory);

	    foreach ($dirarray as $dir) {
	        $currdir = $currdir .'/'. $dir;

	        if (! file_exists($currdir)) {
	        	$isResult = mkdir($currdir, 0777);
	            if (!$isResult & is_bool($isResult)) {
	                return false;
	            }
	        }
	    }

	    return $currdir;
	}
}
