<?php
/**
 * RegisterAndActivateNewAccountRequestProcessor Class File
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
 * @author George Cooper
 * @author Keith Buel
 * @copyright 2011 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package RequestProcessing
 * @subpackage Account
 * @version 1.0
 */

/**
 * RegisterAndActivateNewAccountRequestProcessor
 *
 * This class registers and activates a new account in one shot, without
 * sending a confirmation code.  This is useful for testing purposes,
 * but probably should not be used in production code.
 * 
 * @package RequestProcessing
 * @subpackage Account
 */
class RegisterAndActivateNewAccountRequestProcessor extends RequestProcessor {

	const STRONG_HASH = 'sha256';
	
	private $_templateDefault = '../Templates/Frameworks/Common/XMLHTTPResponse/Account/RegisterNewAccountXMLHTTPResponse.xmltpl';

	public function processRequest() {
    	
		//check number of invites left
		$inputValues = array();
 	    $inputValues[ 'input_username' ] = 'egAppAdmin';
 	    $daoFunction = 'getUserID';
		$functionDAO = $this->processDaoRequest( $daoFunction, $inputValues );
  		$userID = $functionDAO->get_output_user_id();
		
  		//check number of invites left
		$inputValues = array();
 	    $inputValues[ 'user_id' ] = $userID;
 	    $daoFunction = 'getNumberOfInvitesLeft';
		$functionDAO = $this->processDaoRequest( $daoFunction, $inputValues );
  		$userInvitesLeft = $functionDAO->get_output_numberofinvites();
  		
  		$referralCode = 0;
  		
  		if ( $userInvitesLeft > 0 ) {
			$referralCode = $this->generateReferralCode();
	
  			while ( ! $this->isReferralCodeUnique( $referralCode ) ) {
  				$referralCode = $this->generateReferralCode();
  			} 

			if ( $this->addUserInvite( $userID, $this->requestInfoBean->getPOST('userEmail'), $referralCode ) ) {
			} else {
				// TODO handle inviting this user failing
			}
  		} else {
			eGlooLogger::writeLog( eGlooLogger::DEBUG, "No invites left for user: $userID" );
  			//TODO return an error
  		}
  		
        $this->_templateEngine = new XHTMLDefaultTemplateEngine( 'dev', 'us' );

        $accountDTO = null;

        $requestedPassword = $this->requestInfoBean->getPOST('userRequestedAccountPassword');
        $confirmedPassword = $this->requestInfoBean->getPOST('userConfirmedAccountPassword');

		$daoFactory = AbstractDAOFactory::getInstance();
		$userInvitesDAO = $daoFactory->getUserInvitesDAO();

        if ( $this->requestInfoBean->getRequestID() !== 'registerAndActivateNewAccount' ) {
            // Throw exception
            // TODO throw exception; this is a programmer error
        } else if ( !$this->requestInfoBean->issetPOST('userAcceptsLicense') ) {
        	//check if they accept the user license
            $accountDTO = new AccountDTO();
            $accountDTO->setRegistrationSuccessful( false );
            $accountDTO->setRegistrationError( 'Invalid Registration Request: User did not accept license agreement' );
        } else if ( $requestedPassword !== $confirmedPassword ) {
        	//check that the passwords match
            $accountDTO = new AccountDTO();
            $accountDTO->setRegistrationSuccessful( false );
            $accountDTO->setRegistrationError( 'Invalid Registration Request: Requested password and confirmation password do not match' );
		} else {
			// TODO rename this because it makes no sense; should registration
			eGlooLogger::writeLog( eGlooLogger::DEBUG, "Registration Form Validation Successful" );
			
	        $accountName = $this->requestInfoBean->getPOST('userPreferredAccountName');
	        $password = $requestedPassword = hash(self::STRONG_HASH, $this->requestInfoBean->getPOST('userRequestedAccountPassword') );
	        
	        $userEmail = $this->requestInfoBean->getPOST('userEmail');
	        $firstName = $this->requestInfoBean->getPOST('firstNameInput');
	        $lastName = $this->requestInfoBean->getPOST('lastNameInput');
	        $gender = $this->requestInfoBean->getPOST('userGender');
	
	        $birthMonth = $this->requestInfoBean->getPOST('userBirthMonth');
	        $birthDay = $this->requestInfoBean->getPOST('userBirthDay');
	        $birthYear = $this->requestInfoBean->getPOST('userBirthYear');
	
			$daoFactory = AbstractDAOFactory::getInstance();
			$accountDAO = $daoFactory->getAccountDAO();
			$accountDTO = $accountDAO->registerNewAccount( 	$accountName, $password, $userEmail, 
															$firstName, $lastName, $gender, 
	                                                        $birthMonth, $birthDay, $birthYear, $referralCode );
	                                                        
			if( $accountDTO->registrationSuccessful() ){
				
				
				$userDTO = $accountDTO->getUserDTO();
				$userID = $userDTO->getUserID();
				
				$userInvitesDAO = $daoFactory->getUserInvitesDAO();
				$userAssocLevel = $userInvitesDAO->getUserAssociationLevel( $userProfileID );
				
				//set the new user their appropriate number of invites
				$this->setNumberOfInvites( $userProfileID );
				
				$confirmationCode = $this->generateConfirmationCode();
				while( ! $this->isConfirmationCodeUnique( $confirmationCode ) ){
	  				$confirmationCode = $this->generateConfirmationCode();
	  			} 
	  			
	  			$userDTO = $accountDTO->getUserDTO();
	  			
	  			//set confirmation code
	            $daoFunction = 'setUserInvitationConfirmation';
				$inputValues = array();
	 	    	$inputValues[ 'referral_id' ] = $referralCode;
	 	    	$inputValues[ 'inviteduser_id' ] = $userDTO->getUserID();
	 	    	$inputValues[ 'confirmation_id' ] = $confirmationCode;
	 	    	 	    	
	 	    	$daoFactory = AbstractDAOFactory::getInstance();
				$genericPLFunctionDAO = $daoFactory->getGenericPLFunctionDAO();
				$gqDTO = $genericPLFunctionDAO->selectGenericData( $daoFunction,  $inputValues );
				
				
				//Send email:
				$userID = $userDTO->getUserID();
				$hostname = eGlooHTTPRequest::getServerName();
								
		    	$confirmationID = $confirmationCode;
		        $userID = $userDTO->getUserID();
		    	
		    	//set confirmation code
		        $daoFunction = 'activateUserAccount';
				$inputValues = array();
		 	    $inputValues[ 'inviteduser_id' ] = $userID;
		 	    $inputValues[ 'confirmation_id' ] = $confirmationID;
		 	    	 	    	
		 	    $daoFactory = AbstractDAOFactory::getInstance();
				$genericPLFunctionDAO = $daoFactory->getGenericPLFunctionDAO();
				$gqDTO = $genericPLFunctionDAO->selectGenericData( $daoFunction,  $inputValues );
				
				$success = false;
				if( $gqDTO->get_output_successful() ) {
					$success = true;
					eGlooLogger::writeLog( eGlooLogger::DEBUG, "Account activation SUCCESS for user: $userID"  );
				} else {
					eGlooLogger::writeLog( eGlooLogger::DEBUG, "Account activation FAILURE for user: $userID"  );
				}
				
				$templateVariables['success'] = $success;

			} else {
				eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Login Failed. ERROR REASON: ' . $accountDTO->getRegistrationError() );
			}
			
		}

        header("Content-type: application/xml; charset=UTF-8");
        $this->_templateEngine->assign( 'accountDTO', $accountDTO );
        $this->_templateEngine->display( $this->_templateDefault );
  		
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

    /**
     * Sets this user a number of invites
     * 
     * @param uerID
     */
    private function setNumberOfInvites( $userID ){
		
		$daoFactory = AbstractDAOFactory::getInstance();
		$userInvitesDAO = $daoFactory->getUserInvitesDAO();
		$userAssociationLevel = $userInvitesDAO->getUserAssociationLevel( $userID );
	
		if( $userAssociationLevel === 1 ){
			$userInvitesDAO->setNumberOfInvites($userID, 5);
		} else if( $userAssociationLevel === 2 ){
			$userInvitesDAO->setNumberOfInvites($userID, 3);				
		} else {
			$userInvitesDAO->setNumberOfInvites($userID, 0);				
		}
		
    }

	/**
	 * @return string
	 */
	private function generateConfirmationCode(){

		// number of chars in the password
		$totalChar = 20; 
		
		//valid characters to choose from
		$salt = "abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ123456789";
		srand( (double) microtime() * 1000000 );
		
		$confirmationCode = "";
		for ($i=0;$i<$totalChar;$i++) $confirmationCode = $confirmationCode . substr ($salt, rand() % strlen($salt), 1);
		return $confirmationCode;

	}

	/**
	 * @return true if the referralId is unique, false otherwise.
 	 */
	private function isConfirmationCodeUnique($confirmationId){
		
		$daoFunction='isConfirmationIDUnique';
		
		$inputValues = array();
 	    $inputValues[ 'confirmation_id' ] = $confirmationId;	
	
		$daoFactory = AbstractDAOFactory::getInstance();
		$genericPLFunctionDAO = $daoFactory->getGenericPLFunctionDAO();
		$gqDTO = $genericPLFunctionDAO->selectGenericData( $daoFunction,  $inputValues );
 	    
		return $gqDTO->get_output_confirmationunique();
	}
        
}

?>