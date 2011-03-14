<?php
/**
 * TemplateContentDTO Class File
 *
 * Contains the class definition for the TemplateContentDTO
 * 
 * Copyright 2011 eGloo LLC
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
 * @copyright 2011 eGloo LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package $package
 * @subpackage $subpackage
 * @version 1.0
 */

/**
 * TemplateContentDTO
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class TemplateContentDTO extends DataTransferObject {

	protected $_templateContent = null;
	protected $_templateDimensionX = null;
	protected $_templateDimensionY = null;
	protected $_templateMIMEType = null;
	protected $_templateHash = null;
	protected $_templateName = null;
	protected $_templatePath = null;
	protected $_templateSize = null;
	protected $_templateDateUploaded = null;
	protected $_templateUploader = null;
	protected $_templateLocalID = null;
	protected $_templateMod = null;

	/**
	 * @var string Bucket template is contained in
	 */
	protected $_templateBucket = null;

	/**
	 * Returns protected class member $_templateBucket
	 *
	 * @return string Bucket template is contained in
	 */
	public function getTemplateBucket() {
		return $this->_templateBucket;
	}

	/**
	 * Sets protected class member $_templateBucket
	 *
	 * @param templateBucket string Bucket template is contained in
	 */
	public function setTemplateBucket( $templateBucket ) {
		$this->_templateBucket = $templateBucket;
	}

	/**
	 * @var string Store the template is contained in
	 */
	protected $_template_store = null;

	/**
	 * Returns protected class member $_template_store
	 *
	 * @return string Store the template is contained in
	 */
	public function getTemplateStore() {
		return $this->_template_store;
	}

	/**
	 * Sets protected class member $_template_store
	 *
	 * @param template_store string Store the template is contained in
	 */
	public function setTemplateStore( $template_store ) {
		$this->_template_store = $template_store;
	}

	/**
	 * @var integer template_id in the data store
	 */
	protected $_template_store_id = null;

	/**
	 * Returns protected class member $_template_store_id
	 *
	 * @return integer template_id in the data store
	 */
	public function getTemplateStoreID() {
		return $this->_template_store_id;
	}

	/**
	 * Sets protected class member $_template_store_id
	 *
	 * @param template_store_id integer template_id in the data store
	 */
	public function setTemplateStoreID( $template_store_id ) {
		$this->_template_store_id = $template_store_id;
	}

	/**
	 * @var string URI of the template
	 */
	protected $_templateURI = null;

	/**
	 * Returns protected class member $_templateURI
	 *
	 * @return string URI of the template
	 */
	public function getTemplateURI() {
		return $this->_templateURI;
	}

	/**
	 * Sets protected class member $_templateURI
	 *
	 * @param templateURI string URI of the template
	 */
	public function setTemplateURI( $templateURI ) {
		$this->_templateURI = $templateURI;
	}

	/**
	 * @var string View/angle this template represents
	 */
	protected $_template_view = null;

	/**
	 * Returns protected class member $_template_view
	 *
	 * @return string View/angle this template represents
	 */
	public function getTemplateView() {
		return $this->_template_view;
	}

	/**
	 * Sets protected class member $_template_view
	 *
	 * @param template_view string View/angle this template represents
	 */
	public function setTemplateView( $template_view ) {
		$this->_template_view = $template_view;
	}

	/**
	 * @var string Zone this template is located in
	 */
	protected $_template_zone = null;

	/**
	 * Returns protected class member $_template_zone
	 *
	 * @return string Zone this template is located in
	 */
	public function getTemplateZone() {
		return $this->_template_zone;
	}

	/**
	 * Sets protected class member $_template_zone
	 *
	 * @param template_zone string Zone this template is located in
	 */
	public function setTemplateZone( $template_zone ) {
		$this->_template_zone = $template_zone;
	}


	// Need mod/mutation members
	
	public function getTemplateLocalID() {
		return $this->_templateLocalID;
	}

	public function setTemplateLocalID( $templateLocalID ) {
		$this->_templateLocalID = $templateLocalID;
	}

	public function getTemplateMod() {
		return $this->_templateMod;
	}

	public function setTemplateMod( $templateMod ) {
		$this->_templateMod = $templateMod;
	}
	
	public function getTemplateContent() {
		return $this->_templateContent;
	}

	public function setTemplateContent( $templateContent ) {
		$this->_templateContent = $templateContent;
	}

	public function getTemplateHash() {
		return $this->_templateHash;
	}

	public function setTemplateHash( $templateHash ) {
		$this->_templateHash = $templateHash;
	}

	public function getTemplateName() {
		return $this->_templateName;
	}

	public function setTemplateName( $templateName ) {
		$this->_templateName = $templateName;
	}

	public function getTemplateSize() {
		return $this->_templateSize;
	}

	public function setTemplateSize( $templateSize ) {
		$this->_templateSize = $templateSize;
	}

	public function getTemplatePath() {
		return $this->_templatePath;
	}

	public function setTemplatePath( $templatePath ) {
		$this->_templatePath = $templatePath;
	}

	public function getTemplateDimensionX() {
		return $this->_templateDimensionX;
	}
	
	public function setTemplateDimensionX( $templateDimensionX ) {
		$this->_templateDimensionX = $templateDimensionX;
	}

	public function getTemplateDimensionY() {
		return $this->_templateDimensionY;
	}
	
	public function setTemplateDimensionY( $templateDimensionY ) {
		$this->_templateDimensionY = $templateDimensionY;
	}
	
	public function getTemplateMIMEType() {
		return $this->_templateMIMEType;
	}

	public function setTemplateMIMEType( $mimeType ) {
		$this->_templateMIMEType = $mimeType;
	}

	public function getTemplateDateUploaded() {
		return $this->_templateDateUploaded;
	}

	public function setTemplateDateUploaded( $dateUploaded ) {
		$this->_templateDateUploaded = $dateUploaded;
	}

	public function getTemplateUploader() {
		return $this->_templateUploader;
	}

	public function setTemplateUploader( $uploader ) {
		$this->_templateUploader = $uploader;
	}

	public function initFromHTTPFile( $eglooHTTPFile, $load_content = false, $extended_processing = false ) {
		$newTemplateDTO = new TemplateContentDTO();

		$newTemplateDTO->setTemplateName( $eglooHTTPFile->getFileName() );
		$newTemplateDTO->setTemplateMIMEType( $eglooHTTPFile->getFileType() );
		$newTemplateDTO->setTemplatePath( $eglooHTTPFile->getTemporaryFileName() );
		$newTemplateDTO->setTemplateSize( $eglooHTTPFile->getFileSize() );

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

