<?php
/**
 * ProfileCubeContentStrategy Class File
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
 * @package ContentProcessing
 * @version 1.0
 */

/**
 * ProfileCubeContentStrategy
 * 
 * Needs to be commented
 *
 * @package ContentProcessing
 */
class ProfileCubeContentStrategy extends CubeContentStrategy {

	public function prepareContentViewContent(){

		$viewingProfileID = $this->_cubeDTO->getElementInstanceCreatorProfileID();
		
		$loggedInProfileID = $_SESSION['MAIN_PROFILE_ID'];
		
		$daoFactory = AbstractDAOFactory::getInstance();
        $userProfileDAO = $daoFactory->getUserProfileDAO();
        $userProfileDTO = $userProfileDAO->getProfile( $viewingProfileID );
        
        $gender = $userProfileDTO->getSex();
        $hometown = $userProfileDTO->getHomeTown();
        $birthDate = date( 'F j, Y', strtotime( $userProfileDTO->getBirthDate() ) );
        
        $lookingForFriendship = $userProfileDTO->getLookingForFriendship();
        $lookingForRelationship = $userProfileDTO->getLookingForRelationship();
        $lookingForDating = $userProfileDTO->getLookingForDating();
        $lookingForRandomPlay = $userProfileDTO->getLookingForRandomPlay();
        $lookingForDatingWhateverICanGet = $userProfileDTO->getLookingForWhateverICanGet();
        
        $interestedInMen = $userProfileDTO->getInterestedInMen() ? UserProfileDTO::$MEN : '';
        $interestedInWomen = $userProfileDTO->getInterestedInWomen() ? UserProfileDTO::$WOMEN : '';
        
		$sexualInterest = "";
        
        if ( $interestedInMen !== '' && $interestedInWomen !== '' ) {
            $sexualInterest .= UserProfileDTO::$MEN . ', ' . UserProfileDTO::$WOMEN;
        } else if ( $interestedInMen !== '' ) {
            $sexualInterest .= UserProfileDTO::$MEN;
        } else if ( $interestedInWomen !== '' ) {
            $sexualInterest .= UserProfileDTO::$WOMEN;
        }
        
        $lookingFor = "";
                    
        $lookingFor .= $lookingForFriendship !== false ? UserProfileDTO::$FRIENDSHIP . ', ': '';
        $lookingFor .= $lookingForRelationship !== false ? UserProfileDTO::$RELATIONSHIP . ', ': '';
        $lookingFor .= $lookingForDating !== false ? UserProfileDTO::$DATING . ', ': '';
        $lookingFor .= $lookingForRandomPlay !== false ? UserProfileDTO::$RANDOMPLAY . ', ': '';
        $lookingFor .= $lookingForDatingWhateverICanGet !== false ? UserProfileDTO::$WHATEVER : '';
        	
		$this->_templateEngine->assign( 'gender', $gender );
		$this->_templateEngine->assign( 'birthDate', $birthDate );
		$this->_templateEngine->assign( 'sexualInterest', $sexualInterest );
		$this->_templateEngine->assign( 'lookingFor', $lookingFor );		
		$this->_templateEngine->assign( 'hometown', $hometown );		
		$this->_templateEngine->assign( 'rankable', false );
	}

	public function preparePreferencesViewContent(){
		
		$viewingProfileID = $this->_cubeDTO->getElementInstanceCreatorProfileID();
		
		$loggedInProfileID = $_SESSION['MAIN_PROFILE_ID'];
		
		$daoFactory = AbstractDAOFactory::getInstance();
        $userProfileDAO = $daoFactory->getUserProfileDAO();
        $userProfileDTO = $userProfileDAO->getProfile( $loggedInProfileID );
        
        $gender = $userProfileDTO->getSex();
        $hometown = $userProfileDTO->getHomeTown();
        $interestedInMen = $userProfileDTO->getInterestedInMen();
        $interestedInWomen = $userProfileDTO->getInterestedInWomen();
        $birthDate = date( 'Y-m-d', strtotime( $userProfileDTO->getBirthDate() ) );
        $lookingForFriendship = $userProfileDTO->getLookingForFriendship();
        $lookingForRelationship = $userProfileDTO->getLookingForRelationship();
        $lookingForDating = $userProfileDTO->getLookingForDating();
        $lookingForRandomPlay = $userProfileDTO->getLookingForRandomPlay();
        $lookingForDatingWhateverICanGet = $userProfileDTO->getLookingForWhateverICanGet();
        
        $this->_templateEngine->assign( 'gender', $gender );
		$this->_templateEngine->assign( 'interestedInMen', $interestedInMen );
		$this->_templateEngine->assign( 'interestedInWomen', $interestedInWomen );
		$this->_templateEngine->assign( 'hometown', $hometown );
		$this->_templateEngine->assign( 'lookingForFriendship', $lookingForFriendship );
		$this->_templateEngine->assign( 'lookingForRelationship', $lookingForRelationship );
		$this->_templateEngine->assign( 'lookingForDating', $lookingForDating );
		$this->_templateEngine->assign( 'lookingForRandomPlay', $lookingForRandomPlay );
		$this->_templateEngine->assign( 'lookingForDatingWhateverICanGet', $lookingForDatingWhateverICanGet );
		$this->_templateEngine->assign( 'birthDate', $birthDate );
		
	}
 	
}
?>
