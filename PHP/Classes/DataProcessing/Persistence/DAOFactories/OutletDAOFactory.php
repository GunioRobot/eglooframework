<?php
/**
 * OutletDAOFactory Class File
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
 * @copyright 2010 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package $package
 * @subpackage $subpackage
 * @version 1.0
 */

/**
 * OutletDAOFactory
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class OutletDAOFactory extends DAOFactory {

	/**
	 * @return a OutletSQLSessionDAO
	 */
	public function getSessionDAO() {
		return new OutletSessionDAO( $this->_connection_name ); 
	}

	public function getGlobalMenuBarDAO() {
		return new OutletGlobalMenuBarDAO( $this->_connection_name );
	}
	
	public function getInformationBoardIcingDAO() {
		return new OutletInformationBoardIcingDAO( $this->_connection_name );
	}

	public function getInformationBoardMusicDAO() {
		return new OutletInformationBoardMusicDAO( $this->_connection_name );
	}

	public function getInformationBoardPeopleDAO() {
		return new OutletInformationBoardPeopleDAO( $this->_connection_name );
	}

	public function getInformationBoardPicturesDAO() {
		return new OutletInformationBoardPicturesDAO( $this->_connection_name );
	}

	public function getInformationBoardVideoDAO() {
		return new OutletInformationBoardVideoDAO( $this->_connection_name );
	}

	public function getAccountDAO() {
		return new OutletAccountDAO( $this->_connection_name );
	}
	
	public function getUserProfileDAO() {
		return new OutletUserProfileDAO( $this->_connection_name );
	}

	public function getUserProfilePageDAO() {
		return new OutletUserProfilePageDAO( $this->_connection_name );
	}

	public function getFriendsDAO() {
		return new OutletFriendsDAO( $this->_connection_name );
	}
	
	public function getBlogDAO() {
		return new OutletBlogDAO( $this->_connection_name );
	}

	public function getCubeDAO() {
		return new OutletCubeDAO( $this->_connection_name );
	}

	public function getFridgeDAO() {
		return new OutletFridgeDAO( $this->_connection_name );
	}

	public function getUserInvitesDAO() {
		return new OutletUserInvitesDAO( $this->_connection_name );	
	}
	
	public function getSearchDAO() {
		return new OutletSearchDAO( $this->_connection_name );
	}
	
	public function getRelationshipDAO() {
		return new OutletRelationshipDAO( $this->_connection_name );
	}
	
	public function getImageDAO() {
		return new OutletImageDAO( $this->_connection_name );
	}
	
	public function getGenericCubeDAO() {
		return new OutletGenericCubeDAO( $this->_connection_name );
	}
	
	public function getAuctionDAO() {
		return new OutletAuctionDAO( $this->_connection_name );
	}
	
	public function getGenericPLFunctionDAO() {
		return new OutletGenericPLFunctionDAO( $this->_connection_name );
	}

}
