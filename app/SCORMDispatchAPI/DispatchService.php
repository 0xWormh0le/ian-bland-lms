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
require_once 'DispatchDestination.php';
require_once 'Dispatch.php';

/// <summary>
/// Client-side proxy for the "rustici.course.*" Hosted SCORM Engine web
/// service methods.  
/// </summary>
class DispatchService {
	
	private $_configuration = null;
	
	public function __construct($configuration) {
		$this->_configuration = $configuration;
	}
	
    public function GetDestinationList()
    {
        $request = new ServiceRequest($this->_configuration);
		
        $response = $request->CallService("dispatch/getDestinations");

        return DispatchDestination::parseDestinationList($response);
    }

    public function CreateDestination($name)
    {
        $request = new ServiceRequest($this->_configuration);
        $params = array('name' => $name);

		$request->setMethodParams($params);
        $response = $request->CallService("dispatch/createDestination");        
        return $response;
    }

    public function DeleteDestination($destinationId)
    {
        $request = new ServiceRequest($this->_configuration);
        $params = array('did' => $destinationId);
		$request->setMethodParams($params);
        return $request->CallService("dispatch/deleteDestination");
    }

    public function GetDestinationInfo($destinationId)
    {
        $request = new ServiceRequest($this->_configuration);
        $params = array('did' => $destinationId);
		$request->setMethodParams($params);
        $response = $request->CallService("dispatch/getDestination");
               
        return new DispatchDestination($response['data']['destination']);
    }

    public function UpdateDestination($destinationId, $name = null, $tagList = null)
    {
        $request = new ServiceRequest($this->_configuration);
        $params = array('did' => $destinationId);
        if($name != null) {
            $params['name'] = $name;
        }

		$request->setMethodParams($params);
        $request->CallService("dispatch/updateDestination");
    }

    public function CreateDispatch($name, $courseId, $destinationId)
    {
        $request = new ServiceRequest($this->_configuration);
        $params = array('name' => $name);
        $params['cid'] = $courseId;
        $params['did'] = $destinationId;

		$request->setMethodParams($params);
        $response = $request->CallService("dispatch/createDispatch");
        return $response;
    }

    public function GetDispatchInfo($dispatchId)
    {
        $request = new ServiceRequest($this->_configuration);
        $params = array('dpid' => $dispatchId);
		$request->setMethodParams($params);
        $response = $request->CallService("dispatch/getDispatchInfo");
        return new Dispatch($response['data']['dispatch']);
    }

    public function DeleteDispatches($dispatchId)
    {
        $request = new ServiceRequest($this->_configuration);
        $params = array();

        if($dispatchId != null){
            $params['dpid'] = $dispatchId;
        }

		$request->setMethodParams($params);
        $request->CallService("dispatch/deleteDispatches");
    }

    public function GetDispatchList()
    {
        $request = new ServiceRequest($this->_configuration);
        $response = $request->CallService("dispatch/getDispatches");
        return Dispatch::parseDispatchList($response);
    }

    public function UpdateDispatches($destinationId = null, $courseId = null, $dispatchId = null, $tagList = null, $enabled = -1, $tagsToAdd = null, $tagsToRemove = null)
    {
        echo "enabled = $enabled\n";
        $request = new ServiceRequest($this->_configuration);
        $params = array();
        if($destinationId != null){
            $params['did'] = $destinationId;
        }
        if($courseId != null){
            $params['cid'] = $courseId;
        }
        if($dispatchId != null){
            $params['dpid'] = $dispatchId;
        }
        if($enabled != -1){
            $params['enabled'] = ($enabled ? "true" : "false");
        }
		$request->setMethodParams($params);
        $request->CallService("dispatch/updateDispatches");
    }

    public function DownloadDispatchPackage($dispatchId, $learningType)
    {
        $request = new ServiceRequest($this->_configuration);
        $params = array();
        if($dispatchId != null){
            $params['dpid'] = $dispatchId;
        }
        if($learningType != null){
            $params['learning'] = $learningType;
        }        
        $request->setMethodParams($params);
        //$request->CallService("dispatch/updateDispatches");
        return $request->ConstructUrl("dispatch/download");
    }

    public function GetDispatchDownloadUrl($dispatchId = null, $tagList = null, $cssUrl = null)
    {
        $request = new ServiceRequest($this->_configuration);
        $params = array();
        if($destinationId != null){
            $params['did'] = $destinationId;
        }
        if($courseId != null){
            $params['cid'] = $courseId;
        }
        if($dispatchId != null){
            $params['dpid'] = $dispatchId;
        }
		$request->setMethodParams($params);
        return $request->ConstructUrl("dispatch/downloadDispatches");
    }

}
