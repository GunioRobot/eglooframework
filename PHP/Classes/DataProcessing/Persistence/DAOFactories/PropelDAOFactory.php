<?php
/**
 * PropelDAOFactory Class File
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
 * PropelDAOFactory
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class PropelDAOFactory extends ConcreteDAOFactory {

	/**
	 * @return a PropelSQLSessionDAO
	 */
	public function getSessionDAO() {
		return new PropelSessionDAO( $this->_connection_name ); 
	}

	public function getAccountDAO() {
		return new PropelAccountDAO( $this->_connection_name );
	}
	
	public function getUserProfileDAO() {
		return new PropelUserProfileDAO( $this->_connection_name );
	}

	public function getUserProfilePageDAO() {
		return new PropelUserProfilePageDAO( $this->_connection_name );
	}

	public function getFriendsDAO() {
		return new PropelFriendsDAO( $this->_connection_name );
	}
	
	public function getBlogDAO() {
		return new PropelBlogDAO( $this->_connection_name );
	}

	public function getCubeDAO() {
		return new PropelCubeDAO( $this->_connection_name );
	}

	public function getUserInvitesDAO() {
		return new PropelUserInvitesDAO( $this->_connection_name );	
	}
	
	public function getSearchDAO() {
		return new PropelSearchDAO( $this->_connection_name );
	}
	
	public function getRelationshipDAO() {
		return new PropelRelationshipDAO( $this->_connection_name );
	}
	
	public function getImageDAO() {
		return new PropelImageDAO( $this->_connection_name );
	}
	
	public function getGenericCubeDAO() {
		return new PropelGenericCubeDAO( $this->_connection_name );
	}

	public function getGenericPLFunctionDAO() {
		return new PropelGenericPLFunctionDAO( $this->_connection_name );
	}

}
