<?php
/**
 * DPDispatcher Class File
 *
 * $file_block_description
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
 * @copyright 2010 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package $package
 * @subpackage $subpackage
 * @version 1.0
 */

/**
 * DPDispatcher
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
abstract class DPDispatcher {

    /**
     * Static Constants
     */
    private static $singletonDispatcher;
    
    /**
     * XML Variables
     */
    private $DISPATCH_XML_LOCATION = "Templates/Applications/";
    private $dispatchNodes = array();
	protected $dispatchPath = '';
	protected $processTemplate = false;

    private $application = null;
    private $interfaceBundle = null;
    
    /**
     * This method reads the xml file from disk into a document object model.
     * It then populates a hash of [TemplateDispatcher] -> [TemplateDispatcher XML Object]
     */
    abstract protected function loadDispatchNodes();

    /**
     * returns the singleton of this class
     */
	public static function getInstance( $application, $interfaceBundle ) {
		// Junk
	}

    /**
     * Only functional method available to the public.  
     */
    abstract public function dispatch( $requestInfoBean );


	public function getDispatchPath() {
		return $this->dispatchPath;
	}

	public function getProcessTemplate() {
		return $this->processTemplate;
	}

}
