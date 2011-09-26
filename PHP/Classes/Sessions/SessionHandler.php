<?php
/**
 * SessionHandler Class File
 *
 * Contains the class definition for the session handler.
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
 * @author Keith Buel
 * @copyright 2011 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package Sessions
 * @version 1.0
 */

/**
 * SessionHandler
 * 
 * This class contains session management callback functions.
 * 
 * These functions will be called by PHP at different points during the life
 * cycle of a session.  This class must be registered with php before the session
 * start function is called.  An instance of this object is intended to be created
 * at the start of a request.  This probably should be one of the first things
 * that is done in index.php.
 * 
 * A SessionDAO should be used for interaction with persisting to a database.
 * 
 * DO NOT ECHO ANYTHING IN THIS CLASS.  ECHOING INFORMATION HERE WILL VOID PHP'S
 * ABILITY TO CREATE A SESSION COOKIE
 * 
 * 
 * This current version is simply stubbed out to prove that this model will work. 
 *
 * @package Sessions
 */
class SessionHandler {

	//Session life time in minutes
	private static $SESSION_LIFETIME = 15;

	/**
	 * SessionHandler Constructor
	 * 
	 * passing it references to this objects' methods.
	 * on creation of this object, the Session set save handler function is called
	 * 
	 */
	function SessionHandler() {

		session_set_save_handler(
			array ($this, 'open'), 
			array ($this, 'close'), 
			array ($this, 'read'), 
			array ($this, 'write'), 
			array ($this, 'destroy'), 
			array ($this, 'gc'));
	}

	/**
	 * Function that is not needed but required by session_set_save_handler
	 */
	function open($path, $name) {
        eGlooLogger::writeLog( eGlooLogger::DEBUG, "SessionHandler::OPEN -- Path: $path, Name: $name", eGlooLogger::SESSION );
		return true;
	}

	/**
	 * Function that to perform basic garbage collection
	 * @return boolean
	 */
	function close() {
		eGlooLogger::writeLog( eGlooLogger::DEBUG, "SessionHandler::CLOSE", eGlooLogger::SESSION );
		$this->gc();
		return true;
	}

	/**
	 * Session verification should be done here. This session id is then used
	 * to go to the database to access persisted session state.  The session 
	 * information is then returned in the form of a serialized array.  
	 * This method MUST return a string.... or bad things will happen.
	 * 
	 * @param string $sessionID session_id
	 * @return string serialized array of session data
	 */
	function read($sessionID) {
		eGlooLogger::writeLog( eGlooLogger::DEBUG, "SessionHandler::READ -- sessionid = $sessionID", eGlooLogger::SESSION );
		
		//TODO check cache
		
		$daoFactory = AbstractDAOFactory::getInstance();
		$sessionDAO = $daoFactory->getSessionDAO();
		$sessionDTO = $sessionDAO->getSessionData($sessionID);

		//eGlooLogger::writeLog( eGlooLogger::DEBUG, eGlooHTTPRequest::getUserAgent() );
		eGlooLogger::writeLog( eGlooLogger::DEBUG, "SessionHandler::READ: User ID returned from database is: ". $sessionDTO->getUserID(), eGlooLogger::SESSION  );
		eGlooLogger::writeLog( eGlooLogger::DEBUG, "SessionHandler::READ: Session exists in database?: ". $sessionDTO->sessionExists(), eGlooLogger::SESSION  );
		

		$retval = '';

		/**
		 * if the session is not set
		 */
		if( ! $sessionDTO->sessionExists() ){
			eGlooLogger::writeLog( eGlooLogger::DEBUG, "NEW SESSION", eGlooLogger::SESSION  );
			
		} else {

			/**
			 * TODO: do session verification before commencing and returning session data
			 */
			if( $sessionDTO->getUserAgent() !== null ) {
	
	    		if( $sessionDTO->getUserAgent() != eGlooHTTPRequest::getUserAgent() ) {
	        		
	        		/**
	        		 * TODO throw user agent doesn't match exception
	        		 */
					eGlooLogger::writeLog( eGlooLogger::ALERT, "USER AGENT DOESN'T MATCH... FAIL NOW", eGlooLogger::SESSION  );
					return '';
	    		}
	    		
			} else {

	        		/**
	        		 * TODO throw useragent not set exception. 
	        		 * If this is happening, something is very wrong, either
	        		 * from the DAO side or from the write side.
	        		 */
					eGlooLogger::writeLog( eGlooLogger::ALERT, "USER AGENT NOT SET IN SESSIONDTO", eGlooLogger::SESSION  );
			} //end session verification



			eGlooLogger::writeLog( eGlooLogger::DEBUG, "SessionHandler::READ DATA: " . $sessionDTO->getSessionData(), eGlooLogger::SESSION  );
			$sessionData = $sessionDTO->getSessionData();
			if( $sessionData !== null ){
				$retval = $sessionData;
			}
			
		}
		
		return $retval;
			
	}

	/**
	 * This function is called at the end of a request to persist session data.
	 * This function will take the sessionData and write it to the database.
	 * 
	 * @param string $sessionID
	 * @param string $sessionData
	 * @return boolean
	 */
	function write($sessionID, $sessionData) {
		eGlooLogger::writeLog( eGlooLogger::DEBUG, "SessionHandler::write -- sessionid = $sessionID", eGlooLogger::SESSION );

		$sessionDTO = new SessionDTO();
		$sessionDTO->setSessionID( $sessionID );
		$sessionDTO->setSessionData( $sessionData );
		$sessionDTO->setUserAgent( eGlooHTTPRequest::getUserAgent() );
		
		if( isset( $_SESSION['USER_ID'] ) ){
			$sessionDTO->setUserID( $_SESSION['USER_ID'] );
		}

		$daoFactory = AbstractDAOFactory::getInstance();
		$sessionDAO = $daoFactory->getSessionDAO();
		$sessionDAO->setSessionData( $sessionDTO );

		return true;
	}

	/**
	 * This function is called if it is determined that this session needs 
	 * to be destroyed.  This method will destroy persisted data associated 
	 * with this session id.
	 * 
	 * @param string $sessionID
	 * @return boolean
	 */
	function destroy($sessionID) {
		eGlooLogger::writeLog( eGlooLogger::DEBUG, "SessionHandler::DESTROY sessionid = $sessionID", eGlooLogger::SESSION );
		$daoFactory = AbstractDAOFactory::getInstance();
		$sessionDAO = $daoFactory->getSessionDAO();
		$sessionDAO->deleteSession( $sessionID );
		return true;
	}

	/**
	 * clean up old sesions
	 * @return boolean
	 */
	function gc() {
		eGlooLogger::writeLog( eGlooLogger::DEBUG, "SessionHandler::GC maxlife", eGlooLogger::SESSION );

		srand( (double) microtime() * 1000000 );
		$randPercent = rand() % 100;
		
		if( $randPercent < 10 ){
			eGlooLogger::writeLog( eGlooLogger::DEBUG, "SessionHandler::RUNNING GC CLEAN UP, PERCENT: " . $randPercent, eGlooLogger::SESSION  );
			$daoFactory = AbstractDAOFactory::getInstance();
			$sessionDAO = $daoFactory->getSessionDAO();
			$sessionDAO->deleteOldSessions( self::$SESSION_LIFETIME );
		}


		return true;
	}

}
?>
