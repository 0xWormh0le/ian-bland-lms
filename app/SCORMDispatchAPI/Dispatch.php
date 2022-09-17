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

class Dispatch {
    private $_id;
    private $_name;
    private $_destinationId;
    private $_destinationName;
    private $_courseTitle;
    private $_courseId;
    private $_enabled;
    private $_notes;
    private $_createdBy;
    private $_createDate;
    private $_updateDate;
    private $_tags;

    public function __construct($data)
    {
		if(isset($data))
		{
	        $this->_id = (string) $data['id'];
            $this->_name = (string) $data['name'];
	        $this->_destinationId = (string) $data['client_id'];
	        $this->_destinationName = (string) $data['destTitle'];
	        $this->_courseTitle = (string) $data['courseTitle'];
	        $this->_courseId = (string) $data['course_id'];
	        $this->_enabled = (string)$data['blocked'];
	        $this->_createdBy = (string) $data['creator_id'];
	        $this->_createDate = (string) $data['created_at'];
	        $this->_updateDate = (string) $data['updated_at'];
		}
    }
    
    public static function parseDispatchList($data)
    {
        $allResults = array();

		if (false == $data['data']['status']) {
            return $allResults;
        }
        
        foreach ($data['data']['dispatches'] as $dispatchElem)
        {
            $allResults[] = new Dispatch($dispatchElem);
        }

        return $allResults;
    }

    public function getId()
    {
        return $this->_id;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function getDestinationId()
    {
        return $this->_destinationId;
    }

    public function getDestinationName()
    {
        return $this->_destinationName;
    }

    public function getCourseTitle()
    {
        return $this->_courseTitle;
    }

    public function getCourseId()
    {
        return $this->_courseId;
    }

    public function getEnabled()
    {
        return $this->_enabled;
    }

    public function getNotes()
    {
        return $this->_notes;
    }

    public function getCreatedBy()
    {
        return $this->_createdBy;
    }

    public function getCreateDate()
    {
        return $this->_createDate;
    }

    public function getUpdateDate()
    {
        return $this->_updateDate;
    }

    public function getTags()
    {
        return $this->_tags;
    }

}
?>
