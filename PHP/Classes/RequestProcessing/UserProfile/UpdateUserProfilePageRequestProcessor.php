<?php
/**
 * UpdateUserProfilePageRequestProcessor Class File
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
 * @copyright 2011 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package RequestProcessing
 * @version 1.0
 */

/**
 * UpdateUserProfilePageRequestProcessor
 * 
 * Needs to be commented
 * 
 * @package RequestProcessing
 * @subpackage RequestProcessors
 */
class UpdateUserProfilePageRequestProcessor extends RequestProcessor {

    public function processRequest() {

 		$profileID = $_SESSION['MAIN_PROFILE_ID'];
        $pageLayout = $this->requestInfoBean->getPOST( 'pageLayout' );
        
        $daoFactory = AbstractDAOFactory::getInstance();
        $userProfilePageDAO = $daoFactory->getUserProfilePageDAO();

		$userProfilePageDAO->deleteAllProfileCubes( $profileID );

        preg_match_all("/center(\d+\[\]=ElementContainer_-?\d+)/", $pageLayout, $matches);
 		
 		$rowNum = array();
 
  		foreach( $matches[1] as $aMatch ){
        	preg_match("/(\d+)\[\]=ElementContainer_(-?\d+)/", $aMatch, $columnAndID );
        	
			$column = $columnAndID[1];
			
        	if( !isset( $rowNum[ $column ] ) ) {
        		$rowNum[ $column ] = 0;
        	} else {
        		$rowNum[ $column ] = $rowNum[ $column ] + 1;
        	}
        	
       		$userProfilePageDAO->addCubeToPage( $profileID, $columnAndID[2], $columnAndID[1], $rowNum[ $column ] );
        }
        
    }

}

?>