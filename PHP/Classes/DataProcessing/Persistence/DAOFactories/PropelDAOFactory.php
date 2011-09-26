<?php
/**
 * PropelDAOFactory Class File
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
 * @category DataProcessing
 * @package Persistence
 * @subpackage DAOFactories
 * @version 1.0
 */

/**
 * PropelDAOFactory
 *
 * $short_description
 *
 * $long_description
 *
 * @category DataProcessing
 * @package Persistence
 * @subpackage DAOFactories
 */
class PropelDAOFactory extends ConcreteDAOFactory {

	/**
	 * @return a PropelSQLSessionDAO
	 */
	public function getSessionDAO() {
		return new PropelSessionDAO( $this->_connection_name ); 
	}

	public function getCubeDAO() {
		return new PropelCubeDAO( $this->_connection_name );
	}

	public function getGenericCubeDAO() {
		return new PropelGenericCubeDAO( $this->_connection_name );
	}

	public function getGenericPLFunctionDAO() {
		return new PropelGenericPLFunctionDAO( $this->_connection_name );
	}

}
