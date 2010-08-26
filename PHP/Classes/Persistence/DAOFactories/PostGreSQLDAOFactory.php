<?php
/**
 * PostGreSQLDAOFactory Class File
 *
 * Needs to be commented
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
 * @author Keith Buel
 * @copyright 2008 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package Persistence
 * @version 1.0
 */

/**
 * PostGreSQLDAOFactory
 *
 * Concrete DAOFactory to create PGSQLDAO's
 * 
 * @package Persistence
 */
class PostGreSQLDAOFactory extends DAOFactory {

	/**
	 * @return a PGSQLSessionDAO
	 */
	public function getSessionDAO() {
		return new PGSQLSessionDAO(); 
	}

    public function getGlobalMenuBarDAO() {
        return new PGSQLGlobalMenuBarDAO();
    }
    
    public function getInformationBoardIcingDAO() {
        return new PGSQLInformationBoardIcingDAO();
    }

    public function getInformationBoardMusicDAO() {
        return new PGSQLInformationBoardMusicDAO();
    }

    public function getInformationBoardPeopleDAO() {
        return new PGSQLInformationBoardPeopleDAO();
    }

    public function getInformationBoardPicturesDAO() {
        return new PGSQLInformationBoardPicturesDAO();
    }

    public function getInformationBoardVideoDAO() {
        return new PGSQLInformationBoardVideoDAO();
    }

    public function getAccountDAO() {
        return new PGSQLAccountDAO();        
    }
    
    public function getUserProfileDAO(){
        return new PGSQLUserProfileDAO();    	
    }

    public function getUserProfilePageDAO(){
        return new PGSQLUserProfilePageDAO();       
    }

    public function getFriendsDAO(){
        return new PGSQLFriendsDAO();    	
    }
    
    public function getBlogDAO() {
        return new PGSQLBlogDAO();
    }

    public function getCubeDAO() {
        return new PGSQLCubeDAO();
    }

    public function getFridgeDAO() {
        return new PGSQLFridgeDAO();
    }

	public function getUserInvitesDAO() {
		return new PGSQLUserInvitesDAO();	
	}
	
	public function getSearchDAO() {
        return new PGSQLSearchDAO();
    }
    
    public function getRelationshipDAO() {
        return new PGSQLRelationshipDAO();
    }
    
    public function getImageDAO() {
        return new PGSQLImageDAO();
    }
    
    public function getGenericCubeDAO() {
        return new PGSQLGenericCubeDAO();
    }
    
    public function getAuctionDAO() {
    	return new PGSQLAuctionDAO();
    }
    
    public function getGenericPLFunctionDAO() {
    	return new PGSQLGenericPLFunctionDAO();
    }
}
?>
