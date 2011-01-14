<?php
/**
 * CassandraDAOFactory Class File
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
 * CassandraDAOFactory
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class CassandraDAOFactory extends ConcreteDAOFactory {

	/**
	 * @return a CassandraSQLSessionDAO
	 */
	public function getSessionDAO() {
		return new CassandraSessionDAO( $this->_connection_name ); 
	}

	public function getAccountDAO() {
		return new CassandraAccountDAO( $this->_connection_name );
	}
	
	public function getUserProfileDAO() {
		return new CassandraUserProfileDAO( $this->_connection_name );
	}

	public function getUserProfilePageDAO() {
		return new CassandraUserProfilePageDAO( $this->_connection_name );
	}

	public function getFriendsDAO() {
		return new CassandraFriendsDAO( $this->_connection_name );
	}
	
	public function getBlogDAO() {
		return new CassandraBlogDAO( $this->_connection_name );
	}

	public function getCubeDAO() {
		return new CassandraCubeDAO( $this->_connection_name );
	}

	public function getUserInvitesDAO() {
		return new CassandraUserInvitesDAO( $this->_connection_name );	
	}
	
	public function getSearchDAO() {
		return new CassandraSearchDAO( $this->_connection_name );
	}
	
	public function getRelationshipDAO() {
		return new CassandraRelationshipDAO( $this->_connection_name );
	}
	
	public function getImageDAO() {
		return new CassandraImageDAO( $this->_connection_name );
	}
	
	public function getGenericCubeDAO() {
		return new CassandraGenericCubeDAO( $this->_connection_name );
	}

	public function getGenericPLFunctionDAO() {
		return new CassandraGenericPLFunctionDAO( $this->_connection_name );
	}

}
