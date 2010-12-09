<?php
/**
 * UNIDirectionalRelationshipDTO Class File
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
 * @package Data Transfer Objects
 * @version 1.0
 */

/**
 * UNIDirectionalRelationshipDTO
 * 
 * Needs to be commented
 *
 * @package Data Transfer Objects
 */
class UNIDirectionalRelationshipDTO extends RelationshipDTO {
	
	private $parentName = null;
	private $childName = null;
	private $parentBool = true;
	
	public function getRelationDirection(){
		return "UNIDIRECTIONAL";
	}
	
	public function getParentName(){
		return $this->parentName;
	}
	
	public function setParentName( $parentName ){
		$this->parentName = $parentName;
	}
	
	public function getChildName(){
		return $this->childName;
	}
	
	public function setChildName( $childName ){
		$this->childName = $childName;
	}
	
	public function isParent(){
		return $this->parentBool;
	}
	
	public function setParent( $parentBool ){
		$this->parentBool = $parentBool;
	}
	
}

