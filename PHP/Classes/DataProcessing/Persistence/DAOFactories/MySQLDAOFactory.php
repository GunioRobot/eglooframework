<?php
/**
 * MySQLDAOFactory Class File
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
 * MySQLDAOFactory
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class MySQLDAOFactory extends ConcreteDAOFactory {

	/**
	 * @return a MySQLSQLSessionDAO
	 */
	public function getSessionDAO() {
		return new MySQLSessionDAO( $this->_connection_name ); 
	}

	public function getGlobalMenuBarDAO() {
		return new MySQLGlobalMenuBarDAO( $this->_connection_name );
	}
	
	public function getInformationBoardIcingDAO() {
		return new MySQLInformationBoardIcingDAO( $this->_connection_name );
	}

	public function getInformationBoardMusicDAO() {
		return new MySQLInformationBoardMusicDAO( $this->_connection_name );
	}

	public function getInformationBoardPeopleDAO() {
		return new MySQLInformationBoardPeopleDAO( $this->_connection_name );
	}

	public function getInformationBoardPicturesDAO() {
		return new MySQLInformationBoardPicturesDAO( $this->_connection_name );
	}

	public function getInformationBoardVideoDAO() {
		return new MySQLInformationBoardVideoDAO( $this->_connection_name );
	}

	public function getAccountDAO() {
		return new MySQLAccountDAO( $this->_connection_name );
	}
	
	public function getUserProfileDAO() {
		return new MySQLUserProfileDAO( $this->_connection_name );
	}

	public function getUserProfilePageDAO() {
		return new MySQLUserProfilePageDAO( $this->_connection_name );
	}

	public function getFriendsDAO() {
		return new MySQLFriendsDAO( $this->_connection_name );
	}
	
	public function getBlogDAO() {
		return new MySQLBlogDAO( $this->_connection_name );
	}

	public function getCubeDAO() {
		return new MySQLCubeDAO( $this->_connection_name );
	}

	public function getFridgeDAO() {
		return new MySQLFridgeDAO( $this->_connection_name );
	}

	public function getUserInvitesDAO() {
		return new MySQLUserInvitesDAO( $this->_connection_name );	
	}
	
	public function getSearchDAO() {
		return new MySQLSearchDAO( $this->_connection_name );
	}
	
	public function getRelationshipDAO() {
		return new MySQLRelationshipDAO( $this->_connection_name );
	}
	
	public function getImageDAO() {
		return new MySQLImageDAO( $this->_connection_name );
	}
	
	public function getGenericCubeDAO() {
		return new MySQLGenericCubeDAO( $this->_connection_name );
	}
	
	public function getAuctionDAO() {
		return new MySQLAuctionDAO( $this->_connection_name );
	}
	
	public function getGenericPLFunctionDAO() {
		return new MySQLGenericPLFunctionDAO( $this->_connection_name );
	}

}
