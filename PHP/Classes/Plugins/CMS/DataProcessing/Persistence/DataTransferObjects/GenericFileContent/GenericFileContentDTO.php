<?php
/**
 * GenericFileContentDTO Class File
 *
 * Contains the class definition for the GenericFileContentDTO
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
 * GenericFileContentDTO
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class GenericFileContentDTO extends DataTransferObject {

	private $_fileContent = null;
	private $_fileMIMEType = null;
	private $_fileFileHash = null;
	private $_fileFileName = null;
	private $_fileFileSize = null;
	private $_fileDateUploaded = null;
	private $_fileUploader = null;

	// Need mod/mutation members
	
	public function getFileContent() {
		return $this->_fileContent;
	}

	public function setFileContent( $fileContent ) {
		$this->_fileContent = $fileContent;
	}

	public function getFileFileHash() {
		return $this->_fileFileHash;
	}

	public function setFileFileHash( $fileFileHash ) {
		$this->_fileFileHash = $fileFileHash;
	}

	public function getFileFileName() {
		return $this->_fileFileName;
	}

	public function setFileFileName( $fileFileName ) {
		$this->_fileFileName = $fileFileName;
	}

	public function getFileFileSize() {
		return $this->_fileFileSize;
	}

	public function setFileFileSize( $fileFileSize ) {
		$this->_fileFileSize = $fileFileSize;
	}

	public function getFileMIMEType() {
		return $this->_fileMIMEType;
	}

	public function setFileMIMEType( $mimeType ) {
		$this->_fileMIMEType = $mimeType;
	}

	public function getFileDateUploaded() {
		return $this->_fileDateUploaded;
	}

	public function setFileDateUploaded( $dateUploaded ) {
		$this->_fileDateUploaded = $dateUploaded;
	}

	public function getFileUploader() {
		return $this->_fileUploader;
	}

	public function setFileUploader( $uploader ) {
		$this->_fileUploader = $uploader;
	}

}

