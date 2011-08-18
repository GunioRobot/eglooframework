<?php
/**
 * PostgreSQLDAOFactory Class File
 *
 * Needs to be commented
 * 
 * Copyright 2011 eGloo, LLC
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *		  http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *	
 * @author Keith Buel
 * @author George Cooper
 * @copyright 2011 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @category DataProcessing
 * @package Persistence
 * @subpackage DAOFactories
 * @version 1.0
 */

/**
 * PostgreSQLDAOFactory
 *
 * ConcreteDAOFactory subclass to create PGSQLDAO's
 * 
 * @category DataProcessing
 * @package Persistence
 * @subpackage DAOFactories
 */
class PostgreSQLDAOFactory extends ConcreteDAOFactory {

	/**
	 * @return a PGSQLSessionDAO
	 */
	public function getSessionDAO() {
		return new PGSQLSessionDAO( $this->_connection_name ); 
	}

	public function getCubeDAO() {
		return new PGSQLCubeDAO( $this->_connection_name );
	}

	public function getGenericCubeDAO() {
		return new PGSQLGenericCubeDAO( $this->_connection_name );
	}

	public function getGenericPLFunctionDAO() {
		return new PGSQLGenericPLFunctionDAO( $this->_connection_name );
	}

	public function __call( $method, $args ) {
		$genericDAORequested = substr( $method, 3 );
		$concreteDAORequested = 'PGSQL' . $genericDAORequested;
		return new $concreteDAORequested( $this->_connection_name );
	}

}

