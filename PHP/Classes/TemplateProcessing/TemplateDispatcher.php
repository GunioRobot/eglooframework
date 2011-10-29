<?php
/**
 * TemplateDispatcher Class File
 *
 * Contains the class definition for the TemplateDispatcher, a final
 * class responsible for dispatching XHTML requests to the appropriate
 * XHTML template file for parsing.
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
 * @copyright 2011 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package Template
 * @version 1.0
 */

/**
 * TemplateDispatcher
 *
 * Provides a class definition for the TemplateDispatcher.
 *
 * @package Template
 */
abstract class TemplateDispatcher {

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
    abstract public function dispatch( $requestInfoBean, $requestID, $requestClass );


	public function getDispatchPath() {
		return $this->dispatchPath;
	}

	public function getProcessTemplate() {
		return $this->processTemplate;
	}


}
