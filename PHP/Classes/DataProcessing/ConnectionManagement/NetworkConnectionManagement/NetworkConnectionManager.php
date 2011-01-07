<?php
/**
 * NetworkConnectionManager Class File
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
 * NetworkConnectionManager
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
final class NetworkConnectionManager extends ConnectionManager {

	// Database Connections
	// private static $doctrineConnection	= null;
	// private static $mysqlConnection		= null;
	// private static $pgConnection		= null;

	public static function getConnection( $connection_name = 'egPrimary', $engine_mode = null ) {
		$retVal = null;

		// $connection_info = eGlooConfiguration::getDatabaseConnectionInfo($connection_name);
		// const AQUINAS		= 0x00;
		// const CASSANDRA		= 0x01;
		// const DOCTRINE		= 0x02;
		// const EGLOO			= 0x03;
		// const MONGO			= 0x04;
		// const MYSQL			= 0x05;
		// const MYSQLI		= 0x06;
		// const MYSQLIOOP		= 0x07;
		// const ORACLE		= 0x08;
		// const PDO			= 0x09;
		// const POSTGRESQL	= 0x0a;
		// const REST			= 0x0b;
		// const SOAP			= 0x0c;

		// if ($engine_mode !== null) {
		// 	if ( $engine_mode === eGlooConfiguration::DOCTRINE ) {
		// 		$retVal = self::getDoctrineConnection();
		// 	} else if ( $engine_mode === eGlooConfiguration::MYSQL ) {
		// 		$retVal = self::getMySQLConnection();
		// 	} else if ( $engine_mode === eGlooConfiguration::POSTGRESQL ) {
		// 		$retVal = self::getPostgreSQLConnection();
		// 	} else {
		// 		// No DB engine specified in config or no engine available
		// 	}
		// } else {
		// 	if ( $connection_info['engine'] === eGlooConfiguration::DOCTRINE ) {
		// 		$retVal = self::getDoctrineConnection();
		// 	} else if ( $connection_info['engine'] === eGlooConfiguration::MYSQL ) {
		// 		$retVal = self::getMySQLConnection();
		// 	} else if ( $connection_info['engine'] === eGlooConfiguration::POSTGRESQL ) {
		// 		$retVal = self::getPostgreSQLConnection();
		// 	} else {
		// 		// No DB engine specified in config or no engine available
		// 	}
		// }

		return $retVal;
	}

	private static function getAquinasConnection( $connection_name = 'egPrimary' ) {
		
	}

	private static function getRESTConnection( $connection_name = 'egPrimary' ) {
		
	}

	private static function getSOAPConnection( $connection_name = 'egPrimary' ) {
		
	}

}

