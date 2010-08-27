<?php
/**
 * TemplateDirector Class File
 *
 * Contains the class definition for the TemplateDirector, an abstract 
 * director class for template parsing.
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
 * @package Template
 * @version 1.0
 */

/**
 * Template Director
 * 
 * Provides an abstract class definition for template director classes to
 * inherit from.
 *
 * @package Template
 */
abstract class TemplateDirector {
    protected $application = null;
    protected $interfaceBundle = null;
    
	public function setApplication( $application ) {
		$this->application = $application;
	}
	
	public function setInterfaceBundle( $interfaceBundle ) {
		$this->interfaceBundle = $interfaceBundle;
	}
}

?>
