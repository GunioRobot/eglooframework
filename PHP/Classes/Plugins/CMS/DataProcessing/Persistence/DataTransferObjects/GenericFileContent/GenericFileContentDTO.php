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
	private $_fileHash = null;
	private $_fileLocalID = null;
	private $_fileMasterID = null;
	private $_fileMod = null;
	private $_fileName = null;
	private $_fileNetworkID = null;
	private $_filePath = null;
	private $_filePeerID = null;
	private $_fileSize = null;
	private $_fileDateUploaded = null;
	private $_fileUploader = null;

	public function __construct() {
		
	}

	// Need mod/mutation members

	public function getFileLocalID() {
		return $this->_fileLocalID;
	}

	public function setFileLocalID( $fileLocalID ) {
		$this->_fileLocalID = $fileLocalID;
	}
	
	public function getFileContent() {
		return $this->_fileContent;
	}

	public function setFileContent( $fileContent ) {
		$this->_fileContent = $fileContent;
	}

	public function getFileHash() {
		return $this->_fileHash;
	}

	public function setFileHash( $fileHash ) {
		$this->_fileHash = $fileHash;
	}

	public function getFileMod() {
		return $this->_fileMod;
	}

	public function setFileMod( $fileMod ) {
		$this->_fileMod = $fileMod;
	}

	public function getFileName() {
		return $this->_fileName;
	}

	public function setFileName( $fileName ) {
		$this->_fileName = $fileName;
	}

	public function getFilePath() {
		return $this->_filePath;
	}

	public function setFilePath( $filePath ) {
		$this->_filePath = $filePath;
	}

	public function getFileSize() {
		return $this->_fileSize;
	}

	public function setFileSize( $fileSize ) {
		$this->_fileSize = $fileSize;
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

	public function initFromHTTPFile( $eglooHTTPFile, $load_content = false, $extended_processing = false ) {
		$newFileDTO = new GenericFileContentDTO();

		$newFileDTO->setFileName( $eglooHTTPFile->getFileName() );
		$newFileDTO->setFileMIMEType( $eglooHTTPFile->getFileType() );
		$newFileDTO->setFilePath( $eglooHTTPFile->getTemporaryFileName() );
		$newFileDTO->setFileSize( $eglooHTTPFile->getFileSize() );

		if ( $extended_processing ) {
			// Extended processing
		}

		if ( $load_content ) {
			// Load content
		}

		return $newFileDTO;
	}

	public static function initWithForm( Form $form ) {
	}

	public function __destruct() {
		
	}

}

