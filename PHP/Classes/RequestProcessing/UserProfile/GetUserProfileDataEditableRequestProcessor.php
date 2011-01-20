<?php
/**
 * GetUserProfileDataEditableRequestProcessor Class File
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
 * @package RequestProcessing
 * @version 1.0
 */

/**
 * GetUserProfileDataEditableRequestProcessor
 * 
 * Needs to be commented
 * 
 * @package RequestProcessing
 * @subpackage RequestProcessors
 */
class GetUserProfileDataEditableRequestProcessor extends RequestProcessor {

    
    public function processRequest() {
        $cacheGateway = CacheGateway::getCacheGateway();
        
		$loggedInProfileID = $_SESSION['MAIN_PROFILE_ID'];
		
        $daoFactory = AbstractDAOFactory::getInstance();
        $userProfileDAO = $daoFactory->getUserProfileDAO();
        $userProfileDTO = $userProfileDAO->getProfile( $loggedInProfileID );
        
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
       
        
        $output = "{gender:'$gender',";

        $output .= "sexualInterest:'";
            
        if ( $interestedInMen !== '' && $interestedInWomen !== '' ) {
            $output .= UserProfileDTO::$MEN . ', ' . UserProfileDTO::$WOMEN;
        } else if ( $interestedInMen !== '' ) {
            $output .= UserProfileDTO::$MEN;
        } else if ( $interestedInWomen !== '' ) {
            $output .= UserProfileDTO::$WOMEN;
        }
		
		$output .= "',";
		
		 

        $birthMonth = date( 'm', strtotime( $userProfileDTO->getBirthDate() ) );
        $birthDay = date( 'd', strtotime( $userProfileDTO->getBirthDate() ) );
        $birthYear = date( 'Y', strtotime( $userProfileDTO->getBirthDate() ) );
        
        $output .= "hometown:'$hometown',";
        $output .= "birthMonth:'$birthMonth',"; 
        $output .= "birthDay:'$birthDay',"; 
        $output .= "birthYear:'$birthYear',"; 
        		

        $output .= "friendship:'$lookingForFriendship',";
        $output .= "relationship:'$lookingForRelationship',";
        $output .= "dating:'$lookingForDating',";
        $output .= "randomplay:'$lookingForRandomPlay',";
        $output .= "whatever:'$lookingForDatingWhateverICanGet'}";
            
            
        header("Content-type: script/javascript; charset=UTF-8");
        echo $output;
    }

}
 
 
?>
