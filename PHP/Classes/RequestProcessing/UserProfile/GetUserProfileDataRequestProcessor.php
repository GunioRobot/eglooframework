<?php
/**
 * GetUserProfileDataRequestProcessor Class File
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
 * @package RequestProcessing
 * @version 1.0
 */

/**
 * GetUserProfileDataRequestProcessor
 *
 * Needs to be commented
 *
 * @package RequestProcessing
 * @subpackage RequestProcessors
 */
class GetUserProfileDataRequestProcessor extends RequestProcessor {

    private static $_template = '../Templates/Applications/eGloo/InterfaceBundles/Default/XHTML/InternalMainPage/InternalMainPageContainer.tpl';

    public function processRequest() {
        $cacheGateway = CacheGateway::getCacheGateway();

        $format = $this->requestInfoBean->getGET( 'format' );
        $profileID = $this->requestInfoBean->getGET( 'profileID' );

        $daoFactory = AbstractDAOFactory::getInstance();
        $userProfileDAO = $daoFactory->getUserProfileDAO();
        $userProfileDTO = $userProfileDAO->getProfile( $profileID );

        $gender = $userProfileDTO->getSex();
        $hometown = $userProfileDTO->getHomeTown();
        $birthDate = date( 'F j, Y', strtotime( $userProfileDTO->getBirthDate() ) );

//        $lookingForFriendship = $userProfileDTO->getLookingForFriendship() ? UserProfileDTO::$FRIENDSHIP : '';
//        $lookingForRelationship = $userProfileDTO->getLookingForRelationship() ? UserProfileDTO::$RELATIONSHIP : '';
//        $lookingForDating = $userProfileDTO->getLookingForDating() ? UserProfileDTO::$DATING : '';
//        $lookingForRandomPlay = $userProfileDTO->getLookingForRandomPlay() ? UserProfileDTO::$RANDOMPLAY : '';
//        $lookingForDatingWhateverICanGet = $userProfileDTO->getLookingForWhateverICanGet() ? UserProfileDTO::$WHATEVER : '';

        $lookingForFriendship = $userProfileDTO->getLookingForFriendship();
        $lookingForRelationship = $userProfileDTO->getLookingForRelationship();
        $lookingForDating = $userProfileDTO->getLookingForDating();
        $lookingForRandomPlay = $userProfileDTO->getLookingForRandomPlay();
        $lookingForDatingWhateverICanGet = $userProfileDTO->getLookingForWhateverICanGet();

        $interestedInMen = $userProfileDTO->getInterestedInMen() ? UserProfileDTO::$MEN : '';
        $interestedInWomen = $userProfileDTO->getInterestedInWomen() ? UserProfileDTO::$WOMEN : '';

        if ( $format === 'json' ) {
            $output = '{gender:\'' . $gender;
            $output .= '\',hometown:\'' . $hometown;
            $output .= '\',birthDate:\'' . $birthDate;

            $output .= '\',sexualInterest:\'';

            if ( $interestedInMen !== '' && $interestedInWomen !== '' ) {
                $output .= UserProfileDTO::$MEN . ', ' . UserProfileDTO::$WOMEN;
            } else if ( $interestedInMen !== '' ) {
                $output .= UserProfileDTO::$MEN;
            } else if ( $interestedInWomen !== '' ) {
                $output .= UserProfileDTO::$WOMEN;
            }

            $output .= '\',lookingFor:\'';

            $output .= $lookingForFriendship !== false ? UserProfileDTO::$FRIENDSHIP . ', ': '';
            $output .= $lookingForRelationship !== false ? UserProfileDTO::$RELATIONSHIP . ', ': '';
            $output .= $lookingForDating !== false ? UserProfileDTO::$DATING . ', ': '';
            $output .= $lookingForRandomPlay !== false ? UserProfileDTO::$RANDOMPLAY . ', ': '';
            $output .= $lookingForDatingWhateverICanGet !== false ? UserProfileDTO::$WHATEVER : '';
            $output .= '\'}';
        } else if ( $format === 'xml' ) {

        } else {
            // TODO throw exception
        }

        header("Content-type: text/html; charset=UTF-8");
        echo $output;
    }

}

?>