<?php
/**
 * RequestProcessorDecorator Abstract Class File
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
 * @copyright 2010 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package RequestProcessing
 * @version 1.0
 */

/**
 * RequestProcessorDecorator
 * 
 * Needs to be commented
 * 
 * @package RequestProcessing
 * @subpackage Decorators
 */
abstract class RequestProcessorDecorator extends RequestProcessor {

   	private $childRequestProcessor = null;

   	public function setChildRequestProcessor( $requestProcessor ){
   		$this->childRequestProcessor = $requestProcessor;
   	}
   	
   	
    public function processRequest() {
		
		if( $this->requestPreProcessing() ){
			$this->childRequestProcessor->processRequest();
		}
		$this->requestPostProcessing();
		
    }
   
   
    
    public function setRequestInfoBean( $requestInfoBean ){
 		$this->childRequestProcessor->setRequestInfoBean( $requestInfoBean );
 	}
 	
 	public function getRequestInfoBean(){
 		return $this->childRequestProcessor->getRequestInfoBean();
 	}
   
   /**
    * override if needed in sub classes of the this decorator
    */
   abstract protected function requestPreProcessing();

   /**
    * override if needed in sub classes of the this decorator
    */
   abstract protected function requestPostProcessing();
   
    
  }
 
 
?>
