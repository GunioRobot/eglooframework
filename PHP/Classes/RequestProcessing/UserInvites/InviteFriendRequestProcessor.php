<?php
/**
 * InviteFriendRequestProcessor Class File
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
 * @package RequestProcessing
 * @version 1.0
 */

/**
 * InviteFriendRequestProcessor
 * 
 * Needs to be commented
 * 
 * @package RequestProcessing
 * @subpackage RequestProcessors
 */
class InviteFriendRequestProcessor extends RequestProcessor {
 	//<Decorator order="0" decoratorID="VerifyLoggedInDecorator" />	
  //<VariableArgument type="get" id="friendEmail" regex="/^\w+?(\.\w+?|)@\w+?.(biz|com|edu|gov|info|net|org)$/" required="true" />
    public function processRequest() {
    	
    	//get the current user id
  		if( !isset( $_SESSION['USER_ID'] ) ){
  			//TODO throw exception
  		}
		
//		$emailAddress = "keith.buel@gmail.com";
		$emailAddress = $this->requestInfoBean->getGET('friendEmail');
		$emailAddress = $emailAddress . "@" . $this->requestInfoBean->getGET('emailHost');
		$emailAddress = $emailAddress . "." . $this->requestInfoBean->getGET('emailDomain');

		$userID = $_SESSION['USER_ID'];
		//check number of invites left
		$inputValues = array();
 	    $inputValues[ 'user_id' ] = $userID;
 	    $daoFunction='getNumberOfInvitesLeft';
		$functionDAO = $this->processDaoRequest($daoFunction, $inputValues);
  		$userInvites = $functionDAO->get_output_numberofinvites();
  		
  		if( $userInvites > 0){
  			
  			//generate invite code:http://www.google.com/
  			$referralCode = $this->generateReferralCode();
  			while( ! $this->isReferralCodeUnique( $referralCode ) ){
  				$referralCode = $this->generateReferralCode();
  			} 

			if( $this->addUserInvite($userID, $emailAddress, $referralCode) ){

				$subject = "You've Been Invited to eGloo!";
				$firstName = $_SESSION[USER_FIRST_NAME];
				$lastName = $_SESSION[USER_LAST_NAME];
				
				$body = "$firstName $lastName has invited you to join eGloo.  Click on the following link to join!\n\n" .
				"http://www.egloo.com/#referral=$referralCode";
				//$headers = 'From: eGloo <donotreply@test.com' . "\r\n" . 'Reply-To: donotreply@test.com';
				$headers = ""; 
				if ( mail($emailAddress, $subject, $body, $headers) ) {
					eGlooLogger::writeLog( eGlooLogger::DEBUG, "MAIL SUCCESS" );
				 } else {
					eGlooLogger::writeLog( eGlooLogger::DEBUG, "MAIL FAIL" );
				 }
			}
  			
  		} else {
			eGlooLogger::writeLog( eGlooLogger::DEBUG, "Not enough invites left! for user: $userID" );
  			//TODO return an error
  		}
  		
		 
    }

	/**
	 * @return true if the referralId is unique, false otherwise.
 	 */
	private function isReferralCodeUnique($referralId){
		
		$daoFunction='isReferralIdUnique';
		
		$inputValues = array();
 	    $inputValues[ 'referral_id' ] = $referralId;	
	
		$gqDTO = $this->processDaoRequest($daoFunction, $inputValues);
		
		return $gqDTO->get_output_referralunique();
	}

	/**
	 * @param userID   		The user which requested the invitation.
	 * @param emailAddress  The email address to send the invitation to
	 * @param referralCode  The referral code used to log in to the system.
	 * @return true if successful, false otherwise.
	 */
	private function addUserInvite($userID, $emailAddress, $referralCode){
		$daoFunction='addUserInvite';
		
		$inputValues = array();
 	    $inputValues[ 'user_id' ] = $userID;
 	    $inputValues[ 'email_address' ] = $emailAddress;
 	    $inputValues[ 'referral_id' ] = $referralCode;	
 	    
 	    $gqDTO = $this->processDaoRequest($daoFunction, $inputValues);
 	    
 	    return $gqDTO->get_output_invitesuccessful();
	}

	/**
	 * @return string
	 */
	private function generateReferralCode(){

		// number of chars in the password
		$totalChar = 20; 
		
		//valid characters to choose from
		$salt = "abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ123456789";
		srand( (double) microtime() * 1000000 );
		
		$referralCode = "";
		for ($i=0;$i<$totalChar;$i++) $referralCode = $referralCode . substr ($salt, rand() % strlen($salt), 1);
		return $referralCode;

	}

	private function processDaoRequest($daoFunction, $inputValues){
		$daoFactory = AbstractDAOFactory::getInstance();
		$genericPLFunctionDAO = $daoFactory->getGenericPLFunctionDAO();
		return $genericPLFunctionDAO->selectGenericData( $daoFunction,  $inputValues );
	}
 }
?>
