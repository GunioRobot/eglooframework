<?php
/**
 * AccountDTO Class File
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
 * @author <UNKNOWN>
 * @copyright 2010 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package Data Transfer Objects
 * @version 1.0
 */

/**
 * AccountDTO
 * 
 * Needs to be commented
 *
 * @package Data Transfer Objects
 */
class AuctionSlotDTO extends DataTransferObject {

	public static $DAILY = 'DAILY';
	public static $HOURLY = 'HOURLY';

	private $_slotID = null;
	private $_slotType = null;
	private $_isFinished = false;		//finished is true, in progress is false
	private $_isWinning = false;		//winning is true, losing is false
	private $_month = null;
	private $_day = null;
	private $_year = null;
	private $_hour = null;
	private $_ampm = null;
	private $_bids = null;
	private $_price = null;
	private $_startingPrice = null;
	private $_intervalPrice = null;
	private $_userBid = null;
	private $_genre = null;
	private $_bidRank = null;
	
	public function getSlotID(){
		return $this->_slotID;
	}
	
	public function getSlotType(){
		return $this->_slotType;
	}
	
	public function isFinished(){
		return $this->_isFinished;
	}
	
	public function isWinning(){
		return $this->_isWinning;
	}
	
	public function getMonth(){
		return $this->_month;
	}
	
	public function getDay(){
		return $this->_day;
	}
	
	public function getYear(){
		return $this->_year;
	}
	
	public function getHour(){
		return $this->_hour;
	}
	
	public function getTimeOfDay(){
		return $this->_ampm;
	}
	
	public function getBids(){
		return $this->_bids;
	}
	
	public function getBidRank(){
		return $this->_bidRank;
	}
	
	public function getUserBid(){
		return $this->_userBid;		
	}
	
	public function getPrice(){
		return $this->_price;
	}
	
	public function getStartingPrice(){
		return $this->_startingPrice;
	}
	
	public function getIntervalPrice(){
		return $this->_intervalPrice;
	}
	
	public function getGenre(){
		return $this->_genre;
	}
	
	public function setSlotID( $slotID ){
		$this->_slotID = $slotID;
	}
	
	public function setSlotType( $slotType ){
		$this->_slotType = $slotType;
	}
	
	public function setIsFinished( $isFinished ){
		$this->_isFinished = $isFinished;
	}
	
	public function setIsWinning( $isWinning ){
		$this->_isWinning = $isWinning;
	}
		
	public function setMonth( $month ){
		$this->_month = $month;
	}
	
	public function setDay( $day ){
		$this->_day = $day;
	}
	
	public function setYear( $year ){
		$this->_year = $year;
	}
	
	public function setHour( $hour ){
		$this->_hour = $hour;
	}
	
	public function setTimeOfDay( $ampm ){
		$this->_ampm = $ampm;
	}
	
	public function setBids( $bids ){
		$this->_bids = $bids;
	}
	
	public function setBidRank( $bidRank ){
		$this->_bidRank = $bidRank;
	}

	public function setPrice( $price ){
		$this->_price = $price;
	}
	
	public function getStartingPrice( $startingPrice ){
		$this->_startingPrice = $startingPrice;
	}
	
	public function getIntervalPrice( $intervalPrice ){
		$this->_intervalPrice = $intervalPrice;
	}
	
	public function setUserBid( $userBid ){
		$this->_userBid = $userBid;
	}
	
	public function setGenre( $genre ){
		$this->_genre = $genre;
	}
}

?>
