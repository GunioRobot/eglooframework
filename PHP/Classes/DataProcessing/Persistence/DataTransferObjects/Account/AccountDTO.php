<?php
/**
 * AccountDTO Class File
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
 * @author George Cooper
 * @author Keith Buel
 * @copyright 2011 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @category DataProcessing
 * @package Persistence
 * @subpackage DataTransferObjects
 * @version 1.0
 */

/**
 * AccountDTO
 * 
 * Needs to be commented
 *
 * @category DataProcessing
 * @package Persistence
 * @subpackage DataTransferObjects
 */
class AccountDTO {
    
    private $_userDTO = null;
    private $_accountActivationID = null;
    private $_registrationError = null;
    private $_successfulRegistration = false;    
    
    public function getUserDTO() {
        return $this->_userDTO;
    }

    public function setUserDTO( $userDTO ) {
        $this->_userDTO = $userDTO;
    }

    public function getRegistrationError() {
        return $this->_registrationError;
    }
    
    public function setRegistrationError( $registrationError ) {
        $this->_registrationError = $registrationError;
    }

    public function registrationSuccessful() {
        return $this->_successfulRegistration;
    }

    public function setRegistrationSuccessful( $successfulRegistration = true ) {
        $this->_successfulRegistration = $successfulRegistration;
    }

    public function getAccountActivationID( $accountActivationID ) {
        return $this->_accountActivationID;
    }

    public function setAccountActivationID( $accountActivationID ) {
        $this->_accountActivationID = $accountActivationID;
    }

 }
 

