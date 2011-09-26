<?php
/**
 * MassUserInviteRequestProcessor Class File
 *
 * Contains the class definition for the MassUserInviteRequestProcessor, a
 * subclass of the RequestProcessor abstract class.
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
 * @author George Cooper
 * @copyright 2011 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package RequestProcessing
 * @version 1.0
 */

/**
 * MassUserInviteRequestProcessor
 * 
 * Handles requests to parse a list of email addresses, generate referral codes and email
 * users a referral code with an invite message
 * 
 * @package RequestProcessing
 * @subpackage RequestProcessors
 */
class MassUserInviteRequestProcessor extends RequestProcessor {

 	public function processRequest() {
    	
    	// get the current user id
  		if( !isset( $_SESSION['USER_ID'] ) ){
  			//TODO throw exception
  		}
		
  		$emailAddresses = $this->requestInfoBean->getPOST('emailAddresses');
  		
  		$emailAddressList = array_unique( preg_split( '/[\s,]+/', $emailAddresses, 30, PREG_SPLIT_NO_EMPTY ) );
  		
  		$userInvitesRequested = count( $emailAddressList );
  		
  		if ( $userInvitesRequested <= 0 ) {
  			// TODO throw exception; shouldn't get here due to Dispatch rules...
  			return false;
		}
  		
  		foreach ( $emailAddressList as $address ) {
			eGlooLogger::writeLog( eGlooLogger::DEBUG, "MassUserInvite: " . trim( $address ) );
  		}

  		$hostname = eGlooHTTPRequest::getServerName();
  		
		$userID = $_SESSION['USER_ID'];
		
		//check number of invites left
		$inputValues = array();
 	    $inputValues[ 'user_id' ] = $userID;
 	    $daoFunction = 'getNumberOfInvitesLeft';
		$functionDAO = $this->processDaoRequest( $daoFunction, $inputValues );
  		$userInvitesLeft = $functionDAO->get_output_numberofinvites();
  		
  		if ( ( $userInvitesLeft >= $userInvitesRequested ) && $userInvitesLeft > 0 ) {
 			foreach ( $emailAddressList as $emailAddress ) {
	  			//generate invite code:http://www.google.com/
	  			$referralCode = $this->generateReferralCode();
	
	  			while( ! $this->isReferralCodeUnique( $referralCode ) ){
	  				$referralCode = $this->generateReferralCode();
	  			} 
	
				if ( $this->addUserInvite( $userID, $emailAddress, $referralCode ) ) {
	
					$subject = "You've Been Invited to Join eGloo!";
					$firstName = $_SESSION['USER_FIRST_NAME'];
					$lastName = $_SESSION['USER_LAST_NAME'];
					
					$body = $firstName . ' ' . $lastName . ' has invited you to join the eGloo Beta Launch.  ' . 
						'Click on the following link to sign up!' . "\n\n" . 'http://' . $hostname . '/#referral=' . $referralCode . "\n\n";
					
					$body .= 'eGloo supports Firefox 2.0+, Camino 1.5+ and Internet Explorer 6+' . "\n\n";
					
					$body .= 'Please update your browser for the best possible experience.';

					// TODO fix trusted users so that this doesn't trigger sendmail's spam headers
					$headers = "";
					//$headers = 'From: eGloo <do_not_reply@egloo.com>' . "\r\n";
					//$postfixParams = "-f user_invite@egloo.com -F user_invite@egloo.com";
					if ( mail( $emailAddress, $subject, $body, $headers ) ) {
						eGlooLogger::writeLog( eGlooLogger::DEBUG, "MAIL SUCCESS" );
					} else {
						eGlooLogger::writeLog( eGlooLogger::DEBUG, "MAIL FAIL" );
					}
				} else {
					// TODO handle inviting this user failing
				}
 			}
  		} else if ( $userInvitesLeft >= $userInvitesRequested ) {
			eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Not enough invites to complete request for user: ' . $userID );
			eGlooLogger::writeLog( eGlooLogger::DEBUG, 
				$userInvitesRequested . ' invites requested, ' .$userInvitesLeft . ' invites remain.' );
  		} else {
			eGlooLogger::writeLog( eGlooLogger::DEBUG, "No invites left for user: $userID" );
  			//TODO return an error
  		}
  		
  		header( 'Location: http://' . $hostname . '/invite/massUserInviteFormBase/' );
    }

	/**
	 * @return true if the referralId is unique, false otherwise.
 	 */
	private function isReferralCodeUnique( $referralId ) {
		
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
