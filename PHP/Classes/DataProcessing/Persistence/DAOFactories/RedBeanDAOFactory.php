<?php
/**
 * RedBeanDAOFactory Class File
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
 * RedBeanDAOFactory
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class RedBeanDAOFactory extends ConcreteDAOFactory {

	/**
	 * @return a RedBeanSQLSessionDAO
	 */
	public function getSessionDAO() {
		return new RedBeanSessionDAO( $this->_connection_name ); 
	}

	public function getAccountDAO() {
		return new RedBeanAccountDAO( $this->_connection_name );
	}
	
	public function getUserProfileDAO() {
		return new RedBeanUserProfileDAO( $this->_connection_name );
	}

	public function getUserProfilePageDAO() {
		return new RedBeanUserProfilePageDAO( $this->_connection_name );
	}

	public function getFriendsDAO() {
		return new RedBeanFriendsDAO( $this->_connection_name );
	}
	
	public function getBlogDAO() {
		return new RedBeanBlogDAO( $this->_connection_name );
	}

	public function getCubeDAO() {
		return new RedBeanCubeDAO( $this->_connection_name );
	}

	public function getUserInvitesDAO() {
		return new RedBeanUserInvitesDAO( $this->_connection_name );	
	}
	
	public function getSearchDAO() {
		return new RedBeanSearchDAO( $this->_connection_name );
	}
	
	public function getRelationshipDAO() {
		return new RedBeanRelationshipDAO( $this->_connection_name );
	}
	
	public function getImageDAO() {
		return new RedBeanImageDAO( $this->_connection_name );
	}
	
	public function getGenericCubeDAO() {
		return new RedBeanGenericCubeDAO( $this->_connection_name );
	}

	public function getGenericPLFunctionDAO() {
		return new RedBeanGenericPLFunctionDAO( $this->_connection_name );
	}

}
