<?php
/**
 * RoutingDAOFactory Class File
 *
 * $file_block_description
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
 * @package $package
 * @subpackage $subpackage
 * @version 1.0
 */

/**
 * RoutingDAOFactory
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class RoutingDAOFactory extends ConcreteDAOFactory {

	/**
	 * @return a RoutingSessionDAO
	 */
	public function getSessionDAO() {
		return new RoutingSessionDAO( $this->_connection_name ); 
	}

	public function getGlobalMenuBarDAO() {
		return new RoutingGlobalMenuBarDAO( $this->_connection_name );
	}
	
	public function getInformationBoardIcingDAO() {
		return new RoutingInformationBoardIcingDAO( $this->_connection_name );
	}

	public function getInformationBoardMusicDAO() {
		return new RoutingInformationBoardMusicDAO( $this->_connection_name );
	}

	public function getInformationBoardPeopleDAO() {
		return new RoutingInformationBoardPeopleDAO( $this->_connection_name );
	}

	public function getInformationBoardPicturesDAO() {
		return new RoutingInformationBoardPicturesDAO( $this->_connection_name );
	}

	public function getInformationBoardVideoDAO() {
		return new RoutingInformationBoardVideoDAO( $this->_connection_name );
	}

	public function getAccountDAO() {
		return new RoutingAccountDAO( $this->_connection_name );
	}
	
	public function getUserProfileDAO() {
		return new RoutingUserProfileDAO( $this->_connection_name );
	}

	public function getUserProfilePageDAO() {
		return new RoutingUserProfilePageDAO( $this->_connection_name );
	}

	public function getFriendsDAO() {
		return new RoutingFriendsDAO( $this->_connection_name );
	}
	
	public function getBlogDAO() {
		return new RoutingBlogDAO( $this->_connection_name );
	}

	public function getCubeDAO() {
		return new RoutingCubeDAO( $this->_connection_name );
	}

	public function getFridgeDAO() {
		return new RoutingFridgeDAO( $this->_connection_name );
	}

	public function getUserInvitesDAO() {
		return new RoutingUserInvitesDAO( $this->_connection_name );	
	}
	
	public function getSearchDAO() {
		return new RoutingSearchDAO( $this->_connection_name );
	}
	
	public function getRelationshipDAO() {
		return new RoutingRelationshipDAO( $this->_connection_name );
	}
	
	public function getImageDAO() {
		return new RoutingImageDAO( $this->_connection_name );
	}
	
	public function getGenericCubeDAO() {
		return new RoutingGenericCubeDAO( $this->_connection_name );
	}
	
	public function getAuctionDAO() {
		return new RoutingAuctionDAO( $this->_connection_name );
	}
	
	public function getGenericPLFunctionDAO() {
		return new RoutingGenericPLFunctionDAO( $this->_connection_name );
	}

}