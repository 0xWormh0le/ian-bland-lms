<?php namespace lms\Http\Controllers\Scorm;

use Illuminate\Support\MessageBag;
use lms\Http\Controllers\Controller;
use DB;
use Response;
use Input;
use Exception;

class ReportController extends Controller{
 	
	/**
	 * Display a listing of the report.
	 *
	 * @return Response
	 */

	public function index() {

        header('Access-Control-Allow-Origin: *');

		$reports = DB::table(DB::getTablePrefix().'scorm_report')
				->leftJoin('scorm', 'scorm_report.course', '=', 'scorm.id')
				->leftJoin('users', 'users.user_id', '=', 'scorm_report.user')
	            ->select('scorm.scormname', 'scorm.format', 'scorm_report.complete_status', 
	            	'scorm_report.satisfied_status', 'scorm_report.score', 'scorm_report.total_time', 'scorm_report.attempt', 'users.first_name', 'users.last_name')
	            ->get();
		
        return Response::json(array(
            'error' => FALSE,
            'reports' => $reports),
            200
        );
	}

    /**
     * Display the specified report.
     *
     * @param  int  $user
     * @return Response
     */
	public function show($user) {
        header('Access-Control-Allow-Origin: *');

		$reports = DB::table(DB::getTablePrefix().'scorm_report')
				->leftJoin('scorm', 'scorm_report.course', '=', 'scorm.id')
				->leftJoin('users', 'users.user_id', '=', 'scorm_report.user')
				->where('scorm_report.user', $user)
	            ->select('scorm.scormname', 'scorm.format', 'scorm_report.complete_status', 
	            	'scorm_report.satisfied_status', 'scorm_report.score', 'scorm_report.total_time', 'scorm_report.attempt', 'users.first_name', 'users.last_name')
	            ->get();
        return Response::json(array(
            'error' => FALSE,
            'reports' => $reports),
            200
        );	      
        
	}

	public function searchUserAction($id) {
		header('Access-Control-Allow-Origin: *');
		
		if (Input::get('completion_status') != null)
			$completed = Input::get('completion_status');
		else
			$completed = "";

		if (Input::get('success_status') != null)
			$success = Input::get('success_status');
		else
			$success = "";

		if (Input::get('courses') != null)
			$courses = Input::get('courses');
		else
			$courses = "";
		
		if (Input::get('score_f') != null)
			$score_f = Input::get('score_f');
		else
			$score_f = "";

		if (Input::get('score_t') != null)
			$score_t = Input::get('score_t');
		else
			$score_t = "";
        if (Input::get('users') != null)
            $users = Input::get('users');
        else
            $users = [];
		try {
			$message = "";
			$query = DB::table(DB::getTablePrefix().'scorm_report AS scorm_report');
					$query->leftJoin(DB::getTablePrefix().'scorm AS scorm', 'scorm_report.course', '=', 'scorm.id');
					$query->leftJoin(DB::getTablePrefix().'users AS users', 'users.user_id', '=', 'scorm_report.user');

					if (Input::get('users') != null)
						$query->whereIn('scorm_report.user', $users);

					if (Input::get('completion_status') != null)
						$query->whereIn('scorm_report.complete_status', $completed);

					if (Input::get('success_status') != null)
					{
						$query->orWhereIn('scorm_report.satisfied_status', $success);
		        	}

					if (Input::get('courses') != null)
						$query->whereIn('scorm_report.course', $courses);

					if (Input::get('score_f') != "" && Input::get('score_t') == "")
						$query->where('scorm_report.score', ">", $score_f);
					else if (Input::get('score_t') != "" && Input::get('score_f') == "")
						$query->where('scorm_report.score', "<", $score_f);	
					else if (Input::get('score_f') != "" && Input::get('score_t') != "")
						$query->whereBetween('scorm_report.score', array($score_f, $score_f));		

					$query->where('scorm_report.user', $id);											

		            $query->select('scorm.scormname', 'scorm.format', 'scorm_report.complete_status', 
		            	'scorm_report.satisfied_status', 'scorm_report.score', 'scorm_report.total_time', 'scorm_report.attempt', 'users.first_name', 'users.last_name');
		    $reports = $query->get();
	    } catch (Exception $ex) {
	    	$message = $ex;
	    }
        return Response::json(array(
        	'message' => $message,
            'error' => FALSE,
            'reports' => $reports),
            200
        );
	}

    /**
     * Display the specified report.
     * @return Response
     */
	public function searchAction() {

		header('Access-Control-Allow-Origin: *');
		
		if (Input::get('completion_status') != null)
			$completed = Input::get('completion_status');
		else
			$completed = "";

		if (Input::get('success_status') != null)
			$success = Input::get('success_status');
		else
			$success = "";

		if (Input::get('courses') != null)
			$courses = Input::get('courses');
		else
			$courses = "";

		if (Input::get('users') != null)
			$users = Input::get('users');
		else
			$users = array();

		if (Input::get('score_f') != null)
			$score_f = Input::get('score_f');
		else
			$score_f = "";

		if (Input::get('score_t') != null)
			$score_t = Input::get('score_t');
		else
			$score_t = "";
		try {
			$message = "";
			$query = DB::table(DB::getTablePrefix().'scorm_report');
					$query->leftJoin(DB::getTablePrefix().'scorm AS scorm', 'scorm_report.course', '=', 'scorm.id');
					$query->leftJoin(DB::getTablePrefix().'users AS users', 'users.user_id', '=', 'scorm_report.user');

					if (Input::get('users') != null)
						$query->whereIn('scorm_report.user', $users);

					if (Input::get('completion_status') != null)
						$query->whereIn('scorm_report.complete_status', $completed);

					if (Input::get('success_status') != null)
						$query->whereIn('scorm_report.satisfied_status', $success);

					if (Input::get('courses') != null)
						$query->whereIn('scorm_report.course', $courses);

					if (Input::get('score_f') != "" && Input::get('score_t') == "")
						$query->where('scorm_report.score', ">", $score_f);
					else if (Input::get('score_t') != "" && Input::get('score_f') == "")
						$query->where('scorm_report.score', "<", $score_f);	
					else if (Input::get('score_f') != "" && Input::get('score_t') != "")
						$query->whereBetween('scorm_report.score', array($score_f, $score_f));													

		            $query->select('scorm.scormname', 'scorm.format', 'scorm_report.complete_status', 
		            	'scorm_report.satisfied_status', 'scorm_report.score', 'scorm_report.total_time', 'scorm_report.attempt', 'users.first_name', 'users.last_name');
		    $reports = $query->get();
	    } catch (Exception $ex) {
	    	$message = $ex;
	    }
        return Response::json(array(
        	'message' => $message,
            'error' => FALSE,
            'reports' => $reports),
            200
        );
	}    
}