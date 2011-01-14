<?php
/**
 * CubeContentStrategy Abstract Class File
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
 * @author Keith Buel
 * @copyright 2011 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package ContentProcessing
 * @version 1.0
 */

/**
 * CubeContentStrategy
 * 
 * Needs to be commented
 *
 * @package ContentProcessing
 */
abstract class CubeContentStrategy {

	protected $_cubeDTO = null;
	protected $_templateEngine = null;
    
    public function setCubeDTO( $cubeDTO ) { $this->_cubeDTO = $cubeDTO; }
    public function setTemplateEngine( TemplateEngine $templateEngine ) { $this->_templateEngine = $templateEngine; }

	abstract public function prepareContentViewContent();

	abstract public function preparePreferencesViewContent();
   
    
  }
?>
