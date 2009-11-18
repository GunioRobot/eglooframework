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

	/* Connection Information */

	// Doctrine Settings
	// private static $doctrine_host		= 'localhost';
	// private static $doctrine_user		= 'webserver';
	// private static $doctrine_password	= 'test';
	// private static $doctrine_dbname		= 'eGlooFramework';
	// // private static $doctrine_port		   = '5433'; // PGPOOL
	// private static $doctrine_port		= '5432'; // PostgreSQL Daemon

	// MySQL Settings
	private static $mysql_host		= 'localhost';
	private static $mysql_user		= 'webserver';
	private static $mysql_password	= 'test';
	private static $mysql_dbname		= 'eGlooFramework';
	private static $mysql_port		= '';

	// PostgreSQL Settings
	private static $pg_host		= 'localhost';
	private static $pg_user		= 'webserver';
	private static $pg_password	= 'test';
	private static $pg_database		= 'eGlooFramework';
	// private static $pg_port		   = '5433'; // PGPOOL
	private static $pg_port		= '5432'; // PostgreSQL Daemon

	public static function getConnection() {
		$retVal = null;

		if ( eGlooConfiguration::getDatabaseEngine() === eGlooConfiguration::DOCTRINE ) {
			$retVal = self::getDoctrineConnection();
		} else if ( eGlooConfiguration::getDatabaseEngine() === eGlooConfiguration::MYSQL ) {
			$retVal = self::getMySQLConnection();
		} else if ( eGlooConfiguration::getDatabaseEngine() === eGlooConfiguration::POSTGRESQL ) {
			$retVal = self::getPostgreSQLConnection();
		} else {
			// No DB engine specified in config or no engine available
		}

		return $retVal;
	}

	private static function getDoctrineConnection() {
		$dsn = 'pgsql://' . self::$pg_user . ':' . self::$pg_password . '@' . self::$pg_host . '/' . self::$pg_database;

		$manager = Doctrine_Manager::getInstance();
		$conn = $manager->connection($dsn, 'eGlooFramework');

		// At this point no actual connection to the database is created
		// $conn = Doctrine_Manager::connection('pgsql://webserver:test@localhost/eGlooFramework');
		// die;
		// The first time the connection is needed, it is instantiated
		// This query triggers the connection to be created
		// $stmt = $conn->prepare('SELECT * FROM us_stateprovinces limit 1');
		// $stmt->execute();
		// $results = $stmt->fetchAll();
		// die_r($results);
		// die;

		// $conn->setOption('username', $user);
		// $conn->setOption('password', $password);
	}

	private static function getPostgreSQLConnection() {
		$connection_string = 'host=' . self::$pg_host . 
							' user=' . self::$pg_user . 
							' password=' . self::$pg_password . 
							' dbname=' . self::$pg_database .
							' port=' . self::$pg_port;
							  
		$db_handle = pg_connect( $connection_string );

		return $db_handle;
	}

	private static function getMySQLConnection() {
		
	}


}
?>
