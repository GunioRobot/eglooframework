<?php
/**
 * ImageContentDTO Class File
 *
 * Contains the class definition for the ImageContentDTO
 * 
 * Copyright 2011 eGloo, LLC
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *		  http://www.apache.org/licenses/LICENSE-2.0
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
 * ImageContentDTO
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class ImageContentDTO extends DataTransferObject {


	private $_imageContent = null;
	private $_imageDimensionX = null;
	private $_imageDimensionY = null;
	private $_imageMIMEType = null;
	private $_imageFileHash = null;
	private $_imageFileName = null;
	private $_imageFilePath = null;
	private $_imageFileSize = null;
	private $_imageDateUploaded = null;
	private $_imageUploader = null;

	// Need mod/mutation members
	
	public function getImageContent() {
		return $this->_imageContent;
	}

	public function setImageContent( $imageContent ) {
		$this->_imageContent = $imageContent;
	}

	public function getImageFileHash() {
		return $this->_imageFileHash;
	}

	public function setImageFileHash( $imageFileHash ) {
		$this->_imageFileHash = $imageFileHash;
	}

	public function getImageFileName() {
		return $this->_imageFileName;
	}

	public function setImageFileName( $imageFileName ) {
		$this->_imageFileName = $imageFileName;
	}

	public function getImageFileSize() {
		return $this->_imageFileSize;
	}

	public function setImageFileSize( $imageFileSize ) {
		$this->_imageFileSize = $imageFileSize;
	}

	public function getImageDimensionX() {
		return $this->_imageDimensionX;
	}
	
	public function setImageDimensionX( $imageDimensionX ) {
		$this->_imageDimensionX = $imageDimensionX;
	}

	public function getImageDimensionY() {
		return $this->_imageDimensionY;
	}
	
	public function setImageDimensionY( $imageDimensionY ) {
		$this->_imageDimensionY = $imageDimensionY;
	}
	
	public function getImageMIMEType() {
		return $this->_imageMIMEType;
	}

	public function setImageMIMEType( $mimeType ) {
		$this->_imageMIMEType = $mimeType;
	}

	public function getImageDateUploaded() {
		return $this->_imageDateUploaded;
	}

	public function setImageDateUploaded( $dateUploaded ) {
		$this->_imageDateUploaded = $dateUploaded;
	}

	public function getImageUploader() {
		return $this->_imageUploader;
	}

	public function setImageUploader( $uploader ) {
		$this->_imageUploader = $uploader;
	}

	public static function initFromHTTPFile( $eglooHTTPFile, $load_content = false, $extended_processing = false ) {
		$newFileDTO = new ImageContentDTO();

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

