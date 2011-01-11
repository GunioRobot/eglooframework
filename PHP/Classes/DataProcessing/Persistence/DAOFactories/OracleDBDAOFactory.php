<?php
/**
 * OracleDBDAOFactory Class File
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
 * OracleDBDAOFactory
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class OracleDBDAOFactory extends ConcreteDAOFactory {

	/**
	 * @return a OracleDBSQLSessionDAO
	 */
	public function getSessionDAO() {
		return new OracleDBSessionDAO( $this->_connection_name ); 
	}

	public function getAccountDAO() {
		return new OracleDBAccountDAO( $this->_connection_name );
	}
	
	public function getUserProfileDAO() {
		return new OracleDBUserProfileDAO( $this->_connection_name );
	}

	public function getUserProfilePageDAO() {
		return new OracleDBUserProfilePageDAO( $this->_connection_name );
	}

	public function getFriendsDAO() {
		return new OracleDBFriendsDAO( $this->_connection_name );
	}
	
	public function getBlogDAO() {
		return new OracleDBBlogDAO( $this->_connection_name );
	}

	public function getCubeDAO() {
		return new OracleDBCubeDAO( $this->_connection_name );
	}

	public function getUserInvitesDAO() {
		return new OracleDBUserInvitesDAO( $this->_connection_name );	
	}
	
	public function getSearchDAO() {
		return new OracleDBSearchDAO( $this->_connection_name );
	}
	
	public function getRelationshipDAO() {
		return new OracleDBRelationshipDAO( $this->_connection_name );
	}
	
	public function getImageDAO() {
		return new OracleDBImageDAO( $this->_connection_name );
	}
	
	public function getGenericCubeDAO() {
		return new OracleDBGenericCubeDAO( $this->_connection_name );
	}

	public function getGenericPLFunctionDAO() {
		return new OracleDBGenericPLFunctionDAO( $this->_connection_name );
	}

}
