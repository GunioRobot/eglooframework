<?php
/**
 * eGlooDBDAOFactory Class File
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
 * eGlooDBDAOFactory
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class eGlooDBDAOFactory extends ConcreteDAOFactory {

	/**
	 * @return a eGlooDBSQLSessionDAO
	 */
	public function getSessionDAO() {
		return new eGlooDBSessionDAO( $this->_connection_name ); 
	}

	public function getGlobalMenuBarDAO() {
		return new eGlooDBGlobalMenuBarDAO( $this->_connection_name );
	}
	
	public function getInformationBoardIcingDAO() {
		return new eGlooDBInformationBoardIcingDAO( $this->_connection_name );
	}

	public function getInformationBoardMusicDAO() {
		return new eGlooDBInformationBoardMusicDAO( $this->_connection_name );
	}

	public function getInformationBoardPeopleDAO() {
		return new eGlooDBInformationBoardPeopleDAO( $this->_connection_name );
	}

	public function getInformationBoardPicturesDAO() {
		return new eGlooDBInformationBoardPicturesDAO( $this->_connection_name );
	}

	public function getInformationBoardVideoDAO() {
		return new eGlooDBInformationBoardVideoDAO( $this->_connection_name );
	}

	public function getAccountDAO() {
		return new eGlooDBAccountDAO( $this->_connection_name );
	}
	
	public function getUserProfileDAO() {
		return new eGlooDBUserProfileDAO( $this->_connection_name );
	}

	public function getUserProfilePageDAO() {
		return new eGlooDBUserProfilePageDAO( $this->_connection_name );
	}

	public function getFriendsDAO() {
		return new eGlooDBFriendsDAO( $this->_connection_name );
	}
	
	public function getBlogDAO() {
		return new eGlooDBBlogDAO( $this->_connection_name );
	}

	public function getCubeDAO() {
		return new eGlooDBCubeDAO( $this->_connection_name );
	}

	public function getFridgeDAO() {
		return new eGlooDBFridgeDAO( $this->_connection_name );
	}

	public function getUserInvitesDAO() {
		return new eGlooDBUserInvitesDAO( $this->_connection_name );	
	}
	
	public function getSearchDAO() {
		return new eGlooDBSearchDAO( $this->_connection_name );
	}
	
	public function getRelationshipDAO() {
		return new eGlooDBRelationshipDAO( $this->_connection_name );
	}
	
	public function getImageDAO() {
		return new eGlooDBImageDAO( $this->_connection_name );
	}
	
	public function getGenericCubeDAO() {
		return new eGlooDBGenericCubeDAO( $this->_connection_name );
	}
	
	public function getAuctionDAO() {
		return new eGlooDBAuctionDAO( $this->_connection_name );
	}
	
	public function getGenericPLFunctionDAO() {
		return new eGlooDBGenericPLFunctionDAO( $this->_connection_name );
	}

}
