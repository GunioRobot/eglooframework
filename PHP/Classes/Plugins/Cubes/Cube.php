<?php
/**
 * Cube Class File
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
 * @author George Cooper
 * @copyright 2011 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package Content
 * @version 1.0
 */

/**
 * Cube
 * 
 * Needs to be commented
 *
 * @package Content
 */
class Cube extends Content {

    private $elementPackagePath = null;

    public function __construct( $elementTypeID, $elementType, $elementPackagePath ) {
        $this->id = $elementTypeID;
        $this->name = $elementType;
        $this->elementPackagePath = $elementPackagePath;
    }
        
    public function __destruct() {
        
    }    
        
    public function getOriginalUploader() {}
    public function getOriginalUploadDate() {}
    public function getContentType() {}
    
}

?>
