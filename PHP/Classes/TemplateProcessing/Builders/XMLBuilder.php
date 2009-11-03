<?php
/**
 * XMLBuilder Class File
 *
 * Contains the class definition for the XMLBuilder, a concrete subclass 
 * that inherits from the TemplateBuilder class.
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
 * @package Template
 * @version 1.0
 */

require( '../PHP/Classes/Caching/Smarty/SmartyMemcacheHandler.php' );

/**
 * XMLBuilder
 * 
 * Provides a definition for a XMLBuilder subclass of the TemplateBuilder
 * abstract class.
 *
 * @package Template
 * @subpackage Template Building
 */
class XMLBuilder extends TemplateBuilder {
    
    private $deployment = 'dev';
    private $requestInfoBean = null;
    private $templateEngine = null;
    private $templateVariables = null;
        
    public function setRequestInfoBean( $requestInfoBean ) {
        $this->requestInfoBean = $requestInfoBean;
    }
    
    public function setTemplateVariables() {
        
    }
    
    public function run() {
        $templateDispatcher = XHTMLDispatcher::getInstance( 'eGloo', 'Default' );
        
//        $templateDispatcher = XHTMLDispatcher::getInstance( $this->application, $this->interfaceBundle );
        $templateEngine = new TemplateEngine( $this->interfaceBundle, 'US', 'en' );
        
        $dispatchPath = $templateDispatcher->dispatch( $this->requestInfoBean );
        
        // assign variables

        // output        
        $output = $templateEngine->fetch( $dispatchPath );
        
        return $output;        
    }
    
}

?>
