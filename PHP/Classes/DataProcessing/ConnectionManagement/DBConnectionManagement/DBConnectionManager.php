<?php
/**
 * DBConnectionManager Class File
 *
 * Contains the class definition for the DBConnectionManager, a final class for
 * database connection management.
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
 * @author George Cooper
 * @copyright 2010 eGloo, LLC
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
final class DBConnectionManager extends ConnectionManager {

	// Database Connections
	private static $doctrineConnection	= null;
	private static $mysqlConnection		= null;
	private static $pgConnection		= null;

	public static function getConnection( $connection_name = 'egPrimary', $engine_mode = null ) {
		$retVal = null;

		$connection_info = eGlooConfiguration::getDatabaseConnectionInfo($connection_name);
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

	private static function getActiveRecordConnection( $connection_name = 'egPrimary' ) {
		
	}

	private static function getCassandraConnection( $connection_name = 'egPrimary' ) {
		
	}

	private static function getDoctrineConnection( $connection_name = 'egPrimary' ) {
		$connection_info = eGlooConfiguration::getDatabaseConnectionInfo($connection_name);

		$dsn = 'pgsql://' . $connection_info['user'] . ':' . $connection_info['password'] .
			'@' . $connection_info['host'] .':' . $connection_info['port'] . '/' . $connection_info['database'];

		$manager = Doctrine_Manager::getInstance();
		$conn = $manager->connection($dsn, $connection_info['name']);
		
		return $conn;
	}

	private static function geteGlooDBConnection( $connection_name = 'egPrimary' ) {
		
	}

	private static function getMongoDBConnection( $connection_name = 'egPrimary' ) {
		
	}

	private static function getMySQLConnection( $connection_name = 'egPrimary' ) {
		$connection_info = eGlooConfiguration::getDatabaseConnectionInfo($connection_name);

		$host 			= $connection_info['host'];
		$port 			= $connection_info['port'];
		$dbname 		= $connection_info['database'];
		$user 			= $connection_info['user'];
		$password	 	= $connection_info['password'];

		$mysql_conn = mysql_connect($host . ':' . $port, $user, $password, $dbname);

		if (!$mysql_conn) {
			$exception_message = 'DBConnectionManager: Cannot connect to MySQL server via getMySQLConnection.  Error: ' . mysql_error();

			throw new Exception($exception_message);
		} else {
			
		}

		mysql_select_db($dbname, $mysql_conn);

		return $mysql_conn;
	}

	private static function getMySQLiConnection( $connection_name = 'egPrimary' ) {
		$connection_info = eGlooConfiguration::getDatabaseConnectionInfo($connection_name);

		$host 			= $connection_info['host'];
		$port 			= $connection_info['port'];
		$dbname 		= $connection_info['database'];
		$user 			= $connection_info['user'];
		$password	 	= $connection_info['password'];

		$mysqli_conn = mysqli_connect($host . ':' . $port, $user, $password, $dbname);

		if (!$mysqli_conn) {
			$exception_message = 'DBConnectionManager: Cannot connect to MySQL server via getMySQLiConnection.  Error: '
				. mysqli_connect_error();

			throw new Exception($exception_message);
		}

		return $mysqli_conn;
	}

	private static function getMySQLiOOPConnection( $connection_name = 'egPrimary' ) {
		$connection_info = eGlooConfiguration::getDatabaseConnectionInfo($connection_name);

		$host 			= $connection_info['host'];
		$port 			= $connection_info['port'];
		$dbname 		= $connection_info['database'];
		$user 			= $connection_info['user'];
		$password	 	= $connection_info['password'];

		$mysqli = new mysqli($host . ':' . $port, $user, $password, $dbname);

		if (mysqli_connect_errno()) {
			$exception_message = 'DBConnectionManager: Cannot connect to MySQL server via getMySQLiOOPConnection.  Error: '
				. mysqli_connect_error();

			throw new Exception($exception_message);
		}

		return $mysqli;
	}

	private static function getOracleDBConnection( $connection_name = 'egPrimary' ) {
		
	}

	private static function getOutletConnection( $connection_name = 'egPrimary' ) {
		
	}

	private static function getPDOConnection( $connection_name = 'egPrimary' ) {
		
	}

	private static function getPostgreSQLConnection( $connection_name = 'egPrimary' ) {
		$connection_info = eGlooConfiguration::getDatabaseConnectionInfo($connection_name);

		$connection_string = 'host=' . $connection_info['host'] . 
							' user=' . $connection_info['user'] . 
							' password=' . $connection_info['password'] . 
							' dbname=' . $connection_info['database'] .
							' port=' . $connection_info['port'];

		$db_handle = pg_connect( $connection_string );
		
		if (!$db_handle) {
			$exception_message = 'DBConnectionManager: Cannot connect to PostgreSQL server via getPostgreSQLConnection.  Error: ' . pg_last_error($db_handle);

			throw new Exception($exception_message);
		}

		return $db_handle;
	}

	private static function getPropelConnection( $connection_name = 'egPrimary' ) {
		
	}

	private static function getRedBeanConnection( $connection_name = 'egPrimary' ) {
		
	}

}

