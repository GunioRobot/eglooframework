<?php
/**
 * AquinasDAOFactory Class File
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
 * AquinasDAOFactory
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class AquinasDAOFactory extends ConcreteDAOFactory {

	/**
	 * @return a AquinasSQLSessionDAO
	 */
	public function getSessionDAO() {
		return new AquinasSessionDAO( $this->_connection_name ); 
	}

	public function getGlobalMenuBarDAO() {
		return new AquinasGlobalMenuBarDAO( $this->_connection_name );
	}
	
	public function getInformationBoardIcingDAO() {
		return new AquinasInformationBoardIcingDAO( $this->_connection_name );
	}

	public function getInformationBoardMusicDAO() {
		return new AquinasInformationBoardMusicDAO( $this->_connection_name );
	}

	public function getInformationBoardPeopleDAO() {
		return new AquinasInformationBoardPeopleDAO( $this->_connection_name );
	}

	public function getInformationBoardPicturesDAO() {
		return new AquinasInformationBoardPicturesDAO( $this->_connection_name );
	}

	public function getInformationBoardVideoDAO() {
		return new AquinasInformationBoardVideoDAO( $this->_connection_name );
	}

	public function getAccountDAO() {
		return new AquinasAccountDAO( $this->_connection_name );
	}
	
	public function getUserProfileDAO() {
		return new AquinasUserProfileDAO( $this->_connection_name );
	}

	public function getUserProfilePageDAO() {
		return new AquinasUserProfilePageDAO( $this->_connection_name );
	}

	public function getFriendsDAO() {
		return new AquinasFriendsDAO( $this->_connection_name );
	}
	
	public function getBlogDAO() {
		return new AquinasBlogDAO( $this->_connection_name );
	}

	public function getCubeDAO() {
		return new AquinasCubeDAO( $this->_connection_name );
	}

	public function getFridgeDAO() {
		return new AquinasFridgeDAO( $this->_connection_name );
	}

	public function getUserInvitesDAO() {
		return new AquinasUserInvitesDAO( $this->_connection_name );	
	}
	
	public function getSearchDAO() {
		return new AquinasSearchDAO( $this->_connection_name );
	}
	
	public function getRelationshipDAO() {
		return new AquinasRelationshipDAO( $this->_connection_name );
	}
	
	public function getImageDAO() {
		return new AquinasImageDAO( $this->_connection_name );
	}
	
	public function getGenericCubeDAO() {
		return new AquinasGenericCubeDAO( $this->_connection_name );
	}
	
	public function getAuctionDAO() {
		return new AquinasAuctionDAO( $this->_connection_name );
	}
	
	public function getGenericPLFunctionDAO() {
		return new AquinasGenericPLFunctionDAO( $this->_connection_name );
	}

}
