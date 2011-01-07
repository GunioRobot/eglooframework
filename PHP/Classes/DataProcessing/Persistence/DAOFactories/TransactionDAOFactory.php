<?php
/**
 * TransactionDAOFactory Class File
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
 * TransactionDAOFactory
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class TransactionDAOFactory extends ConcreteDAOFactory {

	/**
	 * @return a TransactionSessionDAO
	 */
	public function getSessionDAO() {
		return new TransactionSessionDAO( $this->_connection_name ); 
	}

	public function getGlobalMenuBarDAO() {
		return new TransactionGlobalMenuBarDAO( $this->_connection_name );
	}
	
	public function getInformationBoardIcingDAO() {
		return new TransactionInformationBoardIcingDAO( $this->_connection_name );
	}

	public function getInformationBoardMusicDAO() {
		return new TransactionInformationBoardMusicDAO( $this->_connection_name );
	}

	public function getInformationBoardPeopleDAO() {
		return new TransactionInformationBoardPeopleDAO( $this->_connection_name );
	}

	public function getInformationBoardPicturesDAO() {
		return new TransactionInformationBoardPicturesDAO( $this->_connection_name );
	}

	public function getInformationBoardVideoDAO() {
		return new TransactionInformationBoardVideoDAO( $this->_connection_name );
	}

	public function getAccountDAO() {
		return new TransactionAccountDAO( $this->_connection_name );
	}
	
	public function getUserProfileDAO() {
		return new TransactionUserProfileDAO( $this->_connection_name );
	}

	public function getUserProfilePageDAO() {
		return new TransactionUserProfilePageDAO( $this->_connection_name );
	}

	public function getFriendsDAO() {
		return new TransactionFriendsDAO( $this->_connection_name );
	}
	
	public function getBlogDAO() {
		return new TransactionBlogDAO( $this->_connection_name );
	}

	public function getCubeDAO() {
		return new TransactionCubeDAO( $this->_connection_name );
	}

	public function getFridgeDAO() {
		return new TransactionFridgeDAO( $this->_connection_name );
	}

	public function getUserInvitesDAO() {
		return new TransactionUserInvitesDAO( $this->_connection_name );	
	}
	
	public function getSearchDAO() {
		return new TransactionSearchDAO( $this->_connection_name );
	}
	
	public function getRelationshipDAO() {
		return new TransactionRelationshipDAO( $this->_connection_name );
	}
	
	public function getImageDAO() {
		return new TransactionImageDAO( $this->_connection_name );
	}
	
	public function getGenericCubeDAO() {
		return new TransactionGenericCubeDAO( $this->_connection_name );
	}
	
	public function getAuctionDAO() {
		return new TransactionAuctionDAO( $this->_connection_name );
	}
	
	public function getGenericPLFunctionDAO() {
		return new TransactionGenericPLFunctionDAO( $this->_connection_name );
	}

}