<?php

namespace App\SCORMDispatchAPI;

/* Software License Agreement (BSD License)
 * 
 * Copyright (c) 2010-2011, Rustici Software, LLC
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the <organization> nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL Rustici Software, LLC BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

require_once 'ServiceRequest.php';
require_once 'InvitationData.php';

/// <summary>
/// Client-side proxy for the "rustici.course.*" Hosted SCORM Engine web
/// service methods.  
/// </summary>
class InvitationService{
	
	private $_configuration = null;
	
	public function __construct($configuration) {
		$this->_configuration = $configuration;
		//echo $this->_configuration->getAppId();
	}
	
	/// <summary>
    /// Create a new SCORm Cloud invitation
    /// </summary>
    /// <param name="courseId">Unique Identifier for this course.</param>
    /// <param name="addresses">comma-delimited list of email addresses to which invitations should be sent 
    /// for ftp access service (see ftp service below). 
    /// If the domain specified does not exist, the course will be placed in the default permission domain</param>
    /// <returns>List of Import Results</returns>
    public function CreateInvitation($courseId, $subject, $emails)
    {
		$request = new ServiceRequest($this->_configuration);
        $params = array('cid' => $courseId);

		if (isset($emails))
		{
            $params['emails'] = $emails;
		}
		if (isset($subject))
		{
            $params['subject'] = $subject;
		}
		
		//$request->setMethodParams($params);
        $request->setDataToPost($params);
        
        //return $request->CallService("course/importCourse");
        
		$response = $request->CallService("invitation/createInvitation");
		
        return $response;
    }

	/// <summary>
    /// Update a new SCORm Cloud invitation
    /// </summary>
    /// <param name="courseId">Unique Identifier for this course.</param>
    /// <param name="addresses">comma-delimited list of email addresses to which invitations should be sent 
    /// for ftp access service (see ftp service below). 
    /// If the domain specified does not exist, the course will be placed in the default permission domain</param>
    /// <returns>List of Import Results</returns>
    public function UpdateInvitation($invitationId, $status, $registration)
    {
		$request = new ServiceRequest($this->_configuration);
        $params = array('inviteId' => $invitationId);

		if (isset($registration))
		{
            $params['maxreg'] = $registration;
		}
		if (isset($status))
		{
            $params['status'] = $status;
		}
		
		$request->setMethodParams($params);

		$response = $request->CallService("invitation/updateInvitation");
		
        return $response;
    }

	public function DeleteInvitation($invitationId)
    {
        $request = new ServiceRequest($this->_configuration);
        $params = array('inviteId' => $invitationId);
		$request->setMethodParams($params);
        return $request->CallService("invitation/deleteInvitation");
    }

	public function GetInvitationList()
    {
		$request = new ServiceRequest($this->_configuration);
        $response = $request->CallService("invitation/getInvitations");

        $invitationData = new InvitationData(null);
        $invitationArray = $invitationData->ConvertToInvitationList($response);

        return $invitationArray;
    }

	public function GetInvitationStatus($invitationId)
    {
		$request = new ServiceRequest($this->_configuration);
		$params = array();
		
		$params['inviteId'] = $invitationId;		
        
		$request->setMethodParams($params);
        $response = $request->CallService("invitation/getStatus");
        return $response;
    }
	

	public function GetInvitationInfo($invitationId)
    {
		$request = new ServiceRequest($this->_configuration);
		$params = array();
		
		$params['inviteId'] = $invitationId;
        
		$request->setMethodParams($params);
        $response = $request->CallService("invitation/getInvitation");
        $invitationData = new InvitationData($response['data']['invitation']);
        
        return $invitationData;
    }

	public function ChangeStatus($invitationId, $enable)
    {
		$request = new ServiceRequest($this->_configuration);
		$params = array();
		
		$params['inviteId'] = $invitationId;
		$params['status'] = $enable;		
        
		$request->setMethodParams($params);
        $response = $request->CallService("invitation/changeStatus");
        return $response;
    }
    
    
 }

?>
