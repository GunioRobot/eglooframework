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
 *        http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *  
 * @author Keith Buel
 * @copyright 2008 eGloo, LLC
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
	
    private static $host        = "localhost";
    private static $user        = "webserver";
    private static $password    = "test";
    private static $dbname      = "eGlooFramework";
    // private static $port        = "5433"; // PGPOOL
    private static $port        = "5432"; // PostgreSQL Daemon
	
	public static function getConnection() {
		
		$connection_string = "host=" . self::$host . 
							" user=" . self::$user . 
							" password=" . self::$password . 
							" dbname=" . self::$dbname .
							" port=" . self::$port;
							  
		$db_handle = pg_connect( $connection_string );
		

		return $db_handle;
	}

}
?>
