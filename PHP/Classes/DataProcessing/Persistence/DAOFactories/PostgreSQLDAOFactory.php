<?php
/**
 * PostgreSQLDAOFactory Class File
 *
 * Needs to be commented
 * 
 * Copyright 2010 eGloo, LLC
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *		  http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *	
 * @author Keith Buel
 * @author George Cooper
 * @copyright 2010 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package Persistence
 * @version 1.0
 */

/**
 * PostgreSQLDAOFactory
 *
 * ConcreteDAOFactory subclass to create PGSQLDAO's
 * 
 * @package Persistence
 */
class PostgreSQLDAOFactory extends ConcreteDAOFactory {

	/**
	 * @return a PGSQLSessionDAO
	 */
	public function getSessionDAO() {
		return new PGSQLSessionDAO( $this->_connection_name ); 
	}

	public function getGlobalMenuBarDAO() {
		return new PGSQLGlobalMenuBarDAO( $this->_connection_name );
	}
	
	public function getInformationBoardIcingDAO() {
		return new PGSQLInformationBoardIcingDAO( $this->_connection_name );
	}

	public function getInformationBoardMusicDAO() {
		return new PGSQLInformationBoardMusicDAO( $this->_connection_name );
	}

	public function getInformationBoardPeopleDAO() {
		return new PGSQLInformationBoardPeopleDAO( $this->_connection_name );
	}

	public function getInformationBoardPicturesDAO() {
		return new PGSQLInformationBoardPicturesDAO( $this->_connection_name );
	}

	public function getInformationBoardVideoDAO() {
		return new PGSQLInformationBoardVideoDAO( $this->_connection_name );
	}

	public function getAccountDAO() {
		return new PGSQLAccountDAO( $this->_connection_name );
	}
	
	public function getUserProfileDAO() {
		return new PGSQLUserProfileDAO( $this->_connection_name );
	}

	public function getUserProfilePageDAO() {
		return new PGSQLUserProfilePageDAO( $this->_connection_name );
	}

	public function getFriendsDAO() {
		return new PGSQLFriendsDAO( $this->_connection_name );
	}
	
	public function getBlogDAO() {
		return new PGSQLBlogDAO( $this->_connection_name );
	}

	public function getCubeDAO() {
		return new PGSQLCubeDAO( $this->_connection_name );
	}

	public function getFridgeDAO() {
		return new PGSQLFridgeDAO( $this->_connection_name );
	}

	public function getUserInvitesDAO() {
		return new PGSQLUserInvitesDAO( $this->_connection_name );	
	}
	
	public function getSearchDAO() {
		return new PGSQLSearchDAO( $this->_connection_name );
	}
	
	public function getRelationshipDAO() {
		return new PGSQLRelationshipDAO( $this->_connection_name );
	}
	
	public function getImageDAO() {
		return new PGSQLImageDAO( $this->_connection_name );
	}
	
	public function getGenericCubeDAO() {
		return new PGSQLGenericCubeDAO( $this->_connection_name );
	}
	
	public function getAuctionDAO() {
		return new PGSQLAuctionDAO( $this->_connection_name );
	}
	
	public function getGenericPLFunctionDAO() {
		return new PGSQLGenericPLFunctionDAO( $this->_connection_name );
	}

}

