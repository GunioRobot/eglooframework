<?php
/**
 * RelationshipDTO Abstract Class File
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
 * RelationshipDTO
 * 
 * Needs to be commented
 *
 * @package Data Transfer Objects
 */
abstract class RelationshipDTO {
 
 	private $relationshipType = null;
	private $otherProfileName = null;
	private $otherProfileID = null;
 	private $relationshipID = null;
 
	public function getOtherProfileName() {
        return $this->otherProfileName;
    }

    public function setOtherProfileName( $otherProfileName ) {
        $this->otherProfileName = $otherProfileName;
    }

    public function getOtherProfileID() {
        return $this->otherProfileID;
    }

    public function setOtherProfileID( $otherProfileID ) {
        $this->otherProfileID = $otherProfileID;
    }   
	
	public function getRelationshipType(){
		return $this->relationshipType;
	}
	
	public function setRelationshipType( $relationshipType ){
		$this->relationshipType = $relationshipType;
	}
	
	public function getRelationshipID(){
		return $this->relationshipID;
	}
	
	public function setRelationshipID( $relationshipID ){
		$this->relationshipID = $relationshipID;
	}
	

	abstract public function getRelationDirection();
	
	
 }
?>