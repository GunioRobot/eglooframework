<?php
/**
 * SessionDTO Class File
 *
 * Needs to be commented
 * 
 * Copyright 2011 eGloo, LLC
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *        http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *  
 * @author Keith Buel
 * @copyright 2011 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @category DataProcessing
 * @package Persistence
 * @subpackage DataTransferObjects
 * @version 1.0
 */

/**
 * SessionDTO
 * 
 * Needs to be commented
 *
 * @category DataProcessing
 * @package Persistence
 * @subpackage DataTransferObjects
 */
class SessionDTO{
 	
 	
	private $sessionID = null;
	private $userID = null;
	private $userAgent = null;
	private $sessionData = null;
	private $sessionExists = false;
	
	/**
	 * @return the sessionData
	 */
	public function getSessionData() {
		return $this->sessionData;
	}
	/**
	 * @param sessionData the sessionData to set
	 */
	public function setSessionData($sessionData) {
		$this->sessionData = $sessionData;
	}
	/**
	 * @return the sessionID
	 */
	public function getSessionID() {
		return $this->sessionID;
	}
	/**
	 * @param sessionID the sessionID to set
	 */
	public function setSessionID($sessionID) {
		$this->sessionID = $sessionID;
	}
	/**
	 * @return the userAgent
	 */
	public function getUserAgent() {
		return $this->userAgent;
	}
	/**
	 * @param userAgent the userAgent to set
	 */
	public function setUserAgent($userAgent) {
		$this->userAgent = $userAgent;
	}
	/**
	 * @return the userID
	 */
	public function getUserID() {
		return $this->userID;
	}
	/**
	 * @param userID the userID to set
	 */
	public function setUserID($userID) {
		$this->userID = $userID;
	}

 	public function setSessionExists( $exists ){
 		$this->sessionExists = $exists;
 	}
 	
 	public function sessionExists(){
 		return $this->sessionExists;
 	}


 }
 

