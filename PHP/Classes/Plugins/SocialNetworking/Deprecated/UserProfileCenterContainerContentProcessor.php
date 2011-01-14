<?php
/**
 * UserProfileCenterContainerContentProcessor Class File
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
 * @package ContentProcessing
 * @version 1.0
 */

/**
 * UserProfileCenterContainerContentProcessor
 * 
 * Needs to be commented
 *
 * @package ContentProcessing
 */
class UserProfileCenterContainerContentProcessor extends ContentProcessor {
    
   // private $_templateDefault = '../Templates/Applications/eGloo/InterfaceBundles/Default/XHTML/ProfileCenter/ProfileCenterContainer.tpl';
    
    private $_buildContainer = false;
    private $_loggedInUserProfile = false;
    private $_profileID = null;
    private $_profileImageHash = null;
    private $_username = null;
    
    public function __construct() {}
    
    public function prepareContent() {

    //    $this->_templateEngine->assign( 'userProfileCenterContainerContentUseTemplate', true );
        $this->_templateEngine->assign( 'userProfileCenterContainerContentTemplate', $this->_templateDefault );
        $this->_templateEngine->assign( 'buildContainer', $this->_buildContainer );
        $this->_templateEngine->assign( 'loggedInUserProfile', $this->_loggedInUserProfile );
        $this->_templateEngine->assign( 'profileID', $this->_profileID );
        $this->_profileImageHash = $this->getProfileImageHashFromProfileId($this->_profileID);
        $this->_templateEngine->assign( 'profileImageHash',  $this->_profileImageHash);
        $this->_templateEngine->assign( 'username', $this->_username );
        $this->buildCubes();
    }
	
	/**
	 * Returns the profile image hash for the specific profile ID
	 */
	private function getProfileImageHashFromProfileId( $profileID ){
        $daoFunction = 'getProfileImage';
		$inputValues = array();
    	$inputValues[ 'profileID' ] = $profileID;
    	
    	
	   	$daoFactory = AbstractDAOFactory::getInstance();
		$genericPLFunctionDAO = $daoFactory->getGenericPLFunctionDAO();
		$gqDTO = $genericPLFunctionDAO->selectGenericData( $daoFunction,  $inputValues );
				
		return $gqDTO->get_output_imagefilehash();
	}
	
	private function buildCubes(){
		
		$daoFactory = AbstractDAOFactory::getInstance();
        $userProfilePageDAO = $daoFactory->getUserProfilePageDAO();
        $profileCubesLayout = $userProfilePageDAO->getProfileCubes( $this->_profileID );	
		
		$column0InstanceIDArray  = array();
		$column1InstanceIDArray  = array();
		$column2InstanceIDArray  = array();
	
		//Make an array of cubedtos
		if( isset( $profileCubesLayout['0'] ) ) foreach ($profileCubesLayout['0'] as $row) $column0InstanceIDArray[] = $row;
		if( isset( $profileCubesLayout['1'] ) ) foreach ($profileCubesLayout['1'] as $row) $column1InstanceIDArray[] = $row;
		if( isset( $profileCubesLayout['2'] ) ) foreach ($profileCubesLayout['2'] as $row) $column2InstanceIDArray[] = $row;
		
		$cubeColumn0Output = array();
		$cubeColumn1Output = array();
		$cubeColumn2Output = array();
		
		foreach( $column0InstanceIDArray as $cubeInstanceID ) 
			$cubeColumn0Output[ $cubeInstanceID ] = $this->getCubeOutput( $cubeInstanceID );
	
		foreach( $column1InstanceIDArray as $cubeInstanceID ) 
			$cubeColumn1Output[ $cubeInstanceID ] = $this->getCubeOutput( $cubeInstanceID );
	
		foreach( $column2InstanceIDArray as $cubeInstanceID ) 
			$cubeColumn2Output[ $cubeInstanceID ] = $this->getCubeOutput( $cubeInstanceID );
		
		$this->_templateEngine->assign( 'cubeColumn0Output', $cubeColumn0Output );
		$this->_templateEngine->assign( 'cubeColumn1Output', $cubeColumn1Output );
		$this->_templateEngine->assign( 'cubeColumn2Output', $cubeColumn2Output );
	}
	
	
	private function getCubeOutput( $cubeInstanceID ){
		
        $profileID = $_SESSION['MAIN_PROFILE_ID'];
        $cubeDTO = new CubeDTO(); 
        $cubeDTO->getCubeInstance( $cubeInstanceID, $profileID );
		
		$templateEngine = new CubeDefaultTemplateEngine( 'dev', 'us' );
	    $templateEngine->assign( 'cubeElementInstanceID', $cubeDTO->getElementInstanceID());
	    $templateEngine->assign( 'cubeElementTypeID', $cubeDTO->getElementTypeID() );
	    $templateEngine->assign( 'cubeElementName', $cubeDTO->getCubeName() );
	    
	    if( $cubeDTO->getProfileElementRank() != null ){
        	$templateEngine->assign( 'profileRanked', true );
        	$templateEngine->assign( 'profileRank', ($cubeDTO->getProfileElementRank() + 1 )  * 20  );
        	
        } else {
        	$templateEngine->assign( 'profileRanked', false );
        	$templateEngine->assign( 'elementRank', ($cubeDTO->getElementRank() + 1 )  * 20 );
        }
	    
	    
	
		$templateEngine->assign( 'contentViewCSS', $cubeDTO->getCubeElementContentViewCSS() );
		$templateEngine->assign( 'contentViewFrameBeforeContent', $cubeDTO->getCubeElementContentViewXHTMLFrameBeforeContent() );
		$templateEngine->assign( 'contentViewContent', $cubeDTO->getCubeElementContentViewXHTMLContent() );
		$templateEngine->assign( 'contentViewFrameAfterContent', $cubeDTO->getCubeElementContentViewXHTMLFrameAfterContent() );
		
		$cubeContentProcessor = new CubeContentProcessor($cubeDTO, 'ContentViewContent');
		$cubeContentProcessor->setTemplateEngine( $templateEngine );
		$cubeContentProcessor->prepareContent();
			
	    return $templateEngine->fetch( "../Templates/Frameworks/Common/XHTML/Cube/WholeCube.tpl" );
	}
	
	
	
    public function buildStandAlone() {
        return $this->_templateEngine->fetch( $this->_templateDefault );
    }
    
    public function buildContainer() {
        return $this->_buildContainer;
    }

    public function setBuildContainer( $buildContainer = true ) {
        $this->_buildContainer = $buildContainer;
    }
        
    public function getLoggedInUser() {
        return $this->_loggedInUserProfile;
    }
    
    public function setLoggedInUser( $_loggedInUserProfile ) {
        $this->_loggedInUserProfile = $_loggedInUserProfile;
    }
    
    public function getProfileID() {
        return $this->_profileID;
    }
        
    public function setProfileID( $profileID ) {
        $this->_profileID = $profileID;
    }
    
    public function getProfileImageHash() {
        return $this->_profileImageHash;
    }
        
    public function setProfileImageHash( $profileImageHash ) {
        $this->_profileImageHash = $profileImageHash;
    }

    public function getUsername() {
        return $this->_username;
    }
    
    public function setUsername( $username ) {
        $this->_username = $username;
    }
    
}

?>