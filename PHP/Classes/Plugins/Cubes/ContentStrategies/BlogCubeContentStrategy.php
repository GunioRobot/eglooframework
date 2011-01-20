<?php
/**
 * BlogCubeContentStrategy Class File
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
 * BlogCubeContentStrategy
 * 
 * Needs to be commented
 *
 * @package ContentProcessing
 */
class BlogCubeContentStrategy extends CubeContentStrategy {

	public function prepareContentViewContent(){

		$viewingProfileID = $this->_cubeDTO->getElementInstanceCreatorProfileID();
		
		$loggedInProfileID = $_SESSION['MAIN_PROFILE_ID'];
		
		$daoFactory = AbstractDAOFactory::getInstance();
        $blogDTOArray = $daoFactory->getBlogDAO()->viewBlogEntryList( $viewingProfileID, $loggedInProfileID  );

        $this->_templateEngine->assign( 'blogDTOArray', $blogDTOArray );
        $this->_templateEngine->assign( 'rankable', $this->_cubeDTO->getRankable() );
	}

	public function preparePreferencesViewContent(){}
 	
}
?>
