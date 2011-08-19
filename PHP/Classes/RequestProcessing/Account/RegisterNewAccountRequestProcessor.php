<?php
/**
 * RegisterNewAccountRequestProcessor Class File
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
 * RegisterNewAccountRequestProcessor
 *
 * Needs to be commented
 * 
 * @package RequestProcessing
 * @subpackage Account
 */
class RegisterNewAccountRequestProcessor extends RequestProcessor {
    
    const STRONG_HASH = 'sha256';
    
    private $_templateDefault = '../Templates/Frameworks/Common/XMLHTTPResponse/Account/RegisterNewAccountXMLHTTPResponse.xmltpl';
    
    public function processRequest() {
        $this->_templateEngine = new XHTMLDefaultTemplateEngine( 'dev', 'us' );

        $accountDTO = null;

        $requestedPassword = $this->requestInfoBean->getPOST('userRequestedAccountPassword');
        $confirmedPassword = $this->requestInfoBean->getPOST('userConfirmedAccountPassword');

		$daoFactory = AbstractDAOFactory::getInstance();
		$userInvitesDAO = $daoFactory->getUserInvitesDAO();


        if ( $this->requestInfoBean->getRequestID() !== 'registerNewAccount' ) {
            // Throw exception
            // TODO throw exception; this is a programmer error
        } 
        
        //check if they accept the user license
        else if ( !$this->requestInfoBean->issetPOST('userAcceptsLicense') ) {
            $accountDTO = new AccountDTO();
            $accountDTO->setRegistrationSuccessful( false );
            $accountDTO->setRegistrationError( 'Invalid Registration Request: User did not accept license agreement' );
        }
        
        //check that the passwords match
        else if ( $requestedPassword !== $confirmedPassword ) {
            $accountDTO = new AccountDTO();
            $accountDTO->setRegistrationSuccessful( false );
            $accountDTO->setRegistrationError( 'Invalid Registration Request: Requested password and confirmation password do not match' );
        } 
        
        //check if this referral code
        else if( ! $userInvitesDAO->isReferralCodeValid( $this->requestInfoBean->getPOST('userReferralCode') ) ){
            $accountDTO = new AccountDTO();
            $accountDTO->setRegistrationSuccessful( false );
            $accountDTO->setRegistrationError( 'Invalid Registration Request: Referral Code is not valid' );
		} 
		
		//else.. process the login
		else {
			// TODO rename this because it makes no sense; should registration
			$accountDTO = $this->processLogin();
			
		}

        header("Content-type: application/xml; charset=UTF-8");
        $this->_templateEngine->assign( 'accountDTO', $accountDTO );
        $this->_templateEngine->display( $this->_templateDefault );
    }
        
    /**
     * 
     * 
     * @return $accountDTO
     */
    private function processLogin(){

        $accountName = $this->requestInfoBean->getPOST('userPreferredAccountName');
        $password = $requestedPassword = hash(self::STRONG_HASH, $this->requestInfoBean->getPOST('userRequestedAccountPassword') );
        
        $userEmail = $this->requestInfoBean->getPOST('userEmail');
        $firstName = $this->requestInfoBean->getPOST('firstNameInput');
        $lastName = $this->requestInfoBean->getPOST('lastNameInput');
        $gender = $this->requestInfoBean->getPOST('userGender');

        $birthMonth = $this->requestInfoBean->getPOST('userBirthMonth');
        $birthDay = $this->requestInfoBean->getPOST('userBirthDay');
        $birthYear = $this->requestInfoBean->getPOST('userBirthYear');
        $referralCode = $this->requestInfoBean->getPOST('userReferralCode');

		$daoFactory = AbstractDAOFactory::getInstance();
		$accountDAO = $daoFactory->getAccountDAO();
		$accountDTO = $accountDAO->registerNewAccount( 	$accountName, $password, $userEmail, 
														$firstName, $lastName, $gender, 
                                                        $birthMonth, $birthDay, $birthYear, $referralCode );

		if( $accountDTO->registrationSuccessful() ){
			
			
			$userDTO = $accountDTO->getUserDTO();
			$userID = $userDTO->getUserID();
			
			$userInvitesDAO = $daoFactory->getUserInvitesDAO();
			$userAssocLevel = $userInvitesDAO->getUserAssociationLevel( $userID );
			
			//set the new user their appropriate number of invites
			$this->setNumberOfInvites( $userID );
			
			$confirmationCode = $this->generateConfirmationCode();
			while( ! $this->isConfirmationCodeUnique( $confirmationCode ) ){
  				$confirmationCode = $this->generateConfirmationCode();
  			} 
  			
  			$userDTO = $accountDTO->getUserDTO();
  			
  			//set confirmation code
            $daoFunction = 'setUserInvitationConfirmation';
			$inputValues = array();
 	    	$inputValues[ 'referral_id' ] = $this->requestInfoBean->getPOST('userReferralCode');
 	    	$inputValues[ 'inviteduser_id' ] = $userDTO->getUserID();
 	    	$inputValues[ 'confirmation_id' ] = $confirmationCode;
 	    	 	    	
 	    	$daoFactory = AbstractDAOFactory::getInstance();
			$genericPLFunctionDAO = $daoFactory->getGenericPLFunctionDAO();
			$gqDTO = $genericPLFunctionDAO->selectGenericData( $daoFunction,  $inputValues );
			
			
			//Send email:
			$userID = $userDTO->getUserID();
			$hostname = eGlooHTTPRequest::getServerName();
			
			$subject = "eGloo Account Confirmation";
				
			$body = "Hi $firstName!\n\nPlease click the following link to activate your eGloo account!\n\n" .
				'http://' . $hostname . "/account/activateAccount/&uID=$userID&confirmationID=$confirmationCode\n\n";
			
			$body .= "To get started, click on the \"Fridge\" icon in the navigation bar, select" . 
				" some cubes you would like to add to your page and then click \"create cube(s).\"\n\n";
			
			$body .= "To set a profile picture, click \"Profile Image\" and choose an image to upload from your computer.\n\n";
			
			$body .= "Blogging functionality is basically feature complete and we're pretty happy with how that's turned out.  " . 
				"It's a good place to start to get a feel for the direction eGloo is headed in.\n\n";
			
			$body .= "eGloo is very much a work in progress; we're really stretching the term \"Beta\" here.  " . 
				"But we appreciate you helping us get started and welcome any feedback (feedback@egloo.com) or " .
				"bug reports (report_bug@egloo.com) you would like to send.  Please be sure to provide the name and version " . 
				"of the web browser and operating system you are using.  Attached screenshots are welcome.\n\n";
			
			$body .= "In the meantime, keep an eye on the front page and the eGloo Blog for the latest updates.  " . 
				"Now that we're up and running, you can expect to see feature additions, improvements and bug fixes on " . 
				"a daily basis.\n\n";
			
			$body .= "This is the start of a wild ride and we're thrilled to have you here with us at the beginning.\n\n";
			
			$body .= "Sincerely,\nThe eGloo Development Team";
			
			$headers = ""; 
			if ( mail($userEmail, $subject, $body, $headers) ) {
				eGlooLogger::writeLog( eGlooLogger::DEBUG, "MAIL SUCCESS" );
			 } else {
				eGlooLogger::writeLog( eGlooLogger::DEBUG, "MAIL FAIL" );
				//TODO: delete account?  Or just delete all non active accounts after a
				//certain amount of time?
			 }
			
			eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Login successful' );
		} else {
			eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Login Failed. ERROR REASON: ' . $accountDTO->getRegistrationError() );
		}
		
		return $accountDTO;
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