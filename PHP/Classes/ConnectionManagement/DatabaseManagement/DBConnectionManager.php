<?php
/**
 * DBConnectionManager Class File
 *
 * Contains the class definition for the DBConnectionManager, a final class for
 * database connection management.
 * 
 * Copyright 2008 eGloo, LLC
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
 * @author George Cooper
 * @copyright 2009 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package Database
 * @version 1.0
 */

/**
 * DBConnectionManager
 * 
 * This class gives out connections to the database.
 * 
 * This class can now interface with PGPOOL (postgresql pool) a connection
 * pool daemon, but simply specifying the port of the PGPOOL daemon.
 * This obviously needs to be different than 5432 (postgresql port) 
 * 
 * @package Database
 * @subpackage Management
 */
final class DBConnectionManager {

	// Database Connections
	private static $doctrineConnection	= null;
	private static $mysqlConnection		= null;
	private static $pgConnection		= null;

	public static function getConnection( $connection_name = 'egPrimary', $engine_mode = null ) {
		$retVal = null;

		$connection_info = eGlooConfiguration::getDatabaseConnectionInfo($connection_name);

		if ($engine_mode !== null) {
			if ( $engine_mode === eGlooConfiguration::DOCTRINE ) {
				$retVal = self::getDoctrineConnection();
			} else if ( $engine_mode === eGlooConfiguration::MYSQL ) {
				$retVal = self::getMySQLConnection();
			} else if ( $engine_mode === eGlooConfiguration::POSTGRESQL ) {
				$retVal = self::getPostgreSQLConnection();
			} else {
				// No DB engine specified in config or no engine available
			}
		} else {
			if ( $connection_info['engine'] === eGlooConfiguration::DOCTRINE ) {
				$retVal = self::getDoctrineConnection();
			} else if ( $connection_info['engine'] === eGlooConfiguration::MYSQL ) {
				$retVal = self::getMySQLConnection();
			} else if ( $connection_info['engine'] === eGlooConfiguration::POSTGRESQL ) {
				$retVal = self::getPostgreSQLConnection();
			} else {
				// No DB engine specified in config or no engine available
			}
		}

		return $retVal;
	}

	private static function getDoctrineConnection( $connection_name = 'egPrimary' ) {
		$connection_info = eGlooConfiguration::getDatabaseConnectionInfo($connection_name);

		$dsn = 'pgsql://' . $connection_info['user'] . ':' . $connection_info['password'] .
			'@' . $connection_info['host'] .':' . $connection_info['port'] . '/' . $connection_info['database'];

		$manager = Doctrine_Manager::getInstance();
		$conn = $manager->connection($dsn, $connection_info['name']);
		
		return $conn;
	}

	private static function getPostgreSQLConnection( $connection_name = 'egPrimary' ) {
		$connection_info = eGlooConfiguration::getDatabaseConnectionInfo($connection_name);

		$connection_string = 'host=' . $connection_info['host'] . 
							' user=' . $connection_info['user'] . 
							' password=' . $connection_info['password'] . 
							' dbname=' . $connection_info['database'] .
							' port=' . $connection_info['port'];

		$db_handle = pg_connect( $connection_string );

		return $db_handle;
	}

	private static function getMySQLConnection() {
		
	}


}
?>
