<?php
/**
 * ExternalMainPageAboutRequestProcessor Class File
 *
 * Contains the class definition for the ExternalMainPageAboutRequestProcessor, a
 * subclass of the RequestProcessor abstract class.
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
 * @author George Cooper
 * @copyright 2011 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package RequestProcessing
 * @version 1.0
 */

/**
 * External Main Page About Form Request Processor
 * 
 * Handles client requests to retrieve the "About eGloo" form linked from the
 * external main page.
 * 
 * @package RequestProcessing
 * @subpackage RequestProcessors
 */
class ExternalMainPageAboutRequestProcessor extends RequestProcessor {

    /**
     * Concrete implementation of the abstract RequestProcessor method
     * processRequest().
     * 
     * This method handles processing of the incoming client request.  Its
     * primary function is to establish the deployment environment (dev, test,
     * production) and the current localization, and to then parse the correct
     * template(s) in order to construct and output the appropriate external
     * main "About eGloo" form.
     * 
     * @access public
     */    
    public function processRequest() {
        eGlooLogger::writeLog( eGlooLogger::DEBUG, "ExternalMainPageAboutRequestProcessor: Entered processRequest()" );

        $templateDirector = TemplateDirectorFactory::getTemplateDirector( $this->requestInfoBean );
        $templateBuilder = new XHTMLBuilder();

        $templateDirector->setTemplateBuilder( $templateBuilder );

        $templateDirector->preProcessTemplate();
                
        $output = $templateDirector->processTemplate();

        eGlooLogger::writeLog( eGlooLogger::DEBUG, "ExternalMainPageAboutRequestProcessor: Echoing Response" );
        
        // TODO move header declarations to a decorator
        header("Content-type: text/html; charset=UTF-8");
        
        // TODO buffer output
        echo $output;
    }
    
}
?>