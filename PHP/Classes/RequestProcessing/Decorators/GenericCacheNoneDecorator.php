<?php
/**
 * GenericCacheNoneDecorator Class File
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
 * GenericCacheNoneDecorator
 *
 * Needs to be commented
 *
 * @package RequestProcessing
 * @subpackage Decorators
 */
class GenericCacheNoneDecorator extends RequestProcessorDecorator {

   /**
    * do any pre processing here
    */
	protected function requestPreProcessing(){

        eGlooLogger::writeLog( eGlooLogger::DEBUG, "GenericCacheNoneDecorator: setting cache headers to no-cache", 'Decorators' );
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
		Header("Pragma: no-cache");
		return true;
    }

   /**
    * do any post processing here
    */
	protected function requestPostProcessing(){

	}


  }


?>
