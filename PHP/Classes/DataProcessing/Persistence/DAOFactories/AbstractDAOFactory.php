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
 * @package Persistence
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
 * @package Persistence
 */
class AbstractDAOFactory {

	// Protected Data Members
	protected $_connection_name = null;

	//singleton holder
	private static $singleton;


	public function __construct( $connection_name ) {
		$this->_connection_name = $connection_name;
	}

	/**
	 * Singleton access to this AbstractDAOFactory
	 * 
	 * @return AbstractDAOFactory the singleton reference of the AbstractDAOFactory
	 */
	public static function getInstance() {
		if ( !isset ( self::$singleton ) ) {
			self::$singleton = new AbstractDAOFactory( null );
		}

		return self::$singleton;
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

	public function getAccountDAO( $connection_name = 'egPrimary' ) {
		return $this->getAppropriateFactory( $connection_name )->getAccountDAO();
	}

	public function getUserProfileDAO( $connection_name = 'egPrimary' ) {
		return $this->getAppropriateFactory( $connection_name )->getUserProfileDAO();
	}

	public function getUserProfilePageDAO( $connection_name = 'egPrimary' ) {
		return $this->getAppropriateFactory( $connection_name )->getUserProfilePageDAO();
	}

	public function getFriendsDAO( $connection_name = 'egPrimary' ) {
		return $this->getAppropriateFactory( $connection_name )->getFriendsDAO();
	}

	public function getBlogDAO( $connection_name = 'egPrimary' ) {
		return $this->getAppropriateFactory( $connection_name )->getBlogDAO();
	}

	public function getCubeDAO( $connection_name = 'egPrimary' ) {
		return $this->getAppropriateFactory( $connection_name )->getCubeDAO();
	}

	public function getUserInvitesDAO( $connection_name = 'egPrimary' ) {
		return $this->getAppropriateFactory( $connection_name )->getUserInvitesDAO();
	}

	public function getSearchDAO( $connection_name = 'egPrimary' ) {
		return $this->getAppropriateFactory( $connection_name )->getSearchDAO();
	}

	public function getImageDAO( $connection_name = 'egPrimary' ) {
		return $this->getAppropriateFactory( $connection_name )->getImageDAO();
	}

	public function getRelationshipDAO( $connection_name = 'egPrimary' ) {
		return $this->getAppropriateFactory( $connection_name )->getRelationshipDAO();
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
