<?php
/**
 * GenericCacheAllDecorator Class File
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
 * @package RequestProcessing
 * @version 1.0
 */

/**
 * GenericCacheAllDecorator
 *
 * Needs to be commented
 *
 * @package RequestProcessing
 * @subpackage Decorators
 */
class GenericCacheAllDecorator extends RequestProcessorDecorator {

   /**
    * do any pre processing here
    */
	protected function requestPreProcessing(){
		eGlooLogger::writeLog( eGlooLogger::DEBUG, "GenericCacheAllDecorator: setting cache all headers for one day", 'Decorators' );

		Header("Cache-Control: must-revalidate");
		Header("Pragma: cache");

		//cache for 7 days
		//$offset = 60 * 60 * 24 * 7;
		$offset = 604800;
		Header( "Expires: " . gmdate("D, d M Y H:i:s", time() + $offset) . " GMT" );

		eGlooLogger::writeLog( eGlooLogger::DEBUG, "GenericCacheAllDecorator: Leaving requestPreProcessing()", 'Decorators' );
		return true;
    }

   /**
    * do any post processing here
    */
	protected function requestPostProcessing(){
	}


  }


?>
