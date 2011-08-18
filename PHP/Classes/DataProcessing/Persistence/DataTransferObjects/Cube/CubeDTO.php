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
class CubeDTO extends DataTransferObject implements ElementInterface {

    private $_id = null;
    private $_directoryLocation = null;
	private $_permissionLevel;
    private $_contentViewXML = null;
    private $_preferencesViewXML = null;
    private $_cubeName = null;
    private $_cubeElementXMLHTTPResponse = null;
    private $_cubeElementContentViewInitFunc = null;
    private $_cubeElementContentViewXHTMLFrame = null;
    private $_cubeElementContentViewXHTMLContent = null;
    private $_cubeElementContentViewCSS = null;
    private $_cubeElementPreferencesViewInitFunc = null;
    private $_cubeElementPreferencesViewXHTMLContent = null;
    private $_cubeElementAllFunctions = null;
    
    /*
     * Element Interface Members
     */

    private $_elementID = null;
    private $_elementInstanceID = null;
    private $_elementCreatorProfileID = null;
    private $_elementInstanceCreatorProfileID = null;
    private $_elementType = null;
    private $_elementTypeID = null;

	private $_elementRank = null;
	private $_profileElementrank = null;
	
	private $_rankable = false;
	

	public function getRankable(){
		return $this->_rankable;
	}
	
	public function setRankable($rankable){
		$this->_rankable = $rankable;
	}

	public function getElementRank(){
		return $this->_elementRank;
	}
	
	public function setElementRank($rank){
		$this->_elementRank = $rank;
	}

	public function getProfileElementRank(){
		return $this->_profileElementrank;
	}
	
	public function setProfileElementRank($rank){
		$this->_profileElementrank = $rank;
	}
	
    public function getID() {
        return $this->_id;
    }

	public function getDirectoryLocation() {
        return $this->_directoryLocation;
    }

	public function getPermissionLevel() {
        return $this->_permissionLevel;
    }


    public function setID( $id ) {
        $this->_id = $id;
    }

    public function setDirectoryLocation( $dirLocation ) { 
        $this->_directoryLocation = $dirLocation; 
    }
    
    public function setPermissionLevel( $permissionLevel ) {
        $this->_permissionLevel = $permissionLevel;
    }

    public function getCubeContentView() {
        return $this->_contentViewXML;
    }


    public function getCubeName() {
    	//TODO: fix this
    	$cubeInfoPlist = simplexml_load_file( $this->_directoryLocation . "/" . "Info.plist" );
        $this->_cubeName = (string) $cubeInfoPlist['CubeName'];
        return $this->_cubeName;
    }
    
    public function setCubeName( $cubeName ) {
        $this->_cubeName = $cubeName;
    }
    
    public function getCubePreferencesView() {
        return $this->_preferencesViewXML;
    }

    public function getCubeElementXMLHTTPRequest() {
        return '../Templates/Frameworks/Common/XMLHTTPResponse/Element/CubeElementXMLHTTPResponse.xmltpl';
    }

    public function getCubeElementAllFunctions() {
        return $this->_directoryLocation . '/javascript/AllFunctions.js';
    }

    public function getCubeElementContentViewInitFunc() {
        return $this->_directoryLocation . '/javascript/ContentViewInitFunc.js';
    }
    
    public function getCubeElementContentViewXHTMLFrameBeforeContent() {return $this->_directoryLocation . '/xhtml/ContentViewFrameBeforeContent.html';}
    public function getCubeElementContentViewXHTMLFrameAfterContent() {return $this->_directoryLocation . '/xhtml/ContentViewFrameAfterContent.html';}
    public function getCubeElementContentViewXHTMLContent() {return $this->_directoryLocation . '/xhtml/ContentViewContent.html';}
    public function getCubeElementContentViewCSS() {return $this->_directoryLocation . '/css/Default.css';}

    public function getCubeElementPreferencesViewInitFunc() {
        return $this->_directoryLocation . '/javascript/PreferencesViewInitFunc.js';
    }

    public function getCubeElementPreferencesViewXHTMLContent() {
        return $this->_directoryLocation . '/xhtml/PreferencesViewContent.html';    
         
    }

    public function buildMetaInfo() {
        // load XML
        $cubeInfoPlist = simplexml_load_file( $this->_directoryLocation . "/" . "Info.plist" );
        
        // Make sure to cast this to string
        // Memcache fails to store objects containing resource handles
        $this->_cubeName = (string) $cubeInfoPlist['CubeName'];
    }

    public function build() {}
    

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
    
    public function getCubeInstance( $cubeInstanceID, $profileID ){
    	$daoFunction = 'getElementInstance';
    	
    	$inputValues = array();
    	$inputValues[ 'profileID' ] = $profileID;
    	$inputValues[ 'elementID' ] = $cubeInstanceID;
    	 	    	
    	$daoFactory = AbstractDAOFactory::getInstance();
		$genericPLFunctionDAO = $daoFactory->getGenericPLFunctionDAO();
		$gqDTO = $genericPLFunctionDAO->selectGenericData( $daoFunction,  $inputValues );
		
		$this->setElementInstanceID( $cubeInstanceID );
		$this->setElementTypeID( $gqDTO->get_output_elementtype_id() );
		$this->setElementInstanceCreatorProfileID( $gqDTO->get_output_creator_id() );
		$this->setDirectoryLocation( $gqDTO->get_output_elementpackagepath() );
    	$this->setElementRank( $gqDTO->get_output_elementrank() );
    	$this->setProfileElementRank( $gqDTO->get_output_profileelementrank() );
    	$this->setRankable( $gqDTO->get_output_rankable() );
    	
    }
  	
  	public function createNewInstance( $profileID, $cubeTypeID ){

	 	$daoFunction = 'createNewCubeInstance';
    	
    	$inputValues = array();
    	$inputValues[ 'creatorID' ] = $profileID;
    	$inputValues[ 'elementtypeID' ] = $cubeTypeID;
    	 	    	
    	$daoFactory = AbstractDAOFactory::getInstance();
		$genericPLFunctionDAO = $daoFactory->getGenericPLFunctionDAO();
		$gqDTO = $genericPLFunctionDAO->selectGenericData( $daoFunction,  $inputValues );
		
		$this->setElementInstanceID( $gqDTO->get_output_element_id() );
		$this->setElementTypeID( $cubeTypeID );
		$this->setElementInstanceCreatorProfileID( $profileID );
		$this->setDirectoryLocation( $gqDTO->get_output_elementpackagepath() );
		$this->setElementRank( $gqDTO->get_output_elementrank() );
    	$this->setProfileElementRank( $gqDTO->get_output_profileelementrank() );
    	$this->setRankable( $gqDTO->get_output_rankable() );
    }

}
 

