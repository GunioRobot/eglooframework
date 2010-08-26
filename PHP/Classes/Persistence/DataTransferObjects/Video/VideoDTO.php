<?php
/**
 * VideoDTO Class File
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
 * @author George Cooper
 * @copyright 2008 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package Data Transfer Objects
 * @version 1.0
 */

/**
 * VideoDTO
 * 
 * Needs to be commented
 *
 * @package Data Transfer Objects
 */
class VideoDTO extends DataTransferObject implements ElementInterface {

    private $_videoContent = null;
    private $_videoDimensionX = null;
    private $_videoDimensionY = null;
    private $_videoMIMEType = null;
    private $_videoFileHash = null;
    private $_videoFileName = null;
    private $_videoFileSize = null;
    private $_videoDateUploaded = null;
    private $_videoUploader = null;
    private $_videoElementTitle = null;
    private $_videoElementSummary = null;

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
        
    public function getVideoContent() {
        return $this->_VideoContent;
    }

    public function setVideoContent( $videoContent ) {
        $this->_videoContent = $videoContent;
    }

    public function getVideoFileHash() {
        return $this->_videoFileHash;
    }

    public function setVideoFileHash( $videoFileHash ) {
        $this->_videoFileHash = $videoFileHash;
    }

    public function getVideoFileName() {
        return $this->_videoFileName;
    }

    public function setVideoFileName( $videoFileName ) {
        $this->_videoFileName = $videoFileName;
    }

    public function getVideoFileSize() {
        return $this->_videoFileSize;
    }

    public function setVideoFileSize( $videoFileSize ) {
        $this->_videoFileSize = $videoFileSize;
    }

    public function getVideoDimensionX() {
        return $this->_videoDimensionX;
    }
    
    public function setVideoDimensionX( $videoDimensionX ) {
        $this->_videoDimensionX = $videoDimensionX;
    }

    public function getVideoDimensionY() {
        return $this->_videoDimensionY;
    }
    
    public function setVideoDimensionY( $videoDimensionY ) {
        $this->_videoDimensionY = $videoDimensionY;
    }
    
    public function getVideoMIMEType() {
        return $this->_videoMIMEType;
    }

    public function setVideoMIMEType( $mimeType ) {
        $this->_videoMIMEType = $mimeType;
    }

    public function getVideoDateUploaded() {
        return $this->_videoDateUploaded;
    }

    public function setVideoDateUploaded( $dateUploaded ) {
        $this->_videoDateUploaded = $dateUploaded;
    }

    public function getVideoUploader() {
        return $this->_videoUploader;
    }

    public function setVideoUploader( $uploader ) {
        $this->_videoUploader = $uploader;
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

    public function getVideoElementSummary() {
        return $this->_videoElementSummary;
    }
    
    public function setVideoElementSummary( $videoElementSummary ) {
        $this->_videoElementSummary = $videoElementSummary;
    }

    public function getVideoElementTitle() {
        return $this->_videoElementTitle;
    }
    
    public function setVideoElementTitle( $videoElementTitle ) {
        $this->_videoElementTitle = $videoElementTitle;
    }
    
}

?>