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
class InvitationData
    {
        private $_id;
        private $_subject;
	    private $_courseId;
        private $_courseTitle;
        private $_email;
        private $_status;
        private $_max_registration;
        private $_created_at;
        private $_updated_at;        

		/// <summary>
        /// Inflate launch info object from passed in xml element
        /// </summary>
        /// <param name="launchInfoElem"></param>
        public function __construct($data)
        {
            $this->_id = (string) $data['id'];
            $this->_subject = (string) $data['subject'];
            $this->_courseId = (string) $data['course_id'];
            $this->_courseTitle = (string) $data['courseTitle'];
            $this->_email = (string) $data['email'];
            $this->_status =  (string) $data['status'];
            $this->_max_registration =  (string) $data['max_registration'];
            $this->_created_at =  (string) $data['created_at'];
            $this->_updated_at =  (string) $data['updated_at'];
        }


		/// <summary>
        /// The id associated with this invitation
        /// </summary>
        public function getId()
        {
            return $this->_id;
        }

        /// <summary>
        /// The id associated with this invitation
        /// </summary>
        public function getSubject()
        {
            return $this->_subject;
        }

        /// <summary>
        /// The course id
        /// </summary>
        public function getCourseId()
        {
			return $this->_courseId;
        }

        /// <summary>
        /// The coruse title
        /// </summary>
        public function getCourseTitle()
        {
            return $this->_courseTitle;
        }

        /// <summary>
        /// The email
        /// </summary>
        public function getEmail()
        {
            return $this->_email; 
        }

        /// <summary>
        /// The status
        /// </summary>
        public function getStatus()
        {
            return $this->_status; 
        }
 
         /// <summary>
        /// The max registrations
        /// </summary>
        public function getMaxRegistration()
        {
            return $this->_max_registration; 
        }

         /// <summary>
        /// The created date
        /// </summary>
        public function getCreatedDate()
        {
            return $this->_created_at; 
        }

         /// <summary>
        /// The updated date
        /// </summary>
        public function getUpdatedDate()
        {
            return $this->_updated_at; 
        }        
		//TODO:
		
		/// <summary>
        /// Return list of launch info objects from xml element
        /// </summary>
        /// <param name="doc"></param>
        /// <returns>array of LaunchInfo objects</returns>
		public static function ConvertToInvitationList($data)
        {
            $allResults = array();

            if (false == $data['data']['status']) {
                return $allResults;
            }

            foreach ($data['data']['invitations'] as $invitation)
            {
                $allResults[] = new InvitationData($invitation);
            }

            return $allResults;
        }
}

?>
