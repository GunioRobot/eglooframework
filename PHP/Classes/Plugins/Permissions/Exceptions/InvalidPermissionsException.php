<?php
/**
 * InvalidPermissionsException Class File
 *
 * Contains the class definition for the InvalidPermissionsException.
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
 * @subpackage Exceptions
 * @version 1.0
 */

/**
 * InvalidPermissionsException
 * 
 * An exception <description>
 * 
 * Long Description
 *
 * @package RequestProcessing
 * @subpackage Exceptions
 */
final class InvalidPermissionsException extends Exception {

   /**
    * InvalidPermissionsException constructor.  Takes a message and a code and invokes
    * the parent (Exception) constructor.  May eventaully contain additional code,
    * but for now acts as a means of determining the exact type of exception thrown
    * so it is possible to track down what threw it.
    *
    * @param $message   the message that this exception will contain
    * @param $code      the optional code of this exception (unused)
    * @returns          an InvalidPermissionsException
    */
   public function __construct( $message, $code = 0 ) {
       // Call parent constructor
       parent::__construct( $message, $code );
   }
}
 
 
?>
