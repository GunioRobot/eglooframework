<?php
/**
 * ActiveRecordDAOFactory Class File
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
 * ActiveRecordDAOFactory
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class ActiveRecordDAOFactory extends DAOFactory {

	/**
	 * @return a ActiveRecordSQLSessionDAO
	 */
	public function getSessionDAO() {
		return new ActiveRecordSessionDAO( $this->_connection_name ); 
	}

	public function getGlobalMenuBarDAO() {
		return new ActiveRecordGlobalMenuBarDAO( $this->_connection_name );
	}
	
	public function getInformationBoardIcingDAO() {
		return new ActiveRecordInformationBoardIcingDAO( $this->_connection_name );
	}

	public function getInformationBoardMusicDAO() {
		return new ActiveRecordInformationBoardMusicDAO( $this->_connection_name );
	}

	public function getInformationBoardPeopleDAO() {
		return new ActiveRecordInformationBoardPeopleDAO( $this->_connection_name );
	}

	public function getInformationBoardPicturesDAO() {
		return new ActiveRecordInformationBoardPicturesDAO( $this->_connection_name );
	}

	public function getInformationBoardVideoDAO() {
		return new ActiveRecordInformationBoardVideoDAO( $this->_connection_name );
	}

	public function getAccountDAO() {
		return new ActiveRecordAccountDAO( $this->_connection_name );
	}
	
	public function getUserProfileDAO() {
		return new ActiveRecordUserProfileDAO( $this->_connection_name );
	}

	public function getUserProfilePageDAO() {
		return new ActiveRecordUserProfilePageDAO( $this->_connection_name );
	}

	public function getFriendsDAO() {
		return new ActiveRecordFriendsDAO( $this->_connection_name );
	}
	
	public function getBlogDAO() {
		return new ActiveRecordBlogDAO( $this->_connection_name );
	}

	public function getCubeDAO() {
		return new ActiveRecordCubeDAO( $this->_connection_name );
	}

	public function getFridgeDAO() {
		return new ActiveRecordFridgeDAO( $this->_connection_name );
	}

	public function getUserInvitesDAO() {
		return new ActiveRecordUserInvitesDAO( $this->_connection_name );	
	}
	
	public function getSearchDAO() {
		return new ActiveRecordSearchDAO( $this->_connection_name );
	}
	
	public function getRelationshipDAO() {
		return new ActiveRecordRelationshipDAO( $this->_connection_name );
	}
	
	public function getImageDAO() {
		return new ActiveRecordImageDAO( $this->_connection_name );
	}
	
	public function getGenericCubeDAO() {
		return new ActiveRecordGenericCubeDAO( $this->_connection_name );
	}
	
	public function getAuctionDAO() {
		return new ActiveRecordAuctionDAO( $this->_connection_name );
	}
	
	public function getGenericPLFunctionDAO() {
		return new ActiveRecordGenericPLFunctionDAO( $this->_connection_name );
	}

}
