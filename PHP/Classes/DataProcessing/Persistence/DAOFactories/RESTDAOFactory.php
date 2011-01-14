<?php
/**
 * RESTDAOFactory Class File
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
 * RESTDAOFactory
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class RESTDAOFactory extends ConcreteDAOFactory {

	/**
	 * @return a RESTSQLSessionDAO
	 */
	public function getSessionDAO() {
		return new RESTSessionDAO( $this->_connection_name ); 
	}

	public function getAccountDAO() {
		return new RESTAccountDAO( $this->_connection_name );
	}
	
	public function getUserProfileDAO() {
		return new RESTUserProfileDAO( $this->_connection_name );
	}

	public function getUserProfilePageDAO() {
		return new RESTUserProfilePageDAO( $this->_connection_name );
	}

	public function getFriendsDAO() {
		return new RESTFriendsDAO( $this->_connection_name );
	}
	
	public function getBlogDAO() {
		return new RESTBlogDAO( $this->_connection_name );
	}

	public function getCubeDAO() {
		return new RESTCubeDAO( $this->_connection_name );
	}

	public function getUserInvitesDAO() {
		return new RESTUserInvitesDAO( $this->_connection_name );	
	}
	
	public function getSearchDAO() {
		return new RESTSearchDAO( $this->_connection_name );
	}
	
	public function getRelationshipDAO() {
		return new RESTRelationshipDAO( $this->_connection_name );
	}
	
	public function getImageDAO() {
		return new RESTImageDAO( $this->_connection_name );
	}
	
	public function getGenericCubeDAO() {
		return new RESTGenericCubeDAO( $this->_connection_name );
	}

	public function getGenericPLFunctionDAO() {
		return new RESTGenericPLFunctionDAO( $this->_connection_name );
	}

}
