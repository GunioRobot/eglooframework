<?php
/**
 * DAOFactory Class File
 *
 * Needs to be commented
 * 
 * Copyright 2010 eGloo, LLC
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
 * @copyright 2010 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package Persistence
 * @version 1.0
 */

/**
 * DAOFactory
 * 
 * This class defines an abstract DAOFactory.  It determines the appropriate
 * concrete DAOFactory.	 And then returns the correct requested DAO by calling
 * the appropriate functions on the concrete DAOFactory.
 * 
 * Details of this pattern can be seen on the following website:
 * http://java.sun.com/blueprints/corej2eepatterns/Patterns/DataAccessObject.html
 * 
 * @package Persistence
 */
class DAOFactory {

	//singleton holder
	private static $singleton;

	/**
	 * TODO: set this from a properties file telling us where to connect
	 */
	// private static $DAO_TYPE = "PostgreSQLDAOFactory";

	/**
	 * Singleton access to this DAOFactory
	 * 
	 * @return DAOFactory the singleton reference of the DAOFactory
	 */
	public static function getInstance() {
		if ( !isset ( self::$singleton ) ) {
			self::$singleton = new DAOFactory();
		}

		return self::$singleton;
	}

	/**
	 * This class returns the appropriate DAO factory as specified by
	 * an external property
	 * 
	 * @return DAOFactory a concrete DAO factory
	 */
	private function getAppropriateFactory( $connection_name = 'egPrimary' ) {
		$retVal = null;

		$connection_info = eGlooConfiguration::getDatabaseConnectionInfo($connection_name);

		if ( $connection_info['engine'] === eGlooConfiguration::POSTGRESQL ) {
			$retVal = new PostgreSQLDAOFactory();
		} else if ( $connection_info['engine'] === eGlooConfiguration::MYSQLIOOP ) {
			$retVal = new PostgreSQLDAOFactory();
		} else if ( $connection_info['engine'] === eGlooConfiguration::MYSQLI ) {
			$retVal = new PostgreSQLDAOFactory();
		} else if ( $connection_info['engine'] === eGlooConfiguration::MYSQL ) {
			$retVal = new PostgreSQLDAOFactory();
		} else if ( $connection_info['engine'] === eGlooConfiguration::DOCTRINE ) {
			$retVal = new PostgreSQLDAOFactory();
		} else {
			// No connection specified and no default given...
		}

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

	public function getGlobalMenuBarDAO( $connection_name = 'egPrimary' ) {
		return $this->getAppropriateFactory( $connection_name )->getGlobalMenuBarDAO();
	}

	public function getInformationBoardIcingDAO( $connection_name = 'egPrimary' ) {
		return $this->getAppropriateFactory( $connection_name )->getInformationBoardIcingDAO();
	}

	public function getInformationBoardMusicDAO( $connection_name = 'egPrimary' ) {
		return $this->getAppropriateFactory( $connection_name )->getInformationBoardMusicDAO();
	}

	public function getInformationBoardPeopleDAO( $connection_name = 'egPrimary' ) {
		return $this->getAppropriateFactory( $connection_name )->getInformationBoardPeopleDAO();
	}

	public function getInformationBoardPicturesDAO( $connection_name = 'egPrimary' ) {
		return $this->getAppropriateFactory( $connection_name )->getInformationBoardPicturesDAO();
	}

	public function getInformationBoardVideoDAO( $connection_name = 'egPrimary' ) {
		return $this->getAppropriateFactory( $connection_name )->getInformationBoardVideoDAO();
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
	
	public function getFridgeDAO( $connection_name = 'egPrimary' ) {
		return $this->getAppropriateFactory( $connection_name )->getFridgeDAO();
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
	
	public function getAuctionDAO( $connection_name = 'egPrimary' ) {
		return $this->getAppropriateFactory( $connection_name )->getAuctionDAO();
	}
	
	public function getGenericPLFunctionDAO( $connection_name = 'egPrimary' ) {
		return $this->getAppropriateFactory( $connection_name )->getGenericPLFunctionDAO();
	}

}
