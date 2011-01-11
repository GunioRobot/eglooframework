<?php
/**
 * MongoDBDAOFactory Class File
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
 * MongoDBDAOFactory
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class MongoDBDAOFactory extends ConcreteDAOFactory {

	/**
	 * @return a MongoDBSQLSessionDAO
	 */
	public function getSessionDAO() {
		return new MongoDBSessionDAO( $this->_connection_name ); 
	}

	public function getAccountDAO() {
		return new MongoDBAccountDAO( $this->_connection_name );
	}
	
	public function getUserProfileDAO() {
		return new MongoDBUserProfileDAO( $this->_connection_name );
	}

	public function getUserProfilePageDAO() {
		return new MongoDBUserProfilePageDAO( $this->_connection_name );
	}

	public function getFriendsDAO() {
		return new MongoDBFriendsDAO( $this->_connection_name );
	}
	
	public function getBlogDAO() {
		return new MongoDBBlogDAO( $this->_connection_name );
	}

	public function getCubeDAO() {
		return new MongoDBCubeDAO( $this->_connection_name );
	}

	public function getUserInvitesDAO() {
		return new MongoDBUserInvitesDAO( $this->_connection_name );	
	}
	
	public function getSearchDAO() {
		return new MongoDBSearchDAO( $this->_connection_name );
	}
	
	public function getRelationshipDAO() {
		return new MongoDBRelationshipDAO( $this->_connection_name );
	}
	
	public function getImageDAO() {
		return new MongoDBImageDAO( $this->_connection_name );
	}
	
	public function getGenericCubeDAO() {
		return new MongoDBGenericCubeDAO( $this->_connection_name );
	}

	public function getGenericPLFunctionDAO() {
		return new MongoDBGenericPLFunctionDAO( $this->_connection_name );
	}

}
