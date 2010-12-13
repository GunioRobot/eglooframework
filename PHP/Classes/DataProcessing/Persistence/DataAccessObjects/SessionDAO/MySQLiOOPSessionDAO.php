<?php
/**
 * MySQLiOOPSessionDAO Class File
 *
 * $file_block_description
 * 
 * Copyright 2010 PetFlow, LLC
 *  
 * @author George Cooper
 * @copyright 2010 PetFlow, LLC
 * @package $package
 * @subpackage $subpackage
 * @version 1.0
 */

/**
 * MySQLiOOPSessionDAO
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class MySQLiOOPSessionDAO extends SessionDAO {


   /**
    * A function to write current session data to the 
    * postgre database
    * 
    * @param SessionDTO
    * @return boolean 
    */
	public function setSessionData($sessionDTO){
		/**
		 * TODO write session data from session dto
		 * to database
		 * 
		 * If sessionDTO does not have a sessionID create new one in the database
		 * If sessionDTO does have a sessionID then update the session existing in the database.
		 */

		eGlooLogger::writeLog( eGlooLogger::DEBUG, "MySQLiOOPSessionDAO::setSessionData: sessionid = " . $sessionDTO->getSessionID() . $sessionDTO->getUserAgent() . $sessionDTO->getUserID() . $sessionDTO->getSessionData(), eGlooLogger::SESSION );
		
       $db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();

 		//Prepare a query for execution
 		$result = pg_prepare($db_handle, "query", 'SELECT setSession($1, $2, $3, $4)');

		// Execute the prepared query.  Note that it is not necessary to escape
		$result = pg_execute($db_handle, "query", array($sessionDTO->getSessionID(), $sessionDTO->getUserID(), $sessionDTO->getUserAgent(), base64_encode(serialize($sessionDTO->getSessionData()))));

		pg_close( $db_handle );

	}
   
   /**
    * Read Session Data from the postgre database
    * 
    * @param sessionID 
    * @return sessionDTO 
    */
	public function getSessionData($sessionID){
		$sessionDTO = new SessionDTO();
		/**
		 * TODO fill session dto with session data from databsae
		 */

		 eGlooLogger::writeLog( eGlooLogger::DEBUG, "MySQLiOOPSessionDAO::getSessionData: sessionid = " . $sessionID, eGlooLogger::SESSION );

       $db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();

 		//Prepare a query for execution
 		$result = pg_prepare($db_handle, "query", 'SELECT output_session_id, output_user_id, output_useragent, output_sessiondata, output_SessionExists FROM getSession($1)');

		// Execute the prepared query.  Note that it is not necessary to escape
		$result = pg_execute($db_handle, "query", array($sessionID));
		$testarray = pg_fetch_row($result);
		
		//Closes a motherfucking connection!
		pg_close( $db_handle );

		$sessionDTO->setSessionID($testarray['0']);
		$sessionDTO->setUserID($testarray['1']);
		$sessionDTO->setUserAgent($testarray['2']);
		$sessionDTO->setSessionData(unserialize(base64_decode($testarray['3'])));
		
		if($testarray['4'] === 't'){
			$sessionDTO->setSessionExists(true);
		} else {
			$sessionDTO->setSessionExists(false);
		}
		
		return $sessionDTO;
	}


   /**
    * Delete session from database
    * 
    * @param sessionID
    */
	public function deleteSession( $sessionID ){

       eGlooLogger::writeLog( eGlooLogger::DEBUG, "MySQLiOOPSessionDAO::deleteSession: " . $sessionID, eGlooLogger::SESSION );
		 
		$db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();

 		//Prepare a query for execution
 		$result = pg_prepare($db_handle, "query", 'SELECT deleteSession($1)');

		// Execute the prepared query.  Note that it is not necessary to escape
		$result = pg_execute($db_handle, "query", array($sessionID));
		
		//Closes a motherfucking connection!
		pg_close( $db_handle );
	}



    /**
    * Delete sessions that haven't been accessed in the
    * specified amount of time.
    * 
    * @param sessionID
    */
	public function deleteOldSessions( $sessionLifeTime ){
		
       eGlooLogger::writeLog( eGlooLogger::DEBUG, "MySQLiOOPSessionDAO::deleteOldSessions: " . $sessionLifeTime, eGlooLogger::SESSION );
		/**
		 * TODO delete sessions that are older than the specified
		 * amount of time.
		 */	
		$db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();

 		//Prepare a query for execution
 		$result = pg_prepare($db_handle, "query", 'SELECT deleteOldSessions($1)');

		// Execute the prepared query.  Note that it is not necessary to escape
		$result = pg_execute($db_handle, "query", array($sessionLifeTime . " minutes"));

		//Closes a motherfucking connection!
		pg_close( $db_handle );
	}
}