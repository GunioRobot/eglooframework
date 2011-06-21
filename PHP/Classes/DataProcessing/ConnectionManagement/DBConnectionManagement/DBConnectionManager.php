<?php
/**
 * DBConnectionManager Class File
 *
 * Contains the class definition for the DBConnectionManager, a final class for
 * database connection management.
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
 * @author George Cooper
 * @copyright 2011 eGloo, LLC
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

	private static $connections = array('egCustomConnections' => array());

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
				if (!isset(self::$connections['Doctrine'])){
					self::$connections['Doctrine'] = array();
				}

				$retVal = self::getDoctrineConnection();
			} else if ( $engine_mode === eGlooConfiguration::MYSQL ) {
				if (!isset(self::$connections['MySQL'])){
					self::$connections['MySQL'] = array();
				}

				$retVal = self::getMySQLConnection();
			} else if ( $engine_mode === eGlooConfiguration::MYSQLI ) {
				if (!isset(self::$connections['MySQLi'])){
					self::$connections['MySQLi'] = array();
				}

				$retVal = self::getMySQLiConnection();
			} else if ( $engine_mode === eGlooConfiguration::MYSQLIOOP ) {
				if (!isset(self::$connections['MySQLiOOP'])){
					self::$connections['MySQLiOOP'] = array();
				}

				$retVal = self::getMySQLiOOPConnection();
			} else if ( $engine_mode === eGlooConfiguration::POSTGRESQL ) {
				if (!isset(self::$connections['PostgreSQL'])){
					self::$connections['PostgreSQL'] = array();
				}

				$retVal = self::getPostgreSQLConnection();
			} else {
				// No DB engine specified in config or no engine available
			}
		} else {
			if ( $connection_info['engine'] === eGlooConfiguration::DOCTRINE ) {
				if (!isset(self::$connections['Doctrine'])){
					self::$connections['Doctrine'] = array();
				}

				$retVal = self::getDoctrineConnection();
			} else if ( $connection_info['engine'] === eGlooConfiguration::MYSQL ) {
				if (!isset(self::$connections['MySQL'])){
					self::$connections['MySQL'] = array();
				}

				$retVal = self::getMySQLConnection();
			} else if ( $connection_info['engine'] === eGlooConfiguration::MYSQLI ) {
				if (!isset(self::$connections['MySQLi'])){
					self::$connections['MySQLi'] = array();
				}

				$retVal = self::getMySQLiConnection();
			} else if ( $connection_info['engine'] === eGlooConfiguration::MYSQLIOOP ) {
				if (!isset(self::$connections['MySQLiOOP'])){
					self::$connections['MySQLiOOP'] = array();
				}

				$retVal = self::getMySQLiOOPConnection();
			} else if ( $connection_info['engine'] === eGlooConfiguration::POSTGRESQL ) {
				if (!isset(self::$connections['PostgreSQL'])){
					self::$connections['PostgreSQL'] = array();
				}

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

	private static function getCustomConnectionWithOptions( $connection_name, $engine_mode, $connection_options = null ) {
		$retVal = null;

		if ($engine_mode !== null) {
			if ( $engine_mode === eGlooConfiguration::DOCTRINE ) {
				if (!isset(self::$connections['egCustomConnections']['Doctrine'])){
					self::$connections['egCustomConnections']['Doctrine'] = array();
				}

				$retVal = self::getCustomDoctrineConnection( $connection_name, $connection_options );
			} else if ( $engine_mode === eGlooConfiguration::MYSQL ) {
				if (!isset(self::$connections['egCustomConnections']['MySQL'])){
					self::$connections['egCustomConnections']['MySQL'] = array();
				}

				$retVal = self::getCustomMySQLConnection( $connection_name, $connection_options );
			} else if ( $engine_mode === eGlooConfiguration::MYSQLI ) {
				if (!isset(self::$connections['egCustomConnections']['MySQLi'])){
					self::$connections['egCustomConnections']['MySQLi'] = array();
				}

				$retVal = self::getCustomMySQLiConnection( $connection_name, $connection_options );
			} else if ( $engine_mode === eGlooConfiguration::MYSQLIOOP ) {
				if (!isset(self::$connections['egCustomConnections']['MySQLiOOP'])){
					self::$connections['egCustomConnections']['MySQLiOOP'] = array();
				}

				$retVal = self::getCustomMySQLiOOPConnection( $connection_name, $connection_options );
			} else if ( $engine_mode === eGlooConfiguration::POSTGRESQL ) {
				if (!isset(self::$connections['egCustomConnections']['PostgreSQL'])){
					self::$connections['egCustomConnections']['PostgreSQL'] = array();
				}

				$retVal = self::getCustomPostgreSQLConnection( $connection_name, $connection_options );
			} else {
				// No DB engine specified in config or no engine available
			}
		}

		return $retVal;
	}

	private static function getCustomMySQLiConnectionWithOptions( $connection_name, $connection_options = null ) {
		$retVal = null;

		if (isset(self::$connections['egCustomConnections']['MySQLi'][$connection_name])) {
			$retVal = self::$connections['egCustomConnections']['MySQLi'][$connection_name];
		} else {
			$host 			= $connection_options['host'];
			$port 			= $connection_options['port'];
			$dbname 		= $connection_options['database'];
			$user 			= $connection_options['user'];
			$password	 	= $connection_options['password'];

			$mysqli_conn = mysqli_connect($host, $user, $password, $dbname, $port);

			if (!$mysqli_conn) {
				$exception_message = 'DBConnectionManager: Cannot connect to MySQL server via getMySQLiConnection.  Error: '
					. mysqli_connect_error();

				throw new Exception($exception_message);
			}

			self::$connections['egCustomConnections']['MySQLi'][$connection_name] = new MySQLiDBConnection( $mysqli_conn );
			$retVal = self::$connections['egCustomConnections']['MySQLi'][$connection_name];
		}

		return $retVal;
	}

	private static function getDoctrineConnection( $connection_name = 'egPrimary' ) {
		$retVal = null;

		if (isset(self::$connections['Doctrine'][$connection_name])) {
			$retVal = self::$connections['Doctrine'][$connection_name];
		} else {
			$connection_info = eGlooConfiguration::getDatabaseConnectionInfo($connection_name);

			$dsn = 'pgsql://' . $connection_info['user'] . ':' . $connection_info['password'] .
				'@' . $connection_info['host'] .':' . $connection_info['port'] . '/' . $connection_info['database'];

			$manager = Doctrine_Manager::getInstance();
			$conn = $manager->connection($dsn, $connection_info['name']);
			
			self::$connections['Doctrine'][$connection_name] = new DoctrineDBConnection($conn);

			$retVal = self::$connections['Doctrine'][$connection_name];
		}

		return $conn;
	}

	private static function geteGlooDBConnection( $connection_name = 'egPrimary' ) {
		
	}

	private static function getMongoDBConnection( $connection_name = 'egPrimary' ) {
		
	}

	private static function getMySQLConnection( $connection_name = 'egPrimary' ) {
		$retVal = null;

		if (isset(self::$connections['MySQL'][$connection_name])) {
			$retVal = self::$connections['MySQL'][$connection_name];
		} else {
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

			self::$connections['MySQL'][$connection_name] = new MySQLDBConnection( $mysql_conn );
			$retVal = self::$connections['MySQL'][$connection_name];
		}

		return $retVal;
	}

	private static function getMySQLiConnection( $connection_name = 'egPrimary' ) {
		$retVal = null;

		if (isset(self::$connections['MySQLi'][$connection_name])) {
			$retVal = self::$connections['MySQLi'][$connection_name];
		} else {
			$connection_info = eGlooConfiguration::getDatabaseConnectionInfo($connection_name);

			$host 			= $connection_info['host'];
			$port 			= $connection_info['port'];
			$dbname 		= $connection_info['database'];
			$user 			= $connection_info['user'];
			$password	 	= $connection_info['password'];

			$mysqli_conn = mysqli_connect($host, $user, $password, $dbname, $port);

			if (!$mysqli_conn) {
				$exception_message = 'DBConnectionManager: Cannot connect to MySQL server via getMySQLiConnection.  Error: '
					. mysqli_connect_error();

				throw new Exception($exception_message);
			}

			self::$connections['MySQLi'][$connection_name] = new MySQLiDBConnection( $mysqli_conn );
			$retVal = self::$connections['MySQLi'][$connection_name];
		}

		return $retVal;
	}

	private static function getMySQLiOOPConnection( $connection_name = 'egPrimary' ) {
		$retVal = null;

		if (isset(self::$connections['MySQLiOOP'][$connection_name])) {
			$retVal = self::$connections['MySQLiOOP'][$connection_name];
		} else {
			$connection_info = eGlooConfiguration::getDatabaseConnectionInfo($connection_name);

			$host 			= $connection_info['host'];
			$port 			= $connection_info['port'];
			$dbname 		= $connection_info['database'];
			$user 			= $connection_info['user'];
			$password	 	= $connection_info['password'];

			$mysqli = new mysqli($host, $user, $password, $dbname, $port);

			if (mysqli_connect_errno()) {
				$exception_message = 'DBConnectionManager: Cannot connect to MySQL server via getMySQLiOOPConnection.  Error: '
					. mysqli_connect_error();

				throw new Exception($exception_message);
			}

			// This might not be what you want, but if it isn't, that's your problem.  Welcome to 2011.  Learn to UTF-8, kids
			if ( !$mysqli->set_charset('utf8') ) {
				$exception_message = 'DBConnectionManager: Error loading character set UTF-8.  MySQLiOOP Error: ' . $mysqli->error;
				throw new Exception($exception_message);
			}

			self::$connections['MySQLiOOP'][$connection_name] = new MySQLiOOPDBConnection( $mysqli );
			$retVal = self::$connections['MySQLiOOP'][$connection_name];
		}

		return $retVal;
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

		// return $db_handle;
		$retVal = new PostgreSQLDBConnection( $db_handle );

		return $retVal;
	}

	private static function getPropelConnection( $connection_name = 'egPrimary' ) {
		
	}

	private static function getRedBeanConnection( $connection_name = 'egPrimary' ) {
		
	}

}

