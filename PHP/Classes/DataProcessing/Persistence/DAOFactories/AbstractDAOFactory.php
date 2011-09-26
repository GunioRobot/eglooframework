<?php
/**
 * AbstractDAOFactory Class File
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
 * AbstractDAOFactory
 *
 * This class defines an abstract AbstractDAOFactory.  It determines the appropriate
 * concrete AbstractDAOFactory.	 And then returns the correct requested DAO by calling
 * the appropriate functions on the concrete AbstractDAOFactory.
 *
 * Details of this pattern can be seen on the following website:
 * http://java.sun.com/blueprints/corej2eepatterns/Patterns/DataAccessObject.html
 *
 * @category DataProcessing
 * @package Persistence
 * @subpackage DAOFactories
 */
class AbstractDAOFactory {

	// Protected Data Members
	protected $_connection_name = null;

	//singleton holder
	protected static $singleton;


	public function __construct( $connection_name ) {
		$this->_connection_name = $connection_name;
	}

	/**
	 * Singleton access to this AbstractDAOFactory
	 * 
	 * @return AbstractDAOFactory the singleton reference of the AbstractDAOFactory
	 */
	public static function getInstance() {
		if ( !isset(static::$singleton) ) {
			static::$singleton = new static( null );
		}

		return static::$singleton;
	}

	/**
	 * This class returns the appropriate DAO factory as specified by
	 * an external property
	 * 
	 * @return AbstractDAOFactory a concrete DAO factory
	 */
	private function getAppropriateFactory( $connection_name = 'egPrimary' ) {
		$retVal = null;

		$connection_info = eGlooConfiguration::getDatabaseConnectionInfo($connection_name);

		if ( $connection_info['engine'] === eGlooConfiguration::POSTGRESQL ) {
			$retVal = new PostgreSQLDAOFactory( $connection_name );
		} else if ( $connection_info['engine'] === eGlooConfiguration::MYSQLIOOP ) {
			$retVal = new MySQLiOOPDAOFactory( $connection_name );
		} else if ( $connection_info['engine'] === eGlooConfiguration::MYSQLI ) {
			$retVal = new MySQLiDAOFactory( $connection_name );
		} else if ( $connection_info['engine'] === eGlooConfiguration::MYSQL ) {
			$retVal = new MySQLDAOFactory( $connection_name );
		} else if ( $connection_info['engine'] === eGlooConfiguration::DOCTRINE ) {
			$retVal = new DoctrineDAOFactory( $connection_name );
		} else {
			// No connection specified and no default given...
		}

		return $retVal;
	}

	/**
	 * This method returns the correct SessionDAO by calling the 
	 * appropriate concrete factory
	 * 
	 * @return SessionDAO concrete DAO obtained from the concrete factory
	 */
	public function getSessionDAO( $connection_name = 'egPrimary' ) {
		return $this->getAppropriateFactory( $connection_name )->getSessionDAO();
	}

	public function getCubeDAO( $connection_name = 'egPrimary' ) {
		return $this->getAppropriateFactory( $connection_name )->getCubeDAO();
	}

	public function getGenericCubeDAO( $connection_name = 'egPrimary' ) {
		return $this->getAppropriateFactory( $connection_name )->getGenericCubeDAO();
	}

	public function getGenericPLFunctionDAO( $connection_name = 'egPrimary' ) {
		return $this->getAppropriateFactory( $connection_name )->getGenericPLFunctionDAO();
	}

	public function __call( $method, $args ) {
		return $this->getAppropriateFactory( $args[0] )->$method();
	}

}
