<?php

namespace App\SCORMDispatchAPI;

require_once ('ServiceRequest.php');
require_once ('CourseData.php');
require_once ('ReportSummary.php');
require_once ('RegistrationData.php');
require_once ('LaunchHistoryData.php');

/// <summary>
/// Client-side proxy for the "rustici.course.*" Hosted SCORM Engine web
/// service methods.
/// </summary>
class CourseService {

	private $_configuration = null;

	public function __construct($configuration) {
		$this->_configuration = $configuration;
	}

    /// <summary>
    /// Get the url that can be targeted by a form to upload and import a course into SCORM Cloud
    /// </summary>
    /// <param name="courseId">Unique Course Identifier</param>
    /// <param name="redirectUrl">the url location the browser will be redirected to when the import is finished</param>
    public function GetUploadCourseUrl()
    {
        $request = new ServiceRequest($this->_configuration);
        return $request->ConstructUrl('course/importCourse');
    }

	/// <summary>
    /// Import a SCORM .pif (zip file) from the local filesystem.
    /// </summary>
    /// <param name="courseId">Unique Identifier for this course.</param>
    /// <param name="absoluteFilePathToZip">Full path to the .zip file</param>
    /// <param name="itemIdToImport">ID of manifest item to import. If null, root organization is imported</param>
    /// <param name="permissionDomain">An permission domain to associate this course with,
    /// for ftp access service (see ftp service below).
    /// If the domain specified does not exist, the course will be placed in the default permission domain</param>
    /// <returns>List of Import Results</returns>
    public function CourseUploadAsync($absoluteFilePathToZip)
    {
        $request = new ServiceRequest($this->_configuration);
        $request->setFileToPost($absoluteFilePathToZip);
        
        return $request->CallService("course/importCourse");
    }

    /// <summary>
    /// Get the url that can be opened in a browser and used to preview this course, without
    /// the need for a registration.
    /// </summary>
    /// <param name="courseId">Unique Course Identifier</param>
    /// <param name="versionId">Version Id</param>
    public function DeleteCourse($courseId)
    {
        $request = new ServiceRequest($this->_configuration);
        $params = array('cid' => $courseId);

        $request->SetMethodParams($params);

        return $request->CallService("course/deleteCourse");
    }

    /// <summary>
    /// Get the url that can be opened in a browser and used to preview this course, without
    /// the need for a registration.
    /// </summary>
    /// <param name="courseId">Unique Course Identifier</param>
    /// <param name="versionId">Version Id</param>
    public function GetPreviewUrl($courseId, $redirectOnExitUrl)
    {
        $request = new ServiceRequest($this->_configuration);
        $params = array('cid' => $courseId);

        if(isset($redirectOnExitUrl))
        {
            $params['url'] = $redirectOnExitUrl;
        }

        $request->SetMethodParams($params);

        return $request->ConstructUrl("course/preview");
    }

    /// <summary>
    /// Retrieve a list of high-level data about all courses owned by the
    /// configured appId.
    /// </summary>
 	/// <param name="courseIdFilterRegex">Regular expresion to filter the courses by ID</param>
    /// <returns>List of Course Data objects</returns>
    public function GetCourseList()
    {
        $request = new ServiceRequest($this->_configuration);
        
        $response = $request->CallService("course/getCourseList");        
        $CourseDataObject = new CourseData(null);       
        return $CourseDataObject->ConvertToCourseDataList($response);
    }

    /// <summary>
    /// Return a registration summary object for the given registration
    /// </summary>
    /// <param name="registrationId">The unique identifier of the registration</param>
    /// <returns></returns>
    public function GetCourseSummary($courseId, $redirectOnExitUrl)
    {
        $request = new ServiceRequest($this->_configuration);
        $params = array('cid' => $courseId);
        if(isset($redirectOnExitUrl))
        {
            $params['url'] = $redirectOnExitUrl;
        }            
        $request->setMethodParams($params);

        $response = $request->CallService("course/report");
        
        return new ReportSummary($response);
    }

