<?php
/**
 * AuctionDAO Abstract Class File
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
 * @author Mark Doten
 * @copyright 2010 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package Persistence
 * @version 1.0
 */

/**
 * AuctionDAO
 *
 * Needs to be commented
 * 
 * @package Persistence
 */
abstract class AuctionDAO extends AbstractDAO {       
    
    /**
 	 * This function gets the bid price and number of bids for each time slot during a given time period
 	 * 
 	 * @param fromTime - timestamp for the start of the range
 	 * @param toTime - timestamp for the end of the range
 	 * @param genre - genre of the auction slot
 	 * @param profileID - id of the user
 	 * 
 	 * @return _DTOArray - all info for each time section of the day. 
 	 */
 	abstract public function getAuctionSlotInfoRange( $fromTime, $toTime, $genre, $profileID );
 	
    /**
	 * This function gets the bid price for any time slots.
	 * 
	 * @param fromTime - timestamp for the start of the range
 	 * @param toTime - timestamp for the end of the range
 	 * @param genre - genre of the auction slot
 	 * @param profileID - id of the user
	 * 
	 * @return _DTO - all info for this specific auction slot
	 */
    abstract public function getAuctionSlotInfo( $fromTime, $toTime, $genre, $profileID );
   	
    /**
	 * This function sets the bid price for a specific time slot.
     * 
     * @param slotID - the id of the auction slot
     * @param bidValue - the value that the user is willing to bid on the auction slot
     * @param profileID - The id of the user that submitted the bid.
     * 
     * @return boolean - true if successful, false otherwise.
     */
    abstract public function setBidPrice( $slotID, $bidValue, $profileID );
    
    /**
     * This function gets all current bids by a user.
     * 
     * @param profileID - The id of the user.
     * 
     * @return _DTOArray - bids by a user. 
     */
    abstract public function getAuctionSlotsByUser ( $profileID );
     
}
 
