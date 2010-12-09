<?php
/**
 * MySQLiOOPDAOFactory Class File
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
 * MySQLiOOPDAOFactory
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class MySQLiOOPDAOFactory extends DAOFactory {

	/**
	 * @return a MySQLiOOPSQLSessionDAO
	 */
	public function getSessionDAO() {
		return new MySQLiOOPSessionDAO( $this->_connection_name ); 
	}

	public function getGlobalMenuBarDAO() {
		return new MySQLiOOPGlobalMenuBarDAO( $this->_connection_name );
	}
	
	public function getInformationBoardIcingDAO() {
		return new MySQLiOOPInformationBoardIcingDAO( $this->_connection_name );
	}

	public function getInformationBoardMusicDAO() {
		return new MySQLiOOPInformationBoardMusicDAO( $this->_connection_name );
	}

	public function getInformationBoardPeopleDAO() {
		return new MySQLiOOPInformationBoardPeopleDAO( $this->_connection_name );
	}

	public function getInformationBoardPicturesDAO() {
		return new MySQLiOOPInformationBoardPicturesDAO( $this->_connection_name );
	}

	public function getInformationBoardVideoDAO() {
		return new MySQLiOOPInformationBoardVideoDAO( $this->_connection_name );
	}

	public function getAccountDAO() {
		return new MySQLiOOPAccountDAO( $this->_connection_name );
	}
	
	public function getUserProfileDAO() {
		return new MySQLiOOPUserProfileDAO( $this->_connection_name );
	}

	public function getUserProfilePageDAO() {
		return new MySQLiOOPUserProfilePageDAO( $this->_connection_name );
	}

	public function getFriendsDAO() {
		return new MySQLiOOPFriendsDAO( $this->_connection_name );
	}
	
	public function getBlogDAO() {
		return new MySQLiOOPBlogDAO( $this->_connection_name );
	}

	public function getCubeDAO() {
		return new MySQLiOOPCubeDAO( $this->_connection_name );
	}

	public function getFridgeDAO() {
		return new MySQLiOOPFridgeDAO( $this->_connection_name );
	}

	public function getUserInvitesDAO() {
		return new MySQLiOOPUserInvitesDAO( $this->_connection_name );	
	}
	
	public function getSearchDAO() {
		return new MySQLiOOPSearchDAO( $this->_connection_name );
	}
	
	public function getRelationshipDAO() {
		return new MySQLiOOPRelationshipDAO( $this->_connection_name );
	}
	
	public function getImageDAO() {
		return new MySQLiOOPImageDAO( $this->_connection_name );
	}
	
	public function getGenericCubeDAO() {
		return new MySQLiOOPGenericCubeDAO( $this->_connection_name );
	}
	
	public function getAuctionDAO() {
		return new MySQLiOOPAuctionDAO( $this->_connection_name );
	}
	
	public function getGenericPLFunctionDAO() {
		return new MySQLiOOPGenericPLFunctionDAO( $this->_connection_name );
	}

}
