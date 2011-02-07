<?php
/**
 * ContentDAOFactory Class File
 *
 * Contains the class definition for the ContentDAOFactory
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
 * ContentDAOFactory
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class ContentDAOFactory extends AbstractDAOFactory {

	//singleton holder
	protected static $singleton;

	/**
	 * This class returns the appropriate DAO factory as specified by
	 * an external property
	 * 
	 * @return ConcreteContentDAOFactory a concrete DAO factory
	 */
	private function getAppropriateFactory( $connection_name = 'egDataStore' ) {
		$retVal = null;

		if ( $connection_name === 'egDataStore' ) {
			$retVal = new eGlooDataStoreContentDAOFactory( $connection_name );
		} else if ( $connection_name === 'Akamai' ) {
			$retVal = new AkamaiContentDAOFactory( $connection_name );
		} else if ( $connection_name === 'CloudFront' ) {
			$retVal = new CloudFrontContentDAOFactory( $connection_name );
		} else if ( $connection_name === 'egCDNPrimary' ) {
			// If we didn't match a proper request for a non-DB ContentDAOFactory, we assume DB ContentDAOFactory
			$connection_info = eGlooConfiguration::getCDNConnectionInfo($connection_name);

			if ( $connection_info['provider'] === eGlooConfiguration::AKAMAI ) {
				$retVal = new AkamaiContentDAOFactory( $connection_name );
			} else if ( $connection_info['provider'] === eGlooConfiguration::CLOUDFRONT ) {
				$retVal = new CloudFrontContentDAOFactory( $connection_name );
			} else {
				// No connection specified and no default given...
			}
		} else {
			// If we didn't match a proper request for a non-DB ContentDAOFactory, we assume DB ContentDAOFactory
			$connection_info = eGlooConfiguration::getDatabaseConnectionInfo($connection_name);

			if ( $connection_info['engine'] === eGlooConfiguration::POSTGRESQL ) {
				$retVal = new PostgreSQLContentDAOFactory( $connection_name );
			} else if ( $connection_info['engine'] === eGlooConfiguration::MYSQLIOOP ) {
				$retVal = new MySQLiOOPContentDAOFactory( $connection_name );
			} else if ( $connection_info['engine'] === eGlooConfiguration::MYSQLI ) {
				$retVal = new MySQLiContentDAOFactory( $connection_name );
			} else if ( $connection_info['engine'] === eGlooConfiguration::MYSQL ) {
				$retVal = new MySQLContentDAOFactory( $connection_name );
			} else if ( $connection_info['engine'] === eGlooConfiguration::DOCTRINE ) {
				$retVal = new DoctrineContentDAOFactory( $connection_name );
			} else {
				// No connection specified and no default given...
			}
		}

		return $retVal;
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

	public function getImageContentDAO( $connection_name = 'egDataStore' ) {
		return $this->getAppropriateFactory( $connection_name )->getImageContentDAO();
	}

	public function getGenericFileContentDAO( $connection_name = 'egDataStore' ) {
		return $this->getAppropriateFactory( $connection_name )->getGenericFileContentDAO();
	}

}

