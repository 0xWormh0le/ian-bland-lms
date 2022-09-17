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


class CourseData{
	private $_courseId;
    private $_scormId;
    private $_version;
    private $_title;
    private $_addedDate;

    private $_numberOfRegistrations;
    private $_numberOfVersions;
    

	/// <summary>
    /// Purpose of this class is to map the return xml from the course listing
    /// web service into an object.  This is the main constructor.
    /// </summary>
    /// <param name="courseDataElement"></param>
    public function __construct($courseDataElement)
    {
		if(isset($courseDataElement))
		{
            $this->_courseId = (string) $courseDataElement['id'];
            $this->_scormId = (string) $courseDataElement['scorm_id'];
            $this->_version = (string) @$courseDataElement['version'];
            $this->_addedDate = (string) $courseDataElement['created_at'];
            $this->_title = (string) $courseDataElement['title'];
		}
    }

	/// <summary>
    /// Helper method which takes the full XmlDocument as returned from the course listing
    /// web service and returns a List of CourseData objects.
    /// </summary>
    /// <param name="data"></param>
    /// <returns></returns>
    public static function ConvertToCourseDataList($data)
    {   
        $allResults = array();
		if (false == $data['data']['status']) {
            return $allResults;
        }		
        
        foreach ($data['data']['courses'] as $course)
        {   
            $allResults[] = new CourseData($course);
        }
        
        return $allResults;
    }

	/// <summary>
    /// Course Identifier as specified at import-time
    /// </summary>
    public function getCourseId()
    {
        return $this->_courseId;
    }

    /// <summary>
    /// Count of the number of versions for this course/package
    /// </summary>
    public function getNumberOfVersions()
    {
        return $this->_numberOfVersions;
    }

    /// <summary>
    /// Count of the number of existing registrations there are for this
    /// course -- the number of instances that a user has taken this course.
    /// </summary>
    public function getNumberOfRegistrations()
    {
        return $this->_numberOfRegistrations;
    }

    /// <summary>
    /// The title of this course
    /// </summary>
    public function getTitle()
    {
        return $this->_title;
    }

    /// <summary>
    /// The title of this course
    /// </summary>
    public function getAddedDate()
    {
        return $this->_addedDate;
    }

    /// <summary>
    /// The title of this course
    /// </summary>
    public function getSCORMId()
    {
        return $this->_scormId;
    }

    /// <summary>
    /// The title of this course
    /// </summary>
    public function getVersion()
    {
        return $this->_version;
    }       
}

?>
