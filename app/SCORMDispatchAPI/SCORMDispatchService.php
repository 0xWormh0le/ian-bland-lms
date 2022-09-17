<?php

namespace App\SCORMDispatchAPI;

require_once ('Configuration.php');
require_once ('ServiceRequest.php');
require_once ('CourseService.php');
require_once ('DispatchService.php');
require_once ('InvitationService.php');

class SCORMDispatchService{

	private $_configuration = null;
    private $_courseService = null;
	private $_serviceRequest = null;
    private $_dispatchService = null;
    private $_invitationService = null;

	public function __construct() {
        
        $scormServiceUrl = config('scorm.url');
        $appId = config('scorm.id');
        $securityKey = config('scorm.secret');

        $this->_configuration = new Configuration($scormServiceUrl, $appId, $securityKey);
		$this->_serviceRequest = new ServiceRequest($this->_configuration);
        $this->_courseService = new CourseService($this->_configuration);
        $this->_dispatchService = new DispatchService($this->_configuration);
        $this->_invitationService = new InvitationService($this->_configuration);
	}
	
    public function isValidAccount() {
        $appId = $this->getAppId();
        $key = $this->getSecurityKey();
        $url = $this->getSCORMDispatchServiceUrl();
        
        if (empty($appId) || empty($key) || empty($url)) {
            return false;
        }
        
        return true;
    }   
    
	/**
	* <summary>
    * Contains all SCORM Engine Package-level (i.e., course) functionality.
    * </summary>
	*/
    public function getCourseService()
    {
        return $this->_courseService;
    }

	/**
	* <summary>
    * The Application ID obtained by registering with the SCORM Engine Service
    * </summary>
	*/
    public function getAppId()
    {
            return $this->_configuration->getAppId();
    }

	/**
	* <summary>
    * The security key (password) linked to the Application ID
    * </summary>
	*/
    public function getSecurityKey()
    {
            return $this->_configuration->getSecurityKey();
    }

	/**
	* <summary>
    * URL to the service, ex: http://localhost/serviceApi
    * </summary>
	*/
    public function getSCORMDispatchServiceUrl()
    {
            return $this->_configuration->getSCORMDispatchServiceUrl();
    }    

    /**
    * <summary>
    * Contains SCORM Engine dispatch functionality.
    * </summary>
    */
    public function getDispatchService()
    {
        return $this->_dispatchService;
    }

    /**
    * <summary>
    * Contains SCORM Engine Invitation functionality.
    * </summary>
    */
    public function getInvitationService()
    {
        return $this->_invitationService;
    }
    
	/**
	* <summary>
    * CreateNewRequest
    * </summary>
	*/
    public function CreateNewRequest()
    {
        return new ServiceRequest($this->_configuration);
    }
}
?>
