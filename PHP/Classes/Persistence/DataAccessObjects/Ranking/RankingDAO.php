<?php
/**
 * RankingDAO Abstract Class File
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
 * @author Matthew Brennan
 * @copyright 2008 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package Persistence
 * @version 1.0
 */

/**
 * RankingDAO
 *
 * Needs to be commented
 * 
 * @package Persistence
 */
abstract class RankingDAO {
    
    /**
	 * This function creates a new ranking in the database.
	 * 
	 * @param $profileID  The ID of the profile that is creating the ranking
	 * @param $elementID  The ID of the element that is being ranked
	 * @param $ranking    The numerical value of the rank being given
	 * 
	 * @return RankingDTO object with the given parameters, NULL otherwise 
	 */
    abstract public function createNewRanking($profileID, $elementID, $ranking);

	/**
	 * This function retrieves the ranking given for a specific element.
	 *
	 * @param $elementID   The ID of the element for which the ranking was created
	 * 
	 * @return The int value representing the element's rank, NULL otherwise
	 */
	abstract public function getElementRanking( $elementID );
	
	/**
	 * This function retrieves the ranking given by a 
	 * specific profile for a specific element.
	 * 
	 * @param $profileID   The ID of the profile that created the ranking
	 * @param $elementID   The ID of the element for which the ranking was created
	 * 
	 * @return RankingDTO object, NULL otherwise
	 */
    abstract public function getProfileElementRanking( $profileID, $elementID );
    
}
?>
