<?php
/**
 * XMLRawPHPTemplateEngineException Class File
 *
 * Contains the class definition for the XMLRawPHPTemplateEngineException
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
 * @package $package
 * @subpackage $subpackage
 * @version 1.0
 */

/**
 * XMLRawPHPTemplateEngineException
 *
 * Private exception subclass for use by XMLRawPHPTemplateEngine
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class XMLRawPHPTemplateEngineException extends RawPHPTemplateEngineException {

   /**
    * XMLRawPHPTemplateEngineException constructor.  Takes a message and a code and invokes
    * the parent (RawPHPTemplateEngineException) constructor.  May eventually contain additional code,
    * but for now acts as a means of determining the exact type of exception thrown
    * so it is possible to track down what threw it.
    *
    * @param $message   the message that this exception will contain
    * @param $code      the optional code of this exception (unused)
    * @returns          a XMLRawPHPTemplateEngineException
    */
   public function __construct( $message, $code = 0 ) {
       // Call parent constructor
       parent::__construct( $message, $code );
   }

}