    /// <summary>
    /// Returns a list of registration id's along with their associated course
    /// </summary>
    /// <param name="courseId>Option course id filter</param>
    /// <param name="learnerId>Option learner id filter</param>
    /// <returns></returns>
    public function GetRegistrationList($courseId, $learnerId)
    {
        $request = new ServiceRequest($this->_configuration);
        $params = array();
        if (isset($courseId))
        {
            $params['cid'] = $courseId;
        }
        if (isset($learnerId))
        {
            $params['lid'] = $learnerId;
        }
        $request->setMethodParams($params);

        $response = $request->CallService("course/getRegistrations");
        $regData = new RegistrationData(null);
        // Return the subset of the xml starting with the top <summary>
        $regArray = $regData->ConvertToRegistrationDataList($response);

        return $regArray;
    }

    /// <summary>
    /// Returns list of launch info objects, each of which describe a particular launch,
    /// but note, does not include the actual history log for the launch. To get launch
    /// info including the log, use GetLaunchInfo
    /// </summary>
    /// <param name="registrationId"></param>
    /// <returns>LaunchHistory XML</returns>
    public function GetLaunchHistory($registrationId)
    {
        $request = new ServiceRequest($this->_configuration);
        $params = array('rid' => $registrationId);
        $request->setMethodParams($params);
        
        $response = $request->CallService("registration/launchHistories");
        $historyData = new LaunchHistory(null);
        $historyArray = $historyData->ConvertToLaunchInfoList($response);
        return $historyArray;
    }

    /// <summary>
    /// Returns the current state of the registration, including completion
    /// and satisfaction type data.  Amount of detail depends on format parameter.
    /// </summary>
    /// <param name="registrationId">Unique Identifier for the registration</param>
    /// <param name="resultsFormat">Degree of detail to return</param>
    /// <returns>Registration data in XML Format</returns>
    public function GetRegistrationResult($registrationId)
    {
        $request = new ServiceRequest($this->_configuration);
        $params = array('rid' => $registrationId);

        $request->setMethodParams($params);
        $response = $request->CallService("registration/report");

        // Return the subset of the xml starting with the top <summary>
        //return $response;
        return new ReportSummary($response);
    }

    /// <summary>
    /// Create a new Registration (Instance of a user taking a course)
    /// </summary>
    /// <param name="registrationId">Unique Identifier for the registration</param>
    /// <param name="courseId">Unique Identifier for the course</param>
    /// <param name="learnerId">Unique Identifier for the learner</param>
    /// <param name="learnerFirstName">Learner's first name</param>
    /// <param name="learnerLastName">Learner's last name</param>
    public function CreateRegistration($registrationId, $courseId, $learnerId, $learnerFirstName, 
                                        $learnerLastName)
    {
        $request = new ServiceRequest($this->_configuration);

        $params = array('rid'=>$registrationId,
                        'cid'=>$courseId,
                        'fn'=>$learnerFirstName,
                        'ln'=>$learnerLastName,
                        'lid'=>$learnerId);
        
        $request->setMethodParams($params);
        
        return $request->CallService("registration/createRegistration");
    }
    
    /// <summary>
    /// Gets the url to directly launch/view the course registration in a browser
    /// </summary>
    /// <param name="registrationId">Unique Identifier for the registration</param>
    /// <param name="redirectOnExitUrl">Upon exit, the url that the SCORM player will redirect to</param>
    /// <param name="cssUrl">Absolute url that points to a custom player style sheet</param>
    /// <param name="debugLogPointerUrl">Url that the server will postback a "pointer" url regarding
    /// a saved debug log that resides on s3</param>
    /// <returns>URL to launch</returns>
    public function GetLaunchUrl($registrationId, $redirectOnExitUrl=null)
    {
        $request = new ServiceRequest($this->_configuration);
        $params = array('rid' => $registrationId);
        
        if(isset($redirectOnExitUrl))
        {
            $params['url'] = $redirectOnExitUrl;
        }

        $request->setMethodParams($params);
        return $request->ConstructUrl("registration/launch");
    } 
 }

?>
