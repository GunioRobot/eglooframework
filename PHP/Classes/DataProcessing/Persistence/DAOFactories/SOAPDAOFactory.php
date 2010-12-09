<?php
/**
 * SOAPDAOFactory Class File
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
 * SOAPDAOFactory
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class SOAPDAOFactory extends DAOFactory {

	/**
	 * @return a SOAPSQLSessionDAO
	 */
	public function getSessionDAO() {
		return new SOAPSessionDAO( $this->_connection_name ); 
	}

	public function getGlobalMenuBarDAO() {
		return new SOAPGlobalMenuBarDAO( $this->_connection_name );
	}
	
	public function getInformationBoardIcingDAO() {
		return new SOAPInformationBoardIcingDAO( $this->_connection_name );
	}

	public function getInformationBoardMusicDAO() {
		return new SOAPInformationBoardMusicDAO( $this->_connection_name );
	}

	public function getInformationBoardPeopleDAO() {
		return new SOAPInformationBoardPeopleDAO( $this->_connection_name );
	}

	public function getInformationBoardPicturesDAO() {
		return new SOAPInformationBoardPicturesDAO( $this->_connection_name );
	}

	public function getInformationBoardVideoDAO() {
		return new SOAPInformationBoardVideoDAO( $this->_connection_name );
	}

	public function getAccountDAO() {
		return new SOAPAccountDAO( $this->_connection_name );
	}
	
	public function getUserProfileDAO() {
		return new SOAPUserProfileDAO( $this->_connection_name );
	}

	public function getUserProfilePageDAO() {
		return new SOAPUserProfilePageDAO( $this->_connection_name );
	}

	public function getFriendsDAO() {
		return new SOAPFriendsDAO( $this->_connection_name );
	}
	
	public function getBlogDAO() {
		return new SOAPBlogDAO( $this->_connection_name );
	}

	public function getCubeDAO() {
		return new SOAPCubeDAO( $this->_connection_name );
	}

	public function getFridgeDAO() {
		return new SOAPFridgeDAO( $this->_connection_name );
	}

	public function getUserInvitesDAO() {
		return new SOAPUserInvitesDAO( $this->_connection_name );	
	}
	
	public function getSearchDAO() {
		return new SOAPSearchDAO( $this->_connection_name );
	}
	
	public function getRelationshipDAO() {
		return new SOAPRelationshipDAO( $this->_connection_name );
	}
	
	public function getImageDAO() {
		return new SOAPImageDAO( $this->_connection_name );
	}
	
	public function getGenericCubeDAO() {
		return new SOAPGenericCubeDAO( $this->_connection_name );
	}
	
	public function getAuctionDAO() {
		return new SOAPAuctionDAO( $this->_connection_name );
	}
	
	public function getGenericPLFunctionDAO() {
		return new SOAPGenericPLFunctionDAO( $this->_connection_name );
	}

}
