<?php
/**
 * AccountDTO Class File
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
 * @category DataProcessing
 * @package Persistence
 * @subpackage DataTransferObjects
 * @version 1.0
 */

/**
 * AccountDTO
 * 
 * Needs to be commented
 *
 * @category DataProcessing
 * @package Persistence
 * @subpackage DataTransferObjects
 */
class BlogDTO extends DataTransferObject {

	private $_blogID = null;
    private $_text = null;
    private $_title = null;
    private $_creationDate = null;
    private $_lastModificationDate = null;
    private $_permissions = null;
    private $_tags = null;
    private $_rank = null;
    private $_owner = null;
    private $_error = null;
    private $_successful = false;

	public function getBlogID() {
		return $this->_blogID;
	}

    public function getContent() {
        return $this->_content;
    }

    public function getTitle() {
        return $this->_title;
    }

    public function getCreationDate() {
        return $this->_creationDate;
    }

    public function getLastModificationDate() {
        return $this->_lastModificationDate;
    }

    public function getPermissions() {
        return $this->_permissions;
    }
    
    public function getTags() {
        return $this->_tags;
    }
    
    public function getRank() {
        return $this->_rank;
    }
    
    public function getOwner() {
        return $this->_owner;
    }

	public function setBlogID($blogID) {
		$this->_blogID = $blogID;
	}

    public function setContent( $content ) {
        $this->_content = $content;
    }

    public function setTitle( $title ) { 
        $this->_title = $title; 
    }

    public function setCreationDate( $creationDate ) {
        $this->_creationDate = $creationDate;
    }

    public function setLastModificationDate( $lastModificationDate ) {
        $this->_lastModificationDate = $lastModificationDate;
    }

    public function setPermissions( $permissions ) {
        $this->_permissions = $permissions;
    }
    
    public function setTags( $tags ) {
        $this->_tags = $tags;
    }
    
    public function setRank( $rank ) {
        $this->_rank = $rank;
    }
    
    public function setOwner( $owner ) {
        $this->_owner = $owner;
    }

    public function getCreateBlogEntryError() {
        return $this->_error;
    }
    
    public function setCreateBlogEntryError( $error ) {
        $this->_error = $error;
    }

    public function createBlogEntrySuccessful() {
        return $this->_successful;
    }

    public function setCreateBlogEntrySuccessful( $successful = true ) {
        $this->_successful = $successful;
    }


}

