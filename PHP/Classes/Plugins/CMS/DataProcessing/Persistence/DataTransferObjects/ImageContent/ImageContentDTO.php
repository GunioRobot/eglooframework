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

	protected $_imageContent = null;
	protected $_imageDimensionX = null;
	protected $_imageDimensionY = null;
	protected $_imageMIMEType = null;
	protected $_imageFileHash = null;
	protected $_imageFileName = null;
	protected $_imageFilePath = null;
	protected $_imageFileSize = null;
	protected $_imageDateUploaded = null;
	protected $_imageUploader = null;
	protected $_imageFileLocalID = null;
	protected $_imageFileMod = null;

	/**
	 * @var string Bucket image is contained in
	 */
	protected $_imageBucket = null;

	/**
	 * Returns protected class member $_imageBucket
	 *
	 * @return string Bucket image is contained in
	 */
	public function getImageBucket() {
		return $this->_imageBucket;
	}

	/**
	 * Sets protected class member $_imageBucket
	 *
	 * @param imageBucket string Bucket image is contained in
	 */
	public function setImageBucket( $imageBucket ) {
		$this->_imageBucket = $imageBucket;
	}

	/**
	 * @var string Store the image is contained in
	 */
	protected $_image_store = null;

	/**
	 * Returns protected class member $_image_store
	 *
	 * @return string Store the image is contained in
	 */
	public function getImageStore() {
		return $this->_image_store;
	}

	/**
	 * Sets protected class member $_image_store
	 *
	 * @param image_store string Store the image is contained in
	 */
	public function setImageStore( $image_store ) {
		$this->_image_store = $image_store;
	}

	/**
	 * @var integer image_id in the data store
	 */
	protected $_image_store_id = null;

	/**
	 * Returns protected class member $_image_store_id
	 *
	 * @return integer image_id in the data store
	 */
	public function getImageStoreID() {
		return $this->_image_store_id;
	}

	/**
	 * Sets protected class member $_image_store_id
	 *
	 * @param image_store_id integer image_id in the data store
	 */
	public function setImageStoreID( $image_store_id ) {
		$this->_image_store_id = $image_store_id;
	}

	/**
	 * @var string URI of the image
	 */
	protected $_imageURI = null;

	/**
	 * Returns protected class member $_imageURI
	 *
	 * @return string URI of the image
	 */
	public function getImageURI() {
		return $this->_imageURI;
	}

	/**
	 * Sets protected class member $_imageURI
	 *
	 * @param imageURI string URI of the image
	 */
	public function setImageURI( $imageURI ) {
		$this->_imageURI = $imageURI;
	}

	/**
	 * @var string View/angle this image represents
	 */
	protected $_image_view = null;

	/**
	 * Returns protected class member $_image_view
	 *
	 * @return string View/angle this image represents
	 */
	public function getImageView() {
		return $this->_image_view;
	}

	/**
	 * Sets protected class member $_image_view
	 *
	 * @param image_view string View/angle this image represents
	 */
	public function setImageView( $image_view ) {
		$this->_image_view = $image_view;
	}

	/**
	 * @var string Zone this image is located in
	 */
	protected $_image_zone = null;

	/**
	 * Returns protected class member $_image_zone
	 *
	 * @return string Zone this image is located in
	 */
	public function getImageZone() {
		return $this->_image_zone;
	}

	/**
	 * Sets protected class member $_image_zone
	 *
	 * @param image_zone string Zone this image is located in
	 */
	public function setImageZone( $image_zone ) {
		$this->_image_zone = $image_zone;
	}


	// Need mod/mutation members
	
	public function getImageFileLocalID() {
		return $this->_imageFileLocalID;
	}

	public function setImageFileLocalID( $imageFileLocalID ) {
		$this->_imageFileLocalID = $imageFileLocalID;
	}

	public function getImageFileMod() {
		return $this->_imageFileMod;
	}

	public function setImageFileMod( $imageFileMod ) {
		$this->_imageFileMod = $imageFileMod;
	}
	
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

	public function getImageFilePath() {
		return $this->_imageFilePath;
	}

	public function setImageFilePath( $imageFilePath ) {
		$this->_imageFilePath = $imageFilePath;
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

	public function initFromHTTPFile( $eglooHTTPFile, $load_content = false, $extended_processing = false ) {
		$newFileDTO = new ImageContentDTO();

		$newFileDTO->setImageFileName( $eglooHTTPFile->getFileName() );
		$newFileDTO->setImageMIMEType( $eglooHTTPFile->getFileType() );
		$newFileDTO->setImageFilePath( $eglooHTTPFile->getTemporaryFileName() );
		$newFileDTO->setImageFileSize( $eglooHTTPFile->getFileSize() );

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

