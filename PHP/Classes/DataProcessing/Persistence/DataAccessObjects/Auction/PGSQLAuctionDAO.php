<?php
/**
 * PGSQLAuctionDAO Class File
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
 * @copyright 2011 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package Persistence
 * @version 1.0
 */

/**
 * PGSQLAuctionDAO
 *
 * Needs to be commented
 * 
 * @package Persistence
 */
 class PGSQLAuctionDAO extends AuctionDAO {
 	 	
 	
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
 	public function getAuctionSlotInfoRange( $fromTime, $toTime, $genre, $profileID ){
 		//hour 1
 		$_DTOArray[0] = new AuctionSlotDTO();
 		$_DTOArray[0]->setSlotID( 0000 );
 		$_DTOArray[0]->setSlotType( AuctionSlotDTO::$HOURLY );
 		$_DTOArray[0]->setBids( 5 );
 		$_DTOArray[0]->setPrice( 7 );
 		$_DTOArray[0]->setMonth( 04 );
 		$_DTOArray[0]->setDay( 03 );
 		$_DTOArray[0]->setYear( 2007 );
 		$_DTOArray[0]->setHour( "12" );
 		$_DTOArray[0]->setTimeOfDay( 'am' );
 		$_DTOArray[0]->setIsFinished( false );
 		$_DTOArray[0]->setUserBid( 6 ); 
 		$_DTOArray[1]->setBidRank( 9 );		
 		//hour 2
 		$_DTOArray[1] = new AuctionSlotDTO();
 		$_DTOArray[1]->setSlotID( 0001 );
 		$_DTOArray[1]->setSlotType( AuctionSlotDTO::$HOURLY );
 		$_DTOArray[1]->setBids( 7 );
 		$_DTOArray[1]->setPrice( 10 );
 		$_DTOArray[1]->setMonth( 04 );
 		$_DTOArray[1]->setDay( 03 );
 		$_DTOArray[1]->setYear( 2007 );
 		$_DTOArray[1]->setHour( "1" );
 		$_DTOArray[1]->setTimeOfDay( 'am' );
 		$_DTOArray[1]->setIsFinished( false );
 		$_DTOArray[1]->setUserBid( 12 );
 		$_DTOArray[1]->setBidRank( 2 );
 		//hour 3
 		$_DTOArray[2] = new AuctionSlotDTO();
 		$_DTOArray[2]->setSlotID( 0002 );
 		$_DTOArray[2]->setSlotType( AuctionSlotDTO::$HOURLY );
 		$_DTOArray[2]->setBids( 5 );
 		$_DTOArray[2]->setPrice( 15 );
 		$_DTOArray[2]->setMonth( 04 );
 		$_DTOArray[2]->setDay( 03 );
 		$_DTOArray[2]->setYear( 2007 );
 		$_DTOArray[2]->setHour( "2" );
 		$_DTOArray[2]->setTimeOfDay( 'am' );
 		$_DTOArray[2]->setIsFinished( false );
 		$_DTOArray[2]->setUserBid( 20 );
 		$_DTOArray[2]->setBidRank( 1 );
 		//hour 4
 		$_DTOArray[3] = new AuctionSlotDTO();
 		$_DTOArray[3]->setSlotID( 0003 );
 		$_DTOArray[3]->setSlotType( AuctionSlotDTO::$HOURLY );
 		$_DTOArray[3]->setBids( 7 );
 		$_DTOArray[3]->setPrice( 20 );
 		$_DTOArray[3]->setMonth( 04 );
 		$_DTOArray[3]->setDay( 03 );
 		$_DTOArray[3]->setYear( 2007 );
 		$_DTOArray[3]->setHour( "3" );
 		$_DTOArray[3]->setTimeOfDay( 'am' );
 		$_DTOArray[3]->setIsFinished( false );
 		$_DTOArray[3]->setUserBid( 20 );
 		$_DTOArray[3]->setBidRank( 5 );
 		//hour 5
 		$_DTOArray[4] = new AuctionSlotDTO();
 		$_DTOArray[4]->setSlotID( 0004 );
 		$_DTOArray[4]->setSlotType( AuctionSlotDTO::$HOURLY );
 		$_DTOArray[4]->setBids( 5 );
 		$_DTOArray[4]->setPrice( 25 );
 		$_DTOArray[4]->setMonth( 04 );
 		$_DTOArray[4]->setDay( 03 );
 		$_DTOArray[4]->setYear( 2007 );
 		$_DTOArray[4]->setHour( "4" );
 		$_DTOArray[4]->setTimeOfDay( 'am' );
 		$_DTOArray[4]->setIsFinished( false );
 		$_DTOArray[4]->setUserBid( 12 );
 		$_DTOArray[4]->setBidRank( 9 );
 		//hour 6
 		$_DTOArray[5] = new AuctionSlotDTO();
 		$_DTOArray[5]->setSlotID( 0005 );
 		$_DTOArray[5]->setSlotType( AuctionSlotDTO::$HOURLY );
 		$_DTOArray[5]->setBids( 7 );
 		$_DTOArray[5]->setPrice( 30 );
 		$_DTOArray[5]->setMonth( 04 );
 		$_DTOArray[5]->setDay( 03 );
 		$_DTOArray[5]->setYear( 2007 );
 		$_DTOArray[5]->setHour( "5" );
 		$_DTOArray[5]->setTimeOfDay( 'am' );
 		$_DTOArray[5]->setIsFinished( false );
 		$_DTOArray[5]->setUserBid( 35 );
 		$_DTOArray[5]->setBidRank( 1 );
 		//hour 7
 		$_DTOArray[6] = new AuctionSlotDTO();
 		$_DTOArray[6]->setSlotID( 0006 );
 		$_DTOArray[6]->setSlotType( AuctionSlotDTO::$HOURLY );
 		$_DTOArray[6]->setBids( 5 );
 		$_DTOArray[6]->setPrice( 35 );
 		$_DTOArray[6]->setMonth( 04 );
 		$_DTOArray[6]->setDay( 03 );
 		$_DTOArray[6]->setYear( 2007 );
 		$_DTOArray[6]->setHour( "6" );
 		$_DTOArray[6]->setTimeOfDay( 'am' );
 		$_DTOArray[6]->setIsFinished( false );
 		$_DTOArray[6]->setUserBid( 12 );
 		$_DTOArray[6]->setBidRank( 65 );
 		//hour 8
 		$_DTOArray[7] = new AuctionSlotDTO();
 		$_DTOArray[7]->setSlotID( 0007 );
 		$_DTOArray[7]->setSlotType( AuctionSlotDTO::$HOURLY );
 		$_DTOArray[7]->setBids( 5 );
 		$_DTOArray[7]->setPrice( 40 );
 		$_DTOArray[7]->setMonth( 04 );
 		$_DTOArray[7]->setDay( 03 );
 		$_DTOArray[7]->setYear( 2007 );
 		$_DTOArray[7]->setHour( "7" );
 		$_DTOArray[7]->setTimeOfDay( 'am' );
 		$_DTOArray[7]->setIsFinished( false );
 		$_DTOArray[7]->setUserBid( 12 );
 		$_DTOArray[7]->setBidRank( 26 );
 		//hour 9
 		$_DTOArray[8] = new AuctionSlotDTO();
 		$_DTOArray[8]->setSlotID( 0008 );
 		$_DTOArray[8]->setSlotType( AuctionSlotDTO::$HOURLY );
 		$_DTOArray[8]->setBids( 7 );
 		$_DTOArray[8]->setPrice( 45 );
 		$_DTOArray[8]->setMonth( 04 );
 		$_DTOArray[8]->setDay( 03 );
 		$_DTOArray[8]->setYear( 2007 );
 		$_DTOArray[8]->setHour( "8" );
 		$_DTOArray[8]->setTimeOfDay( 'am' );
 		$_DTOArray[8]->setIsFinished( false );
 		$_DTOArray[8]->setUserBid( 12 );
 		$_DTOArray[8]->setBidRank( 54 );
 		//hour 10
 		$_DTOArray[9] = new AuctionSlotDTO();
 		$_DTOArray[9]->setSlotID( 0009 );
 		$_DTOArray[9]->setSlotType( AuctionSlotDTO::$HOURLY );
 		$_DTOArray[9]->setBids( 5 );
 		$_DTOArray[9]->setPrice( 50 );
 		$_DTOArray[9]->setMonth( 04 );
 		$_DTOArray[9]->setDay( 03 );
 		$_DTOArray[9]->setYear( 2007 );
 		$_DTOArray[9]->setHour( "9" );
 		$_DTOArray[9]->setTimeOfDay( 'am' );
 		$_DTOArray[9]->setIsFinished( false );
 		$_DTOArray[9]->setUserBid( 45 );
 		$_DTOArray[9]->setBidRank( 9 );
 		//hour 11
 		$_DTOArray[10] = new AuctionSlotDTO();
 		$_DTOArray[10]->setSlotID( 0010 );
 		$_DTOArray[10]->setSlotType( AuctionSlotDTO::$HOURLY );
 		$_DTOArray[10]->setBids( 7 );
 		$_DTOArray[10]->setPrice( 55 );
 		$_DTOArray[10]->setMonth( 04 );
 		$_DTOArray[10]->setDay( 03 );
 		$_DTOArray[10]->setYear( 2007 );
 		$_DTOArray[10]->setHour( "10" );
 		$_DTOArray[10]->setTimeOfDay( 'am' );
 		$_DTOArray[10]->setIsFinished( false );
 		$_DTOArray[10]->setUserBid( 75 );
 		$_DTOArray[10]->setBidRank( 1 );
 		//hour 12
 		$_DTOArray[11] = new AuctionSlotDTO();
 		$_DTOArray[11]->setSlotID( 0011 );
 		$_DTOArray[11]->setSlotType( AuctionSlotDTO::$HOURLY );
 		$_DTOArray[11]->setBids( 5 );
 		$_DTOArray[11]->setPrice( 60 );
 		$_DTOArray[11]->setMonth( 04 );
 		$_DTOArray[11]->setDay( 03 );
 		$_DTOArray[11]->setYear( 2007 );
 		$_DTOArray[11]->setHour( "11" );
 		$_DTOArray[11]->setTimeOfDay( 'am' );
 		$_DTOArray[11]->setIsFinished( false );
 		$_DTOArray[11]->setUserBid( 12 );
 		$_DTOArray[11]->setBidRank( 65 );
 		
 		return $_DTOArray;
 	}
 	
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
    public function getAuctionSlotInfo( $fromTime, $toTime, $genre, $profileID ){
   		
   		$_DTO = new AuctionSlotDTO();
   		$_DTO->setSlotID( 0004 );
 		$_DTO->setSlotType( AuctionSlotDTO::$HOURLY );
 		$_DTO->setBids( 5 );
 		$_DTO->setPrice( 5 );
 		$_DTO->setMonth( 04 );
 		$_DTO->setDay( 08 );
 		$_DTO->setYear( 2007 );
 		$_DTO->setHour( "2" );
 		$_DTO->setTimeOfDay( "am" );
 		$_DTO->setGenre( "Techno" );
 		$_DTO->setIsFinished( false );
 		$_DTO->setUserBid( 4 );
 		$_DTO->setBidRank( 6 );
 		
   		return $_DTO;
    }      
    
    /**
	 * This function sets the bid price for a specific time slot.
     * 
     * @param slotID - the id of the auction slot
     * @param bidValue - the value that the user is willing to bid on the auction slot
     * @param profileID - The id of the user that submitted the bid.
     * 
     * @return boolean - true if successful, false otherwise.
     */
    public function setBidPrice( $slotID, $bidValue, $profileID ){
    	
    	return true;
    }
    
    /**
     * This function gets all current bids by a user.
     * 
     * @param profileID - The id of the user.
     * 
     * @return _DTOArray - bids by a user. 
     */
    public function getAuctionSlotsByUser( $profileID ){
    	
    	//bid 1
    	$_DTOArray[0] = new AuctionSlotDTO();
    	$_DTOArray[0]->setSlotID( 0020 );
    	$_DTOArray[0]->setSlotType( AuctionSlotDTO::$HOURLY );
 		$_DTOArray[0]->setBids( 5 );
 		$_DTOArray[0]->setPrice( 5 );
 		$_DTOArray[0]->setMonth( 03 );
 		$_DTOArray[0]->setDay( 13 );
 		$_DTOArray[0]->setYear( 2007 );
 		$_DTOArray[0]->setHour( "4" );
 		$_DTOArray[0]->setTimeOfDay( 'am' ); 		
 		$_DTOArray[0]->isFinished( true );
		$_DTOArray[0]->setUserBid( 7 );
		$_DTOArray[0]->setGenre( "Techno" );
		$_DTOArray[0]->setBidRank( 1 );
 		//bid 2
 		$_DTOArray[1] = new AuctionSlotDTO();
 		$_DTOArray[1]->setSlotID( 0026 );
 		$_DTOArray[1]->setSlotType( AuctionSlotDTO::$HOURLY );
 		$_DTOArray[1]->setBids( 5 );
 		$_DTOArray[1]->setPrice( 9 );
 		$_DTOArray[1]->setMonth( 03 );
 		$_DTOArray[1]->setDay( 13 );
 		$_DTOArray[1]->setYear( 2007 );
 		$_DTOArray[1]->setHour( "7" );
 		$_DTOArray[1]->setTimeOfDay( 'am' );
 		$_DTOArray[1]->isFinished( false );
		$_DTOArray[1]->setUserBid( 8 );
		$_DTOArray[1]->setGenre( "Techno" );
		$_DTOArray[1]->setBidRank( 6 );
		//bid 3
 		$_DTOArray[2] = new AuctionSlotDTO();
 		$_DTOArray[2]->setSlotID( 0039 );
 		$_DTOArray[2]->setSlotType( AuctionSlotDTO::$DAILY );
 		$_DTOArray[2]->setBids( 5 );
 		$_DTOArray[2]->setPrice( 12 );
 		$_DTOArray[2]->setMonth( 03 );
 		$_DTOArray[2]->setDay( 14 );
 		$_DTOArray[2]->setYear( 2007 );
 		$_DTOArray[2]->isFinished( false );
		$_DTOArray[2]->setUserBid( 7 );
		$_DTOArray[2]->setGenre( "Classical" );
		$_DTOArray[2]->setBidRank( 10 );
 		
   		return $_DTOArray;    	
    }     
    
 }

