<?php
/**
 * RankingDTO Class File
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
 * @author <UNKNOWN>
 * @copyright 2011 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @category DataProcessing
 * @package Persistence
 * @subpackage DataTransferObjects
 * @version 1.0
 */

/**
 * RankingDTO
 * 
 * Needs to be commented
 *
 * @category DataProcessing
 * @package Persistence
 * @subpackage DataTransferObjects
 */
class RankingDTO extends DataTransferObject {
    
    private $_rankID = null;
    private $_rank = null;
    private $_profileID = null;
    private $_elementID = null;
    private $_creationDate = null;
    private $_successful = false;    
    
    //Accessor funtions
	public function getRankID() {
		return $this->_rankID;
	}

    public function getRank() {
        return $this->_rank;
    }

    public function getProfileID() {
        return $this->_profileID;
    }
	
	public function getElementID() {
        return $this->_elementID;
    }
    
    public function getCreationDate() {
        return $this->_creationDate;
    }
    
    public function getSuccessful() {
        return $this->_successful;
    }
    
    //Set functions
    public function setRankID($rankID) {
		$this->_rankID = $rankID;
	}

    public function setRank($rank) {
        $this->_rank = $rank;
    }

    public function setProfileID($profileID) {
        $this->_profileID = $profileID;
    }
	
	public function setElementID($elementID) {
        $this->_elementID = $elementID;
    }
    
    public function setCreationDate($creationDate) {
        $this->_creationDate = $creationDate;
    }
    
    public function setSuccessful($successful) {
        $this->_successful = $successful;
    }
    
    public function output(){
    	echo "--> RankingDTO Object <--<br>";
    	echo "ProfileID: $this->_profileID<br>";
    	echo "ElementID: $this->_elementID<br>";
    	echo "Rank: $this->_rank<br>";
    	echo "Creation Date: $this->_creationDate<br>";    	
    	echo "Successful: $this->_successful<br>";
    	echo "----------END OBJECT----------<br>";
    }
 }

