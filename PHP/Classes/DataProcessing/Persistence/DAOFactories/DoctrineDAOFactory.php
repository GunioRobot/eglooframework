<?php
/**
 * DoctrineDAOFactory Class File
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
 * DoctrineDAOFactory
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class DoctrineDAOFactory extends ConcreteDAOFactory {

	/**
	 * @return a DoctrineSQLSessionDAO
	 */
	public function getSessionDAO() {
		return new DoctrineSessionDAO( $this->_connection_name ); 
	}

	public function getAccountDAO() {
		return new DoctrineAccountDAO( $this->_connection_name );
	}
	
	public function getUserProfileDAO() {
		return new DoctrineUserProfileDAO( $this->_connection_name );
	}

	public function getUserProfilePageDAO() {
		return new DoctrineUserProfilePageDAO( $this->_connection_name );
	}

	public function getFriendsDAO() {
		return new DoctrineFriendsDAO( $this->_connection_name );
	}
	
	public function getBlogDAO() {
		return new DoctrineBlogDAO( $this->_connection_name );
	}

	public function getCubeDAO() {
		return new DoctrineCubeDAO( $this->_connection_name );
	}

	public function getUserInvitesDAO() {
		return new DoctrineUserInvitesDAO( $this->_connection_name );	
	}
	
	public function getSearchDAO() {
		return new DoctrineSearchDAO( $this->_connection_name );
	}
	
	public function getRelationshipDAO() {
		return new DoctrineRelationshipDAO( $this->_connection_name );
	}
	
	public function getImageDAO() {
		return new DoctrineImageDAO( $this->_connection_name );
	}
	
	public function getGenericCubeDAO() {
		return new DoctrineGenericCubeDAO( $this->_connection_name );
	}

	public function getGenericPLFunctionDAO() {
		return new DoctrineGenericPLFunctionDAO( $this->_connection_name );
	}

}
