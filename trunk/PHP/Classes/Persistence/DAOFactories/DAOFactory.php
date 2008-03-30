<?php
/**
 * DAOFactory Class File
 *
 * Needs to be commented
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
 * @package Persistence
 * @version 1.0
 */

/**
 * DAOFactory
 * 
 * This class defines an abstract DAOFactory.  It determines the appropriate
 * concrete DAOFactory.  And then returns the correct requested DAO by calling
 * the appropriate functions on the concrete DAOFactory.
 * 
 * Details of this pattern can be seen on the following website:
 * http://java.sun.com/blueprints/corej2eepatterns/Patterns/DataAccessObject.html
 * 
 * @package Persistence
 */
class DAOFactory {

	//singleton holder
	private static $singleton;

	/**
	 * TODO: set this from a properties file telling us where to connect
	 */
	private static $DAO_TYPE = "PostGreSQLDAOFactory";

	/**
	 * Singleton access to this DAOFactory
	 * 
	 * @return DAOFactory the singleton reference of the DAOFactory
	 */
	public static function getInstance() {
		if (!isset (self :: $singleton)) {
			self :: $singleton = new DAOFactory();
		}

		return self :: $singleton;
	}

	/**
	 * This class returns the appropriate DAO factory as specified by
	 * an external property
	 * 
	 * @return DAOFactory a concrete DAO factory
	 */
	private function getAppropriateFactory() {

		//get the appropriate factory
		if (self :: $DAO_TYPE == "PostGreSQLDAOFactory") {
			return new PostGreSQLDAOFactory();
		}

	}

	/**
	 * This method returns the correct SessionDAO by calling the 
	 * appropriate concrete factory
	 * 
	 * @return SessionDAO concrete DAO obtained from the concrete factory
	 */
	public function getSessionDAO() {
		return $this->getAppropriateFactory()->getSessionDAO();
	}

	public function getGlobalMenuBarDAO() {
		return $this->getAppropriateFactory()->getGlobalMenuBarDAO();
	}

	public function getInformationBoardIcingDAO() {
		return $this->getAppropriateFactory()->getInformationBoardIcingDAO();
	}

	public function getInformationBoardMusicDAO() {
		return $this->getAppropriateFactory()->getInformationBoardMusicDAO();
	}

	public function getInformationBoardPeopleDAO() {
		return $this->getAppropriateFactory()->getInformationBoardPeopleDAO();
	}

	public function getInformationBoardPicturesDAO() {
		return $this->getAppropriateFactory()->getInformationBoardPicturesDAO();
	}

	public function getInformationBoardVideoDAO() {
		return $this->getAppropriateFactory()->getInformationBoardVideoDAO();
	}

	public function getAccountDAO() {
		return $this->getAppropriateFactory()->getAccountDAO();
	}

	public function getUserProfileDAO() {
		return $this->getAppropriateFactory()->getUserProfileDAO();
	}

    public function getUserProfilePageDAO() {
        return $this->getAppropriateFactory()->getUserProfilePageDAO();
    }

	public function getFriendsDAO() {
		return $this->getAppropriateFactory()->getFriendsDAO();
	}

	public function getBlogDAO() {
		return $this->getAppropriateFactory()->getBlogDAO();
	}

    public function getCubeDAO() {
        return $this->getAppropriateFactory()->getCubeDAO();
    }
    
    public function getFridgeDAO() {
        return $this->getAppropriateFactory()->getFridgeDAO();
    }

    public function getUserInvitesDAO() {
        return $this->getAppropriateFactory()->getUserInvitesDAO();
    }

    public function getSearchDAO() {
        return $this->getAppropriateFactory()->getSearchDAO();
    }
    
    public function getImageDAO() {
        return $this->getAppropriateFactory()->getImageDAO();
    }

    public function getRelationshipDAO() {
        return $this->getAppropriateFactory()->getRelationshipDAO();
    }

    public function getGenericCubeDAO() {
        return $this->getAppropriateFactory()->getGenericCubeDAO();
    }
    
    public function getAuctionDAO() {
    	return $this->getAppropriateFactory()->getAuctionDAO();
    }
    
    public function getGenericPLFunctionDAO() {
    	return $this->getAppropriateFactory()->getGenericPLFunctionDAO();
    }

}
?>
