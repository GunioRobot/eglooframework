<?php
/**
 * Content Class File
 *
 * $file_block_description
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
 * Content
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
abstract class Content {

    protected $id;
    protected $name;
    protected $permissions;

    public function __construct() {
        
    }
        
    public function __destruct() {
        
    }    
    
    public function getID() {
        return $this->id;   
    }
 
    public function getName() {
        return $this->name;   
    }

    public function getPermissions() {
        return $this->permissions;   
    }
    
    abstract public function getOriginalUploader();
    abstract public function getOriginalUploadDate();
    abstract public function getContentType();
    
}
