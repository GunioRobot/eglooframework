<?php
/**
 * RegistrationDTO Class File
 *
 * $file_block_description
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
 * @author George Cooper
 * @copyright 2010 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package $package
 * @subpackage $subpackage
 * @version 1.0
 */

/**
 * RegistrationDTO
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class RegistrationDTO extends DataTransferObject {

	// Registration Transaction
	protected $_registration_id = null;

	// Form Meta
	protected $_form_action = null;

	// Form Localization
	protected $_displayLocalization = null;
	protected $_inputLocalization = null;

	// Address Information
	protected $_address_id = null;
	protected $_address_attention = null;
	protected $_address_lines = null;
	protected $_address_locality = null;
	protected $_address_town = null;
	protected $_address_region = null;
	protected $_address_province = null;
	protected $_address_postal_code = null;
	protected $_address_country = null;
	protected $_address_additional_info = null;

	// DTO Version
	protected $_addressDTO = null;

	// Billing Address Information
	// protected $_billing_address_id = null;
	// protected $_billing_address_attention = null;
	// protected $_billing_address_lines = null;
	// protected $_billing_address_locality = null;
	// protected $_billing_address_town = null;
	// protected $_billing_address_region = null;
	// protected $_billing_address_province = null;
	// protected $_billing_address_postal_code = null;
	// protected $_billing_address_country = null;
	// protected $_billing_address_additional_info = null;

	// DTO Version
	// protected $_billingAddressDTO = null;

	// Shipping Address Information
	// protected $_shipping_address_id = null;
	// protected $_shipping_address_attention = null;
	// protected $_shipping_address_lines = null;
	// protected $_shipping_address_locality = null;
	// protected $_shipping_address_town = null;
	// protected $_shipping_address_region = null;
	// protected $_shipping_address_province = null;
	// protected $_shipping_address_postal_code = null;
	// protected $_shipping_address_country = null;
	// protected $_shipping_address_additional_info = null;

	// DTO Version
	// protected $_shippingAddressDTO = null;

	// Email Address
	protected $_primary_email_address = null;
	protected $_secondary_email_address = null;

	// DTO Version
	protected $_emailAddressDTO = null;

	// Password Fields
	protected $_password_field_one = null;
	protected $_password_field_two = null;

	// Payment Method
	// protected $_payment_method_id;

	// DTO Version
	// protected $_paymentMethodDTO = null;

	// Person Information
	protected $_first_name = null;
	protected $_middle_name_or_initial = null;
	protected $_last_name = null;

	protected $_birth_month = null;
	protected $_birth_day = null;
	protected $_birth_year = null;

	protected $_gender = null;

	// DTO Version
	protected $_personDTO = null;

	// Phone
	protected $_primary_phone_number = null;
	protected $_secondary_phone_number = null;

	// DTO Version
	protected $_phoneDTO = null;

	// Additional Contact Options
	protected $_fax_phone_number = null;
	protected $_mobile_phone_number = null;
	protected $_office_phone_number = null;

	// User (Meta)
	protected $_username = null;
	protected $_role_id = null;

	// DTO Version
	protected $_userDTO = null;

	public function __construct( $registrationForm ) {
		die_r($registrationForm);
	}

	public function getRegistrationID() {
		return $this->_registration_id;
	}

	public function setRegistrationID( $registration_id ) {
		$this->_registration_id = $registration_id;
	}

	public function getFormAction() {
		return $this->_form_action;
	}

	public function setFormAction( $form_action ) {
		$this->_form_action = $_form_action;
	}

	public function getDisplayLocalization() {
		return $this->_displayLocalization;
	}

	public function setDisplayLocalization( $displayLocalization ) {
		$this->_displayLocalization = $displayLocalization;
	}

	public function getInputLocalization() {
		return $this->_inputLocalization;
	}

	public function setInputLocalization( $inputLocalization ) {
		$this->_inputLocalization = $inputLocalization;
	}

/*
protected $_address_id = null;
protected $_address_attention = null;
protected $_address_lines = null;
protected $_address_locality = null;
protected $_address_town = null;
protected $_address_region = null;
protected $_address_province = null;
protected $_address_postal_code = null;
protected $_address_country = null;
protected $_address_additional_info = null;
*/
	public function getAddressID() {
		return $this->_address_id;
	}

	public function setAddressID( $address_id ) {
		$this->_address_id = $address_id;
	}

	public function getRegistrationID() {
		return $this->_registration_id;
	}

	public function setRegistrationID( $registration_id ) {
		$this->_registration_id = $registration_id;
	}

	public function getRegistrationID() {
		return $this->_registration_id;
	}

	public function setRegistrationID( $registration_id ) {
		$this->_registration_id = $registration_id;
	}

	public function getRegistrationID() {
		return $this->_registration_id;
	}

	public function setRegistrationID( $registration_id ) {
		$this->_registration_id = $registration_id;
	}

	public function getRegistrationID() {
		return $this->_registration_id;
	}

	public function setRegistrationID( $registration_id ) {
		$this->_registration_id = $registration_id;
	}

	public function getRegistrationID() {
		return $this->_registration_id;
	}

	public function setRegistrationID( $registration_id ) {
		$this->_registration_id = $registration_id;
	}

	public function getRegistrationID() {
		return $this->_registration_id;
	}

	public function setRegistrationID( $registration_id ) {
		$this->_registration_id = $registration_id;
	}

	public function getRegistrationID() {
		return $this->_registration_id;
	}

	public function setRegistrationID( $registration_id ) {
		$this->_registration_id = $registration_id;
	}

	public function getRegistrationID() {
		return $this->_registration_id;
	}

	public function setRegistrationID( $registration_id ) {
		$this->_registration_id = $registration_id;
	}

	public function getRegistrationID() {
		return $this->_registration_id;
	}

	public function setRegistrationID( $registration_id ) {
		$this->_registration_id = $registration_id;
	}

	public function getRegistrationID() {
		return $this->_registration_id;
	}

	public function setRegistrationID( $registration_id ) {
		$this->_registration_id = $registration_id;
	}

	public function getRegistrationID() {
		return $this->_registration_id;
	}

	public function setRegistrationID( $registration_id ) {
		$this->_registration_id = $registration_id;
	}

	public function getRegistrationID() {
		return $this->_registration_id;
	}

	public function setRegistrationID( $registration_id ) {
		$this->_registration_id = $registration_id;
	}

	public function getRegistrationID() {
		return $this->_registration_id;
	}

	public function setRegistrationID( $registration_id ) {
		$this->_registration_id = $registration_id;
	}

	public function getRegistrationID() {
		return $this->_registration_id;
	}

	public function setRegistrationID( $registration_id ) {
		$this->_registration_id = $registration_id;
	}

	public function getRegistrationID() {
		return $this->_registration_id;
	}

	public function setRegistrationID( $registration_id ) {
		$this->_registration_id = $registration_id;
	}

	public function getRegistrationID() {
		return $this->_registration_id;
	}

	public function setRegistrationID( $registration_id ) {
		$this->_registration_id = $registration_id;
	}

	public function getRegistrationID() {
		return $this->_registration_id;
	}

	public function setRegistrationID( $registration_id ) {
		$this->_registration_id = $registration_id;
	}

	public function getRegistrationID() {
		return $this->_registration_id;
	}

	public function setRegistrationID( $registration_id ) {
		$this->_registration_id = $registration_id;
	}

	public function getRegistrationID() {
		return $this->_registration_id;
	}

	public function setRegistrationID( $registration_id ) {
		$this->_registration_id = $registration_id;
	}

	public function getRegistrationID() {
		return $this->_registration_id;
	}

	public function setRegistrationID( $registration_id ) {
		$this->_registration_id = $registration_id;
	}

	public function getRegistrationID() {
		return $this->_registration_id;
	}

	public function setRegistrationID( $registration_id ) {
		$this->_registration_id = $registration_id;
	}

	public function getRegistrationID() {
		return $this->_registration_id;
	}

	public function setRegistrationID( $registration_id ) {
		$this->_registration_id = $registration_id;
	}

	public function getRegistrationID() {
		return $this->_registration_id;
	}

	public function setRegistrationID( $registration_id ) {
		$this->_registration_id = $registration_id;
	}

	public function getRegistrationID() {
		return $this->_registration_id;
	}

	public function setRegistrationID( $registration_id ) {
		$this->_registration_id = $registration_id;
	}

	public function getRegistrationID() {
		return $this->_registration_id;
	}

	public function setRegistrationID( $registration_id ) {
		$this->_registration_id = $registration_id;
	}

	public function getRegistrationID() {
		return $this->_registration_id;
	}

	public function setRegistrationID( $registration_id ) {
		$this->_registration_id = $registration_id;
	}

	public function getRegistrationID() {
		return $this->_registration_id;
	}

	public function setRegistrationID( $registration_id ) {
		$this->_registration_id = $registration_id;
	}

	public function getRegistrationID() {
		return $this->_registration_id;
	}

	public function setRegistrationID( $registration_id ) {
		$this->_registration_id = $registration_id;
	}

	public function getRegistrationID() {
		return $this->_registration_id;
	}

	public function setRegistrationID( $registration_id ) {
		$this->_registration_id = $registration_id;
	}

	public function getRegistrationID() {
		return $this->_registration_id;
	}

	public function setRegistrationID( $registration_id ) {
		$this->_registration_id = $registration_id;
	}


}

