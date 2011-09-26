<?php
/**
 * ImageDTO Class File
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
 * @author George Cooper
 * @copyright 2011 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @category DataProcessing
 * @package Persistence
 * @subpackage DataTransferObjects
 * @version 1.0
 */

/**
 * ImageDTO
 * 
 * Needs to be commented
 *
 * @category DataProcessing
 * @package Persistence
 * @subpackage DataTransferObjects
 */
class ImageDTO extends DataTransferObject implements ElementInterface {

    private $_imageContent = null;
    private $_imageDimensionX = null;
    private $_imageDimensionY = null;
    private $_imageMIMEType = null;
    private $_imageFileHash = null;
    private $_imageFileName = null;
    private $_imageFileSize = null;
    private $_imageDateUploaded = null;
    private $_imageUploader = null;
    private $_imageElementTitle = null;
    private $_imageElementSummary = null;

    /*
     * Element Interface Members
     */

    private $_elementID = null;
    private $_elementInstanceID = null;
    private $_elementCreatorProfileID = null;
    private $_elementInstanceCreatorProfileID = null;
    private $_elementType = null;
    private $_elementTypeID = null;
    
    /*
     * Element Extension Members
     */
        
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

    /**
     * Element Interface Methods
     */

    public function getElementType() {
        return $this->_elementType;
    }

    public function setElementType( $elementType ) {
        $this->_elementType = $elementType;
    }

    public function getElementTypeID() {
        return $this->_elementTypeID;
    }

    public function setElementTypeID( $elementTypeID ) {
        $this->_elementTypeID = $elementTypeID;
    }
    
    public function getElementID() {
        return $this->_elementID;
    }

    public function setElementID( $elementID ) {
        $this->_elementID = $elementID;
    }
    
    public function getElementInstanceID() {
        return $this->_elementInstanceID;
    }

    public function setElementInstanceID( $elementInstanceID ) {
        $this->_elementInstanceID = $elementInstanceID;
    }
    
    public function getElementCreatorProfileID() {
        return $this->_elementCreatorProfileID;
    }

    public function setElementCreatorProfileID( $elementCreatorProfileID ) {
        $this->_elementCreatorProfileID = $elementCreatorProfileID;
    }
    
    public function getElementInstanceCreatorProfileID() {
        return $this->_elementInstanceCreatorProfileID;
    }

    public function setElementInstanceCreatorProfileID( $elementInstanceCreatorProfileID ) {
        $this->_elementInstanceCreatorProfileID = $elementInstanceCreatorProfileID;
    }

    public function getElementPackage() {}
    
    public function setElementPackage( $elementPackage ) {}

    public function getElementPackagePath() {}
    
    public function setElementPackagePath( $elementPackagePath ) {}    

    /*
     * Element Extension Methods
     */

    public function getImageElementSummary() {
        return $this->_imageElementSummary;
    }
    
    public function setImageElementSummary( $imageElementSummary ) {
        $this->_imageElementSummary = $imageElementSummary;
    }

    public function getImageElementTitle() {
        return $this->_imageElementTitle;
    }
    
    public function setImageElementTitle( $imageElementTitle ) {
        $this->_imageElementTitle = $imageElementTitle;
    }
    
}
