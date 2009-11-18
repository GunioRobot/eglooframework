<?php
/**
 * ProcessLogoutRequestProcessor Class File
 *
 * Needs to be commented
 * 
 * Copyright 2008 eGloo, LLC
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
 * @copyright 2008 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package RequestProcessing
 * @subpackage Account
 * @version 1.0
 */

/**
 * ProcessLogoutRequestProcessor
 *
 * Needs to be commented
 * 
 * @package RequestProcessing
 * @subpackage Account
 */
class ProcessLogoutRequestProcessor extends RequestProcessor {

    
    public function processRequest() {
        // TODO more clean up
        $_SESSION = '';
        session_regenerate_id( true );
        session_destroy();
        header('Location: /');
//        
//
//        $this->_templateEngine = new TemplateEngine( 'dev', 'us' );
//        header("Content-type: text/html; charset=UTF-8");
//
//
//        $username = $this->requestInfoBean->getPOST('username');
//        $password = $this->requestInfoBean->getPOST('password');
//        
//        eGlooLogger::writeLog( eGlooLogger::DEBUG, "USERNAME! " . $username );
//        eGlooLogger::writeLog( eGlooLogger::DEBUG, "PASSWORD! " . $password );
//        
//        
//        $daoFactory = DAOFactory::getInstance();
//        $accountDAO = $daoFactory->getAccountDAO();
//        $userDTO = $accountDAO->userLogin($username, $password, '1', '2');
//                
//        if( $userDTO->getUserID() !== null){
//            //login successful
//            $_SESSION['USER_ID'] = $userDTO->getUserID();
//            echo "{'LOGGED_IN': 'true', 'whatever': 'dudes'}";
//            
//        } else {
//            //login failed
//            echo "{LOGGED_IN: false}";
//            
//        }
//            
//        exit;
    }
 }
?>