<?php

 	if(!defined('sugarEntry'))define('sugarEntry', true);	

	if(!is_file('config.php')) {	
		require_once('../../../config.php'); // provides $sugar_config
	}

	$sugar_config['dbconfig']['db_host_name'];
	$sugar_config['dbconfig']['db_user_name'];
	$sugar_config['dbconfig']['db_password'];
	$sugar_config['dbconfig']['db_name'];

	$link = mysql_connect($sugar_config['dbconfig']['db_host_name'], $sugar_config['dbconfig']['db_user_name'], $sugar_config['dbconfig']['db_password']);
	if (!$link) {
		die('Could not connect : ' .  mysql_error());
	}
	$db = mysql_select_db($sugar_config['dbconfig']['db_name'], $link);
    
   	global $db;

	$scorm = $_GET["s"];
	$version = $_GET["v"];
	$user = $_GET["u"];
	$course = $_GET["c"];
	$preview = $_GET["p"];
	$id = $_GET["id"];

	if (isset($_GET["m"]) && $_GET["m"] == 'getSCORM') {

		$query = "SELECT * FROM oepl_learning_scorm_track WHERE scorm = '" . $scorm . "' AND user = '" . $user . "'";

		$result = mysql_query($query);

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

		while ( $rows = mysql_fetch_assoc($result) ) {
			switch ($rows['elementname']) {
				case "cmi.core.lesson_status":
				case "cmi.completion_status":
					$lesson_status = $rows['elementvalue'];
				break;
				case "cmi.core.session_time":
				case "cmi.session_time":
					$lesson_total_time = $rows['elementvalue'];
				break;
				case "cmi.core.score.raw":
				case "cmi.score.raw":
					$lesson_score_raw = $rows['elementvalue'];
				break;
				case "cmi.core.score.max":
				case "cmi.score.max":
					$lesson_score_max = $rows['elementvalue'];
				break;
				case "cmi.core.score.min":
				case "cmi.score.min":
					$lesson_score_min = $rows['elementvalue'];
				break;
				case "cmi.success_status":
					$lesson_success_status = $rows['elementvalue'];
				break;	
				case "cmi.suspend_data":
					$lesson_suspend_data = $rows['elementvalue'];
				break;
				case "cmi.core.lesson_mode":
				case "cmi.mode":
					$lesson_mode = $rows['elementvalue'];
				break;
				case "cmi.core.lesson_location":
				case "cmi.location":
					$lesson_location = $rows['elementvalue'];
				case "cmi.core.credit":
				case "cmi.credit":
					$lesson_credit = $rows['elementvalue'];					
				break;
				case "cmi.core.entry":
				case "cmi.entry":
					$lesson_entry = $rows['elementvalue'];					
				break;			
				case "cmi.core.exit":
				case "cmi.exit":
					$lesson_exit = $rows['elementvalue'];					
				break;	
				case "cmi.core.comments":				
					$lesson_comments = $rows['elementvalue'];					
				break;				
				case "cmi.core.launch_data":
				case "cmi.launch_data":
					$lesson_launch_data = $rows['elementvalue'];					
				break;											
			}			
		}

		if (isset($version) && $version == "SCORM_12") {
			$responses = array(
				'cmi_core_student_id' => "",
				'cmi_core_student_name' => "",
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
				'cmi_learner_id' => "liu.yunfei",
				'cmi_learner_name' => "liu.yunfei",
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
	} else if (isset($_GET["m"]) && $_GET["m"] == 'putSCORM') {
		
		try {			
			$query = "SELECT max(attempt) AS a FROM oepl_learning_scorm_track WHERE scorm = '" . $scorm . "' AND user = '" . $user . "'";
			$result = mysql_query($query);
			$row = mysql_fetch_assoc($result);

			if ($row['a'] != null && $row['a'] > 0)
				$attempt = $row['a'];
			else
				$attempt = 1;

			$total_user_score = 0;						
			$passing_score = "";
			$user_results = "Failed";
			$user_scale = 0;

			foreach($_GET as $key=>$value) {
				$cmi_key = str_replace("__", ".", $key);
				if ($cmi_key != 'scorm' && 
					$cmi_key != 'sco' && 
					$cmi_key != 'mode' && 
					$cmi_key != 'user' && 
					$cmi_key != 'vs') {
					$timemodified = date('Y-m-d H:i:s');

					$query = "SELECT 
								trackid
							  FROM 
							  	oepl_learning_scorm_track 
							  WHERE 
							  	scorm = '" . $scorm . "' AND user = '" . $user . "' AND attempt = '" . $attempt . "' AND elementname = '" . $cmi_key . "'";
					$result = mysql_query($query);
					$row = mysql_fetch_assoc($result);

					if ($cmi_key == 'cmi.core.score.raw') {
						$total_user_score = $value;
					} else if ($cmi_key == 'cmi.score.scaled') {
						$total_user_score = $value * 100;
					}

					if ($cmi_key == 'cmi.core.score.max') {
						$passing_score = $value;
					} else if ($cmi_key == 'cmi.score.max') {
						$passing_score = $value;
					}

					if ($cmi_key == 'cmi.core.lesson_status') {
						if ($value == 'completed')
							$user_results = 'Passed';
						else if ($value == 'incomplete')
							$user_results = 'Failed';
						else if ($value == 'passed')
							$user_results = 'Passed';
						else if ($value == 'failed')
							$user_results = 'Failed';
					} else if ($cmi_key == 'cmi.completion_status') {
						if ($value == 'completed')
							$user_results = 'Passed';
						else if ($value == 'incomplete')
							$user_results = 'Failed';						
					} else if ($cmi_key == 'cmi.success_status') {
 						if ($value == 'passed')
							$user_results = 'Passed';
						else if ($value == 'failed')
							$user_results = 'Failed';
					}

					//if ($cmi_key == 'cmi.score.scaled') {
					//	$total_user_score = $value * 100;
					//}

					if ($row['trackid'] > 0 ) {
						$trackid = $row['trackid'];
						$query = "UPDATE 
									oepl_learning_scorm_track 
								  SET 
								  	elementvalue = '$value', attempt = '$attempt'
								  WHERE trackid ='". $trackid . "'";
					} else {
						$query = "INSERT INTO oepl_learning_scorm_track
									(
										scorm,
										user,
										attempt,
										elementname,
										elementvalue,
										timemodified
									)
								 VALUES
									(
										'$scorm',
										'$user',
										'$attempt',
										'$cmi_key',
										'$value',
										'$timemodified'
									)";
					}

					$result = mysql_query($query);
				
				}
			}

			if ($preview == 0) {
				$query_progress = "SELECT 
							id
						  FROM 
						  	oepl_learninguser 
						  WHERE 
						  	learning_id = '" . $course . "' AND a_user_id = '" . $user . "' AND oepl_learninguser.deleted = 0";
		
				$result_progress = mysql_query($query_progress);
				$row_progress = mysql_fetch_assoc($result_progress);

				if ($row_progress['id'] != "" ) {
					$id = $row_progress['id'];


					$sql = "UPDATE 
								oepl_learninguser 
							  SET 
							  	user_percentage = '$total_user_score', user_results = '$user_results', passing_score = '$passing_score', date_modified = '$timemodified'
							  WHERE id ='". $id . "'";
				} else {					
					$sql = "INSERT INTO oepl_learninguser
								(
									id, 
									date_modified,
									deleted,
									a_user_id,
									total_score,
									total_user_score,
									user_percentage,
									user_results,
									learning_id,
									last_exam
								)
							 VALUES
								(
									'$id',
									'$timemodified',
									'0',
									'$user',
									'0',
									'0',
									'$total_user_score',
									'$user_results',
									'$course',
									'0'
								)";
				}
				$result_progress = mysql_query($sql);

				$responses = array(					
					'sql' => $sql,
					'result_progress' => $result_progress,
				); 			

			echo json_encode($responses);

			}

		} catch(Exception $e) {
			echo json_encode($e);
		}
	}
?>

