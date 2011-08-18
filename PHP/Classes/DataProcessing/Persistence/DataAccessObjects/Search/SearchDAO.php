<?php
/**
 * SearchDAO Abstract Class File
 *
 * Needs to be commented
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
 * @category DataProcessing
 * @package Persistence
 * @subpackage DataAccessObjects
 * @version 1.0
 */

/**
 * SearchDAO
 *
 * Needs to be commented
 * 
 * @category DataProcessing
 * @package Persistence
 * @subpackage DataAccessObjects
 */
abstract class SearchDAO extends AbstractDAO {
    
    /**
     * This function searches the database for names and their associated
     * profiles based on a search string.  The search string will search
     * the user first name and last name fields in the database.
     * 
     * 
     * @param name - the name to search for
     * @param limit - integer - the number of results to return
     * @param offset - integer - the row number to start retrieving at
     * @return array - An array of SearchNameProfileResultDTOs 
     */
	abstract public function getNameAndProfileIDByName( $name, $limit, $offset );
    
}

