<?php
/**
 * AjaxProcessLoginRequestProcessor Class File
 *
 * Needs to be commented
 * 
 * Copyright 2010 eGloo, LLC
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
 * @author George Cooper
 * @copyright 2011 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package RequestProcessing
 * @subpackage Account
 * @version 1.0
 */

/**
 * AjaxProcessLoginRequestProcessor
 *
 * Needs to be commented
 * 
 * @package RequestProcessing
 * @subpackage Account
 */
class AjaxProcessLoginRequestProcessor extends RequestProcessor {

    const STRONG_HASH = 'sha256';
    
    public function processRequest() {
		    	
		$jsonRetval = '';

		$username = $this->requestInfoBean->getGET('username');
		$password = hash(self::STRONG_HASH, $this->requestInfoBean->getGET('password') );
		
		$daoFactory = AbstractDAOFactory::getInstance();
		$accountDAO = $daoFactory->getAccountDAO();
		$userDTO = $accountDAO->userLogin($username, $password, '129.21.140.168', 'useragent');

		if( $userDTO->getUserID() !== null){

			//login successful
			$_SESSION['USER_ID'] = $userDTO->getUserID();
			$_SESSION['LOGGED_IN'] = true;
            $_SESSION['USER_USERNAME'] = $username;
			/**
			 * TEMP get main profile ID
			 * TODO remove later
			 */
			$_SESSION['MAIN_PROFILE_ID'] = $accountDAO->getMainProfileID( $_SESSION['USER_ID'] );
            $userInformation = $accountDAO->getUserInformation( $_SESSION['USER_ID'] );
			$_SESSION['USER_FIRST_NAME'] = $userInformation['firstname'];
            $_SESSION['USER_LAST_NAME'] = $userInformation['lastname'];
            
			//set the json return message
			$jsonRetval =  "window.location = 'http://' + window.location.hostname + '/profileID=" . $_SESSION['MAIN_PROFILE_ID'] . "';";
			
			/*
			 * create the new session and delete the old 
			 * because of change in permission level
			 */
			session_regenerate_id( true );
			
			//TODO this is to get IE to accept cookies with its stricter security model
			setcookie(session_name(), session_id(), time()+60*60*24*30, '/');
		} else {
			//login failed
			$jsonRetval = "$('#loadingImage').hide();$('#loginFailed').html('Login Failed');$('#forgotPassword').show();";
		}
		
		//set the header after all session information has been written.
        header("Content-type: text/javascript; charset=UTF-8");
        
        //return the json message to the axis call
		echo $jsonRetval;
    }
 }
?>