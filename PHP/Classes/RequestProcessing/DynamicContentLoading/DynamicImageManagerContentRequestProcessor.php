<?php
/**
 * DynamicImageManagerContentRequestProcessor Class File
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
 * @author Keith Buel
 * @copyright 2011 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package RequestProcessing
 * @version 1.0
 */

/**
 * DynamicImageManagerContentRequestProcessor
 * 
 * Needs to be commented
 * 
 * @package RequestProcessing
 * @subpackage RequestProcessors
 */
class DynamicImageManagerContentRequestProcessor extends RequestProcessor {

    public function processRequest() {
        /**
         * what type? css, js, xhtml
         * which cube?
         */     
        $type = $this->requestInfoBean->getGET("contentType");
        
        $profileID = $_SESSION['MAIN_PROFILE_ID'];

        $output = null;
    
        if ($type === "css") {
            header("Content-type: text/css; charset=UTF-8");
            $output = $cubeDTO->getCubeElementContentViewCSS();
        } else if ( $type === "js") {
            header("Content-type: script/javascript; charset=UTF-8");
            switch ( $this->requestInfoBean->getGET( 'contentID' ) ) {
                case 'InitFunc' :
                    $output = $cubeDTO->getCubeElementContentViewInitFunc();
                    break;
                case 'InitPrefsFunc' :
                    $output = $cubeDTO->getCubeElementPreferencesViewInitFunc();
                    break;
                default :
                    break;
            }
            
        } else if ( $type === "xhtml") {
            switch ( $this->requestInfoBean->getGET( 'contentID' ) ) {
                case 'ContentViewFrame' :
                    $output = $cubeDTO->getCubeElementContentViewXHTMLFrame();
                    break;
                case 'ContentViewContent' :
                    $output = $cubeDTO->getCubeElementContentViewXHTMLContent();
                    break;
                case 'PreferencesViewContent' :
                    $output = $cubeDTO->getCubeElementPreferencesViewXHTMLContent();
                    break;
                default :
                    break;    
            }
        }
        
        echo $output;
}
 
}

?>
