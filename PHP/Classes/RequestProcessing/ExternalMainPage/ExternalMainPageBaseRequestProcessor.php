<?php
/**
 * ExternalMainPageBaseRequestProcessor Class File
 *
 * Contains the class definition for the ExternalMainPageBaseRequestProcessor, a
 * subclass of the RequestProcessor abstract class.
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
 * @author Keith Buel
 * @author George Cooper
 * @copyright 2008 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package RequestProcessing
 * @version 1.0
 */

/**
 * External Main Page Request Processor
 * 
 * Handles client requests to retrieve the external main page (the domain root;
 * e.g. www.egloo.com).
 * 
 * @package RequestProcessing
 * @subpackage RequestProcessors
 */
class ExternalMainPageBaseRequestProcessor extends RequestProcessor {
    
    /**
     * Concrete implementation of the abstract RequestProcessor method
     * processRequest().
     * 
     * This method handles processing of the incoming client request.  Its
     * primary function is to establish the deployment environment (dev, test,
     * production) and the current localization, and to then parse the correct
     * template(s) in order to construct and output the appropriate external
     * main page (the domain root; e.g. www.egloo.com).
     * 
     * @access public
     */
    public function processRequest() {
    	
        eGlooLogger::writeLog( eGlooLogger::$DEBUG, "ExternalMainPageBaseRequestProcessor: Entered processRequest()" );

        // TODO In terms of a framework, we probably don't want to force logged in users
        // to do a hard redirect for every web application this might host; We should look
        // into abstracting both this and forcing SSL.  But for eGloo's purposes, this is
        // fine for now
        if ( isset( $_SESSION['LOGGED_IN'] ) && $_SESSION['LOGGED_IN'] === true ) {
	   		header( 'Location: /profileID=' . $_SESSION['MAIN_PROFILE_ID'] );
        } else {
	        $templateDirector = TemplateDirectorFactory::getTemplateDirector( $this->requestInfoBean );
	        $templateBuilder = new XHTMLBuilder();
	
	        $templateDirector->setTemplateBuilder( $templateBuilder );
	
	        $templateDirector->preProcessTemplate();
	        
			$lines = file('.svn/entries');
			$version = "";
			foreach( $lines as $line ) {
				if ( preg_match("/revision=\"(\d+)\"/", $line, $revision ) ){
					$version = $revision[1];
					break;	
				}
			}
			
			//svn version 1.4 fix
			if( $version === "" ){
				$version = $lines[3];
			}
			
	        $templateVariables['svnVersion'] = $version;
	        $templateDirector->setTemplateVariables( $templateVariables );            
	        
	                
	        $output = $templateDirector->processTemplate();
	
	        eGlooLogger::writeLog( eGlooLogger::$DEBUG, "ExternalMainPageBaseRequestProcessor: Echoing Response" );
	        
	        // TODO move header declarations to a decorator
	        header("Content-type: text/html; charset=UTF-8");
	        
	        // TODO buffer output
	        echo $output;        
        }
        
        eGlooLogger::writeLog( eGlooLogger::$DEBUG, "ExternalMainPageBaseRequestProcessor: Exiting processRequest()" );

    }
    
}
?>