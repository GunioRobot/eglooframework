<?php
/**
 * MySQLiDAOFactory Class File
 *
 * $file_block_description
 * 
 * Copyright 2011 eGloo, LLC
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
 * MySQLiDAOFactory
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class MySQLiDAOFactory extends ConcreteDAOFactory {

	/**
	 * @return a MySQLiSQLSessionDAO
	 */
	public function getSessionDAO() {
		return new MySQLiSessionDAO( $this->_connection_name ); 
	}

	public function getAccountDAO() {
		return new MySQLiAccountDAO( $this->_connection_name );
	}
	
	public function getUserProfileDAO() {
		return new MySQLiUserProfileDAO( $this->_connection_name );
	}

	public function getUserProfilePageDAO() {
		return new MySQLiUserProfilePageDAO( $this->_connection_name );
	}

	public function getFriendsDAO() {
		return new MySQLiFriendsDAO( $this->_connection_name );
	}
	
	public function getBlogDAO() {
		return new MySQLiBlogDAO( $this->_connection_name );
	}

	public function getCubeDAO() {
		return new MySQLiCubeDAO( $this->_connection_name );
	}

	public function getUserInvitesDAO() {
		return new MySQLiUserInvitesDAO( $this->_connection_name );	
	}
	
	public function getSearchDAO() {
		return new MySQLiSearchDAO( $this->_connection_name );
	}
	
	public function getRelationshipDAO() {
		return new MySQLiRelationshipDAO( $this->_connection_name );
	}
	
	public function getImageDAO() {
		return new MySQLiImageDAO( $this->_connection_name );
	}
	
	public function getGenericCubeDAO() {
		return new MySQLiGenericCubeDAO( $this->_connection_name );
	}

	public function getGenericPLFunctionDAO() {
		return new MySQLiGenericPLFunctionDAO( $this->_connection_name );
	}

}
