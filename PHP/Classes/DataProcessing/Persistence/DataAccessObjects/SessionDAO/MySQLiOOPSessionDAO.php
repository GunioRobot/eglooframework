<?php
/**
 * MySQLiOOPSessionDAO Class File
 *
 * $file_block_description
 * 
 * Copyright 2011 eGloo, LLC
 *	
 * @author George Cooper
 * @copyright 2011 eGloo, LLC
 * @category DataProcessing
 * @package Persistence
 * @subpackage DataAccessObjects
 * @version 1.0
 */

/**
 * MySQLiOOPSessionDAO
 *
 * $short_description
 *
 * $long_description
 *
 * @category DataProcessing
 * @package Persistence
 * @subpackage DataAccessObjects
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

		$connection = DBConnectionManager::getConnection();
		// TODO Move this to an external SQL file at some point
		$preparedQueryTransaction = new QueryTransaction('REPLACE INTO dr_sessions (sid, hostname, session, timestamp) VALUES' . 
			' ("%s", "%s", "%s", ' . time() . ')');
		$preparedQueryTransaction->setQueryDialect($connection->getConnectionDialect());

		$sessionDataSerialized = base64_encode( $sessionDTO->getSessionData() );

		$queryParameters = array();
		$queryParameters[] = array( 'type' => 'string', 'value' => $sessionDTO->getSessionID() );
		$queryParameters[] = array( 'type' => 'string', 'value' => eGlooHTTPRequest::getRemoteAddress() );
		$queryParameters[] = array( 'type' => 'string', 'value' => $sessionDataSerialized );

		QueryPopulationManager::populateQueryTransaction($preparedQueryTransaction, $queryParameters);
		$queryExecutionRoutine = QueryExecutionRoutineManager::getQueryExecutionRoutine($preparedQueryTransaction);
		$responseTransaction = $queryExecutionRoutine->executeTransactionWithConnection($preparedQueryTransaction, $connection);
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

		$connection = DBConnectionManager::getConnection();

		// TODO Move this to an external SQL file at some point
		$preparedQueryTransaction = new QueryTransaction('SELECT * FROM dr_sessions WHERE sid = "%s"');
		$preparedQueryTransaction->setQueryDialect($connection->getConnectionDialect());

		$queryParameters = array(array('type' => 'string', 'value' => $sessionID));

		QueryPopulationManager::populateQueryTransaction($preparedQueryTransaction, $queryParameters);
		$queryExecutionRoutine = QueryExecutionRoutineManager::getQueryExecutionRoutine($preparedQueryTransaction);
		$responseTransaction = $queryExecutionRoutine->executeTransactionWithConnection($preparedQueryTransaction, $connection);

		$sessionData = $responseTransaction->getDataPackage();

		if( isset($sessionData) && !empty($sessionData) ) {
			$sessionDTO->setSessionID($sessionID);

			$unserializedSD = array();

			if (isset($sessionData[0]['session'])) {
				$sessionDTO->setSessionData( base64_decode($sessionData[0]['session']) );
			}

			if (isset($sessionData[0]['uid'])) {
				$sessionDTO->setUserID( $sessionData[0]['uid']);
			}

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
		 
		$connection = DBConnectionManager::getConnection();

		// TODO Move this to an external SQL file at some point
		$preparedQueryTransaction = new QueryTransaction('DELETE FROM dr_sessions WHERE sid="%s"');
		$preparedQueryTransaction->setQueryDialect($connection->getConnectionDialect());

		$queryParameters = array();

		QueryPopulationManager::populateQueryTransaction($preparedQueryTransaction, $queryParameters);
		$queryExecutionRoutine = QueryExecutionRoutineManager::getQueryExecutionRoutine($preparedQueryTransaction);
		$responseTransaction = $queryExecutionRoutine->executeTransactionWithConnection($preparedQueryTransaction, $connection);
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
		// $db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();
		// 
		// //Prepare a query for execution
		// $result = pg_prepare($db_handle, "query", 'SELECT deleteOldSessions($1)');
		// 
		// // Execute the prepared query.	Note that it is not necessary to escape
		// $result = pg_execute($db_handle, "query", array($sessionLifeTime . " minutes"));
		// 
		// pg_close( $db_handle );
	}
}