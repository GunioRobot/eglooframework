<?php
/**
 * CreditCard Class File
 *
 * $file_block_description
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
 * @package $package
 * @subpackage $subpackage
 * @version 1.0
 */

/**
 * CreditCard
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class CreditCard implements PaymentMethod {

	/* Private Data Members */
	private $_authorized_card_users = array();
	private $_billingAddress = null;
	private $_billingPhoneNumber = null;
	private $_billing_due_date = null;
	private $_cardBalance = null;
	private $_cardholderAccount = null;
	private $_card_number = null;
	private $_card_security_code = null;
	private $_creditLimit = null;
	private $_default_currency = null;
	private $_encrypted_card_number = null;
	private $_expirationDate = null;
	private $_glyph = null;
	private $_interest_rate = null;
	private $_last_digits = null;
	private $_minimum_payment_percentage = null;
	private $_pending_charges = array();
	private $_security_questions = array();
	private $_statementDate = null;
	private $_transactions = array();
	private $_transactionsToProcess = array();

	/* Protected Data Members */
	protected $_card_issuer = null;
	protected $_cardIssuerPhoneNumber = null;
	protected $_card_number_pattern = null;
	protected $_card_security_code_pattern = null;
	protected $_card_type = null;

	public function __construct() {
		
	}

	public function __destruct() {
		
	}

	public function getAuthorizedCardUsers() {
		return $this->_authorized_card_users;
	}

	public function setAuthorizedCardUsers( $authorized_card_users ) {
		$this->_authorized_card_users = $authorized_card_users;
	}

	public function getBillingAddress() {
		return $this->_billingAddress;
	}

	public function setBillingAddress( $billingAddress ) {
		$this->_billingAddress = $billingAddress;
	}

	public function getBillingPhoneNumber() {
		return $this->_billingPhoneNumber;
	}

	public function setBillingPhoneNumber( $billingPhoneNumber ) {
		$this->_billingPhoneNumber = $billingPhoneNumber;
	}

	public function getBillingDueDate() {
		return $this->_billing_due_date;
	}

	public function setBillingDueDate( $billing_due_date ) {
		$this->_billing_due_date = $billing_due_date;
	}

	public function getCardBalance() {
		return $this->_cardBalance;
	}

	public function setCardBalance( Currency $cardBalance ) {
		$this->_cardBalance = $cardBalance;
	}

	public function getCardholderAccount() {
		return $this->_cardholderAccount;
	}

	public function setCardholderAccount( $cardholderAccount ) {
		$this->_cardholderAccount = $cardholderAccount;
	}

	public function getCardIssuer() {
		return $this->_card_issuer;
	}

	public function setCardIssuer( $card_issuer ) {
		$this->_card_issuer = $card_issuer;
	}

	public function getCardIssuerPhoneNumber() {
		return $this->_cardIssuerPhoneNumber;
	}

	public function setCardIssuerPhoneNumber( $cardIssuerPhoneNumber ) {
		$this->_cardIssuerPhoneNumber = $cardIssuerPhoneNumber;
	}

	public function getCardNumber() {
		return $this->_card_number;
	}

	public function setCardNumber( $card_number ) {
		$this->_card_number = $card_number;
	}

	public function getCardNumberPattern() {
		return $this->_card_number_pattern;
	}

	public function setCardNumberPattern( $card_number_pattern ) {
		$this->_card_number_pattern = $card_number_pattern;
	}

	public function getCardSecurityCode() {
		return $this->_card_security_code;
	}

	public function setCardSecurityCode( $card_security_code ) {
		$this->_card_security_code = $card_security_code;
	}

	public function getCardSecurityCodePattern() {
		return $this->_card_security_code_pattern;
	}

	public function setCardSecurityCodePattern( $card_security_code_pattern ) {
		$this->_card_security_code_pattern = $card_security_code_pattern;
	}

	public function getCardType() {
		return $this->_card_type;
	}

	public function setCardType( $card_type ) {
		$this->_card_type = $card_type;
	}

	public function getCreditLimit() {
		return $this->_creditLimit;
	}

	public function setCreditLimit( Currency $creditLimit ) {
		$this->_creditLimit = $creditLimit;
	}

	public function getDefaultCurrency() {
		return $this->_default_currency;
	}

	public function setDefaultCurrency( $default_currency ) {
		$this->_default_currency = $default_currency;
	}

	public function getEncryptedCardNumber() {
		return $this->_encrypted_card_number;
	}

	public function setEncryptedCardNumber( $encrypted_card_number ) {
		$this->_encrypted_card_number = $encrypted_card_number;
	}

	public function getExpirationDate() {
		return $this->_expirationDate;
	}

	public function setExpirationDate( $expirationDate ) {
		$this->_expirationDate = $expirationDate;
	}

	public function getGlyph() {
		return $this->_glyph;
	}

	public function setGlyph( $glyph ) {
		$this->_glyph = $glyph;
	}

	public function getInterestRate() {
		return $this->_interest_rate;
	}

	public function setInterestRate( $interest_rate ) {
		$this->_interest_rate = $interest_rate;
	}

	public function getLastDigits() {
		return $this->_last_digits;
	}

	public function setLastDigits( $last_digits ) {
		$this->_last_digits = $last_digits;
	}

	public function getMinimumPaymentPercentage() {
		return $this->_minimum_payment_percentage;
	}

	public function setMinimumPaymentPercentage( $minimum_payment_percentage ) {
		$this->_minimum_payment_percentage = $minimum_payment_percentage;
	}

	public function getPendingCharges() {
		return $this->_pending_charges;
	}

	public function setPendingCharges( $pending_charges ) {
		$this->_pending_charges = $pending_charges;
	}

	public function getSecurityQuestions() {
		return $this->_security_questions;
	}

	public function setSecurityQuestions( $security_questions ) {
		$this->_security_questions = $security_questions;
	}

	public function getStatementDate() {
		return $this->_statementDate;
	}

	public function setStatementDate( $statementDate ) {
		$this->_statementDate = $statementDate;
	}

	public function getTransactions() {
		return $this->_transactions;
	}

	public function setTransactions( $transactions ) {
		$this->_transactions = $transactions;
	}

	public function getTransactionsToProcess() {
		return $this->_transactions_to_process;
	}

	public function setTransactions( $transactions ) {
		$this->_transactions_to_process = $transactions;
	}

	public function __toString() {
		
	}

	static public function encryptCardNumber() {
		
	}

	
}

