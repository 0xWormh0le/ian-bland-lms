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


 /// <summary>
/// Data class to hold high-level Launch Info
 /// </summary>
class LaunchHistory
{
    private $_score;
    private $_completion;
    private $_satisfaction;
    private $_totaltime;
    private $_attempt;
    private $_coursetitle;

	/// <summary>
    /// Inflate launch info object from passed in xml element
    /// </summary>
    /// <param name="launchInfoElem"></param>
    public function __construct($report)
    {
		$this->_completion = (string) $report['complete_status'];
        $this->_satisfaction = (string) $report['satisfied_status'];
        $this->_score = (string) $report['score'];
        $this->_totaltime = (string) $report['total_time'];
        $this->_attempt =  (string) $report['attempt'];
        $this->_coursetitle =  (string) $report['courseTitle'];
    }


	/// <summary>
    /// The id associated with this launch
    /// </summary>
    public function getScore()
    {
        return $this->_score;
    }

    /// <summary>
    /// The completion value of the launch
    /// </summary>
    public function getCompletion()
    {
		return $this->_completion;
    }

    /// <summary>
    /// The satisfaction value of the launch
    /// </summary>
    public function getSatisfaction()
    {
        return $this->_satisfaction;
    }

    /// <summary>
    /// The total learning time
    /// </summary>
    public function getTotalTime()
    {
        return $this->_totaltime; 
    }

    /// <summary>
    /// The attempt number
    /// </summary>
    public function getAttempt()
    {
        return $this->_attempt; 
    }

     /// <summary>
    /// The attempt number
    /// </summary>
    public function getCourseTitle()
    {
        return $this->_coursetitle; 
    }

	//TODO:
	
	/// <summary>
    /// Return list of launch info objects from xml element
    /// </summary>
    /// <param name="doc"></param>
    /// <returns>array of LaunchInfo objects</returns>
	public static function ConvertToLaunchInfoList($data)
    {
        $allResults = array();

        if (false == $data['data']['status']) {
            return $allResults;
        }

        foreach ($data['data']['reports'] as $report)
        {
            $allResults[] = new LaunchHistory($report);
        }

        return $allResults;
    }
}
?>