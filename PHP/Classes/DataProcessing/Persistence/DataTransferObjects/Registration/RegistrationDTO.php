<?php
/**
 * RegistrationDTO Class File
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
 * @category DataProcessing
 * @package Persistence
 * @subpackage DataTransferObjects
 * @version 1.0
 */

/**
 * RegistrationDTO
 *
 * $short_description
 *
 * $long_description
 *
 * @category DataProcessing
 * @package Persistence
 * @subpackage DataTransferObjects
 */
class RegistrationDTO extends DataTransferObject implements FormDTOInterface {

	/**
	 * @var integer Unique registration ID
	 */
	protected $_registration_id = null;

	/**
	 * Returns protected class member $_registration_id
	 *
	 * @return integer Unique registration ID
	 */
	public function getRegistrationID() {
		return $this->_registration_id;
	}

	/**
	 * Sets protected class member $_registration_id
	 *
	 * @param registration_id integer Unique registration ID
	 */
	public function setRegistrationID( $registration_id ) {
		$this->_registration_id = $registration_id;
	}

	/**
	 * @var mixed Form action of this registration
	 */
	protected $_form_action = null;

	/**
	 * Returns protected class member $_form_action
	 *
	 * @return mixed Form action of this registration
	 */
	public function getFormAction() {
		return $this->_form_action;
	}

	/**
	 * Sets protected class member $_form_action
	 *
	 * @param form_action mixed Form action of this registration
	 */
	public function setFormAction( $form_action ) {
		$this->_form_action = $form_action;
	}

	/**
	 * @var mixed Display localization of this registration form
	 */
	protected $_display_localization = null;

	/**
	 * Returns protected class member $_display_localization
	 *
	 * @return mixed Display localization of this registration form
	 */
	public function getDisplayLocalization() {
		return $this->_display_localization;
	}

	/**
	 * Sets protected class member $_display_localization
	 *
	 * @param display_localization mixed Display localization of this registration form
	 */
	public function setDisplayLocalization( $display_localization ) {
		$this->_display_localization = $display_localization;
	}

	/**
	 * @var mixed Input localization of this registration form
	 */
	protected $_input_localization = null;

	/**
	 * Returns protected class member $_input_localization
	 *
	 * @return mixed Input localization of this registration form
	 */
	public function getInputLocalization() {
		return $this->_input_localization;
	}

	/**
	 * Sets protected class member $_input_localization
	 *
	 * @param input_localization mixed Input localization of this registration form
	 */
	public function setInputLocalization( $input_localization ) {
		$this->_input_localization = $input_localization;
	}

	/**
	 * @var bool Whether the user accepted the TOS or not
	 */
	protected $_acceptsTOS = null;

	/**
	 * Returns protected class member $_acceptsTOS
	 *
	 * @return bool Whether the user accepted the TOS or not
	 */
	public function getAcceptsTOS() {
		return $this->_acceptsTOS;
	}

	/**
	 * Sets protected class member $_acceptsTOS
	 *
	 * @param acceptsTOS bool Whether the user accepted the TOS or not
	 */
	public function setAcceptsTOS( $acceptsTOS ) {
		$this->_acceptsTOS = $acceptsTOS;
	}

	/**
	 * @var string Unique address ID
	 */
	protected $_address_id = null;

	/**
	 * Returns protected class member $_address_id
	 *
	 * @return string Unique address ID
	 */
	public function getAddressID() {
		return $this->_address_id;
	}

	/**
	 * Sets protected class member $_address_id
	 *
	 * @param address_id string Unique address ID
	 */
	public function setAddressID( $address_id ) {
		$this->_address_id = $address_id;
	}

	/**
	 * @var string Attention of the address
	 */
	protected $_address_attention = null;

	/**
	 * Returns protected class member $_address_attention
	 *
	 * @return string Attention of the address
	 */
	public function getAddressAttention() {
		return $this->_address_attention;
	}

	/**
	 * Sets protected class member $_address_attention
	 *
	 * @param address_attention string Attention of the address
	 */
	public function setAddressAttention( $address_attention ) {
		$this->_address_attention = $address_attention;
	}

	/**
	 * @var array Lines of the address (first, variable)
	 */
	protected $_address_lines = null;

	/**
	 * Returns protected class member $_address_lines
	 *
	 * @return array Lines of the address (first, variable)
	 */
	public function getAddressLines() {
		return $this->_address_lines;
	}

	/**
	 * Sets protected class member $_address_lines
	 *
	 * @param address_lines array Lines of the address (first, variable)
	 */
	public function setAddressLines( $address_lines ) {
		$this->_address_lines = $address_lines;
	}

	/**
	 * @var mixed Locality of address
	 */
	protected $_address_locality = null;

	/**
	 * Returns protected class member $_address_locality
	 *
	 * @return mixed Locality of address
	 */
	public function getAddressLocality() {
		return $this->_address_locality;
	}

	/**
	 * Sets protected class member $_address_locality
	 *
	 * @param address_locality mixed Locality of address
	 */
	public function setAddressLocality( $address_locality ) {
		$this->_address_locality = $address_locality;
	}


	/**
	 * @var mixed Town of address
	 */
	protected $_address_town = null;

	/**
	 * Returns protected class member $_address_town
	 *
	 * @return mixed Town of address
	 */
	public function getAddressTown() {
		return $this->_address_town;
	}

	/**
	 * Sets protected class member $_address_town
	 *
	 * @param address_town mixed Town of address
	 */
	public function setAddressTown( $address_town ) {
		$this->_address_town = $address_town;
	}

	/**
	 * @var mixed Region of address
	 */
	protected $_address_region = null;

	/**
	 * Returns protected class member $_address_region
	 *
	 * @return mixed Region of address
	 */
	public function getAddressRegion() {
		return $this->_address_region;
	}

	/**
	 * Sets protected class member $_address_region
	 *
	 * @param address_region mixed Region of address
	 */
	public function setAddressRegion( $address_region ) {
		$this->_address_region = $address_region;
	}

	/**
	 * @var mixed Province of address
	 */
	protected $_address_province = null;

	/**
	 * Returns protected class member $_address_province
	 *
	 * @return mixed Province of address
	 */
	public function getAddressProvince() {
		return $this->_address_province;
	}

	/**
	 * Sets protected class member $_address_province
	 *
	 * @param address_province mixed Province of address
	 */
	public function setAddressProvince( $address_province ) {
		$this->_address_province = $address_province;
	}

	/**
	 * @var mixed Postal code of address
	 */
	protected $_address_postal_code = null;

	/**
	 * Returns protected class member $_address_postal_code
	 *
	 * @return mixed Postal code of address
	 */
	public function getAddressPostalCode() {
		return $this->_address_postal_code;
	}

	/**
	 * Sets protected class member $_address_postal_code
	 *
	 * @param address_postal_code mixed Postal code of address
	 */
	public function setAddressPostalCode( $address_postal_code ) {
		$this->_address_postal_code = $address_postal_code;
	}

	/**
	 * @var mixed Country of address
	 */
	protected $_address_country = null;

	/**
	 * Returns protected class member $_address_country
	 *
	 * @return mixed Country of address
	 */
	public function getAddressCountry() {
		return $this->_address_country;
	}

	/**
	 * Sets protected class member $_address_country
	 *
	 * @param address_country mixed Country of address
	 */
	public function setAddressCountry( $address_country ) {
		$this->_address_country = $address_country;
	}

	/**
	 * @var string Additional information for address
	 */
	protected $_address_additional_info = null;

	/**
	 * Returns protected class member $_address_additional_info
	 *
	 * @return string Additional information for address
	 */
	public function getAddressAdditionalInfo() {
		return $this->_address_additional_info;
	}

	/**
	 * Sets protected class member $_address_additional_info
	 *
	 * @param address_additional_info string Additional information for address
	 */
	public function setAddressAdditionalInfo( $address_additional_info ) {
		$this->_address_additional_info = $address_additional_info;
	}

	/**
	 * @var AddressDTO DTO representation of address
	 */
	protected $_addressDTO = null;

	/**
	 * Returns protected class member $_addressDTO
	 *
	 * @return AddressDTO DTO representation of address
	 */
	public function getAddressDTO() {
		return $this->_addressDTO;
	}

	/**
	 * Sets protected class member $_addressDTO
	 *
	 * @param addressDTO AddressDTO DTO representation of address
	 */
	public function setAddressDTO( $addressDTO ) {
		$this->_addressDTO = $addressDTO;
	}

	/**
	 * @var mixed Primary email address
	 */
	protected $_primary_email_address = null;

	/**
	 * Returns protected class member $_primary_email_address
	 *
	 * @return mixed Primary email address
	 */
	public function getPrimaryEmailAddress() {
		return $this->_primary_email_address;
	}

	/**
	 * Sets protected class member $_primary_email_address
	 *
	 * @param primary_email_address mixed Primary email address
	 */
	public function setPrimaryEmailAddress( $primary_email_address ) {
		$this->_primary_email_address = $primary_email_address;
	}

	/**
	 * @var mixed Secondary email address
	 */
	protected $_secondary_email_address = null;

	/**
	 * Returns protected class member $_secondary_email_address
	 *
	 * @return mixed Secondary email address
	 */
	public function getSecondaryEmailAddress() {
		return $this->_secondary_email_address;
	}

	/**
	 * Sets protected class member $_secondary_email_address
	 *
	 * @param secondary_email_address mixed Secondary email address
	 */
	public function setSecondaryEmailAddress( $secondary_email_address ) {
		$this->_secondary_email_address = $secondary_email_address;
	}

	/**
	 * @var EmailAddressDTO DTO representation of primary email address
	 */
	protected $_primaryEmailAddressDTO = null;

	/**
	 * Returns protected class member $_primaryEmailAddressDTO
	 *
	 * @return EmailAddressDTO DTO representation of primary email address
	 */
	public function getPrimaryEmailAddressDTO() {
		return $this->_primaryEmailAddressDTO;
	}

	/**
	 * Sets protected class member $_primaryEmailAddressDTO
	 *
	 * @param primaryEmailAddressDTO EmailAddressDTO DTO representation of primary email address
	 */
	public function setPrimaryEmailAddressDTO( $primaryEmailAddressDTO ) {
		$this->_primaryEmailAddressDTO = $primaryEmailAddressDTO;
	}

	/**
	 * @var EmailAddressDTO DTO representation of secondary email address
	 */
	protected $_secondaryEmailAddressDTO = null;

	/**
	 * Returns protected class member $_secondaryEmailAddressDTO
	 *
	 * @return EmailAddressDTO DTO representation of secondary email address
	 */
	public function getSecondaryEmailAddressDTO() {
		return $this->_secondaryEmailAddressDTO;
	}

	/**
	 * Sets protected class member $_secondaryEmailAddressDTO
	 *
	 * @param secondaryEmailAddressDTO EmailAddressDTO DTO representation of secondary email address
	 */
	public function setSecondaryEmailAddressDTO( $secondaryEmailAddressDTO ) {
		$this->_secondaryEmailAddressDTO = $secondaryEmailAddressDTO;
	}

	/**
	 * @var string First password field submitted
	 */
	protected $_password_field_one = null;

	/**
	 * Returns protected class member $_password_field_one
	 *
	 * @return string First password field submitted
	 */
	public function getPasswordFieldOne() {
		return $this->_password_field_one;
	}

	/**
	 * Sets protected class member $_password_field_one
	 *
	 * @param password_field_one string First password field submitted
	 */
	public function setPasswordFieldOne( $password_field_one ) {
		$this->_password_field_one = $password_field_one;
	}

	/**
	 * @var string Second password field submitted
	 */
	protected $_password_field_two = null;

	/**
	 * Returns protected class member $_password_field_two
	 *
	 * @return string Second password field submitted
	 */
	public function getPasswordFieldTwo() {
		return $this->_password_field_two;
	}

	/**
	 * Sets protected class member $_password_field_two
	 *
	 * @param password_field_two string Second password field submitted
	 */
	public function setPasswordFieldTwo( $password_field_two ) {
		$this->_password_field_two = $password_field_two;
	}

	/**
	 * @var string First name of person registering
	 */
	protected $_first_name = null;

	/**
	 * Returns protected class member $_first_name
	 *
	 * @return string First name of person registering
	 */
	public function getFirstName() {
		return $this->_first_name;
	}

	/**
	 * Sets protected class member $_first_name
	 *
	 * @param first_name string First name of person registering
	 */
	public function setFirstName( $first_name ) {
		$this->_first_name = $first_name;
	}

	/**
	 * @var string Middle name or initial of person registering
	 */
	protected $_middle_name_or_initial = null;

	/**
	 * Returns protected class member $_middle_name_or_initial
	 *
	 * @return string Middle name or initial of person registering
	 */
	public function getMiddleNameOrInitial() {
		return $this->_middle_name_or_initial;
	}

	/**
	 * Sets protected class member $_middle_name_or_initial
	 *
	 * @param middle_name_or_initial string Middle name or initial of person registering
	 */
	public function setMiddleNameOrInitial( $middle_name_or_initial ) {
		$this->_middle_name_or_initial = $middle_name_or_initial;
	}

	/**
	 * @var string Last name of person registering
	 */
	protected $_last_name = null;

	/**
	 * Returns protected class member $_last_name
	 *
	 * @return string Last name of person registering
	 */
	public function getLastName() {
		return $this->_last_name;
	}

	/**
	 * Sets protected class member $_last_name
	 *
	 * @param last_name string Last name of person registering
	 */
	public function setLastName( $last_name ) {
		$this->_last_name = $last_name;
	}

	/**
	 * @var mixed Birth month of person registering
	 */
	protected $_birth_month = null;

	/**
	 * Returns protected class member $_birth_month
	 *
	 * @return mixed Birth month of person registering
	 */
	public function getBirthMonth() {
		return $this->_birth_month;
	}

	/**
	 * Sets protected class member $_birth_month
	 *
	 * @param birth_month mixed Birth month of person registering
	 */
	public function setBirthMonth( $birth_month ) {
		$this->_birth_month = $birth_month;
	}

	/**
	 * @var mixed Birth date of person registering
	 */
	protected $_birth_day = null;

	/**
	 * Returns protected class member $_birth_day
	 *
	 * @return mixed Birth date of person registering
	 */
	public function getBirthDay() {
		return $this->_birth_day;
	}

	/**
	 * Sets protected class member $_birth_day
	 *
	 * @param birth_day mixed Birth date of person registering
	 */
	public function setBirthDay( $birth_day ) {
		$this->_birth_day = $birth_day;
	}

	/**
	 * @var mixed Birth year of person registering
	 */
	protected $_birth_year = null;

	/**
	 * Returns protected class member $_birth_year
	 *
	 * @return mixed Birth year of person registering
	 */
	public function getBirthYear() {
		return $this->_birth_year;
	}

	/**
	 * Sets protected class member $_birth_year
	 *
	 * @param birth_year mixed Birth year of person registering
	 */
	public function setBirthYear( $birth_year ) {
		$this->_birth_year = $birth_year;
	}

	/**
	 * @var mixed Gender of person registering
	 */
	protected $_gender = null;

	/**
	 * Returns protected class member $_gender
	 *
	 * @return mixed Gender of person registering
	 */
	public function getGender() {
		return $this->_gender;
	}

	/**
	 * Sets protected class member $_gender
	 *
	 * @param gender mixed Gender of person registering
	 */
	public function setGender( $gender ) {
		$this->_gender = $gender;
	}

	/**
	 * @var PersonDTO DTO representation of person registering
	 */
	protected $_personDTO = null;

	/**
	 * Returns protected class member $_personDTO
	 *
	 * @return PersonDTO DTO representation of person registering
	 */
	public function getPersonDTO() {
		return $this->_personDTO;
	}

	/**
	 * Sets protected class member $_personDTO
	 *
	 * @param personDTO PersonDTO DTO representation of person registering
	 */
	public function setPersonDTO( $personDTO ) {
		$this->_personDTO = $personDTO;
	}

	/**
	 * @var mixed Primary phone number of person registering
	 */
	protected $_primary_phone_number = null;

	/**
	 * Returns protected class member $_primary_phone_number
	 *
	 * @return mixed Primary phone number of person registering
	 */
	public function getPrimaryPhoneNumber() {
		return $this->_primary_phone_number;
	}

	/**
	 * Sets protected class member $_primary_phone_number
	 *
	 * @param primary_phone_number mixed Primary phone number of person registering
	 */
	public function setPrimaryPhoneNumber( $primary_phone_number ) {
		$this->_primary_phone_number = $primary_phone_number;
	}

	/**
	 * @var mixed Secondary phone number of person registering
	 */
	protected $_secondary_phone_number = null;

	/**
	 * Returns protected class member $_secondary_phone_number
	 *
	 * @return mixed Secondary phone number of person registering
	 */
	public function getSecondaryPhoneNumber() {
		return $this->_secondary_phone_number;
	}

	/**
	 * Sets protected class member $_secondary_phone_number
	 *
	 * @param secondary_phone_number mixed Secondary phone number of person registering
	 */
	public function setSecondaryPhoneNumber( $secondary_phone_number ) {
		$this->_secondary_phone_number = $secondary_phone_number;
	}

	/**
	 * @var PhoneNumberDTO DTO representation of primary phone number
	 */
	protected $_primaryPhoneNumberDTO = null;

	/**
	 * Returns protected class member $_primaryPhoneNumberDTO
	 *
	 * @return PhoneNumberDTO DTO representation of primary phone number
	 */
	public function getPrimaryPhoneNumberDTO() {
		return $this->_primaryPhoneNumberDTO;
	}

	/**
	 * Sets protected class member $_primaryPhoneNumberDTO
	 *
	 * @param primaryPhoneNumberDTO PhoneNumberDTO DTO representation of primary phone number
	 */
	public function setPrimaryPhoneNumberDTO( $primaryPhoneNumberDTO ) {
		$this->_primaryPhoneNumberDTO = $primaryPhoneNumberDTO;
	}

	/**
	 * @var PhoneNumberDTO DTO representation of secondary phone number
	 */
	protected $_secondaryPhoneNumberDTO = null;

	/**
	 * Returns protected class member $_secondaryPhoneNumberDTO
	 *
	 * @return PhoneNumberDTO DTO representation of secondary phone number
	 */
	public function getSecondaryPhoneNumberDTO() {
		return $this->_secondaryPhoneNumberDTO;
	}

	/**
	 * Sets protected class member $_secondaryPhoneNumberDTO
	 *
	 * @param secondaryPhoneNumberDTO PhoneNumberDTO DTO representation of secondary phone number
	 */
	public function setSecondaryPhoneNumberDTO( $secondaryPhoneNumberDTO ) {
		$this->_secondaryPhoneNumberDTO = $secondaryPhoneNumberDTO;
	}

	/**
	 * @var string Username of the person registering
	 */
	protected $_username = null;

	/**
	 * Returns protected class member $_username
	 *
	 * @return string Username of the person registering
	 */
	public function getUsername() {
		return $this->_username;
	}

	/**
	 * Sets protected class member $_username
	 *
	 * @param username string Username of the person registering
	 */
	public function setUsername( $username ) {
		$this->_username = $username;
	}

	/**
	 * @var mixed Role ID of the user registering
	 */
	protected $_role_id = null;

	/**
	 * Returns protected class member $_role_id
	 *
	 * @return mixed Role ID of the user registering
	 */
	public function getRoleID() {
		return $this->_role_id;
	}

	/**
	 * Sets protected class member $_role_id
	 *
	 * @param role_id mixed Role ID of the user registering
	 */
	public function setRoleID( $role_id ) {
		$this->_role_id = $role_id;
	}

	/**
	 * @var UserDTO DTO representation of the user registering
	 */
	protected $_userDTO = null;

	/**
	 * Returns protected class member $_userDTO
	 *
	 * @return UserDTO DTO representation of the user registering
	 */
	public function getUserDTO() {
		return $this->_userDTO;
	}

	/**
	 * Sets protected class member $_userDTO
	 *
	 * @param userDTO UserDTO DTO representation of the user registering
	 */
	public function setUserDTO( $userDTO ) {
		$this->_userDTO = $userDTO;
	}

	public function __construct() {}

	public function initWithForm( Form $form ) {
		/* Fields Not Touched (But Available) */
		// _registration_id
		// 	    _form_action
		// 	    _display_localization
		// 	    _input_localization
		// 	    _addressDTO
		// 	    _secondary_email_address
		// 	    _primaryEmailAddressDTO
		// 	    _secondaryEmailAddressDTO
		// 	    _personDTO
		// 	    _secondary_phone_number
		// 	    _primaryPhoneNumberDTO
		// 	    _secondaryPhoneNumberDTO
		// 	    _role_id
		// 	    _userDTO:protected] =>
		$addressFormFieldSet = $form->getFormFieldSet('address');
		$this->_address_id = $addressFormFieldSet->getFormField('address_id')->getValue();
		$this->_address_attention = $addressFormFieldSet->getFormField('address_attention')->getValue();

		$this->_address_lines = array();

		foreach( $addressFormFieldSet->getFormField('address_lines')->getFormFields() as $addressLine ) {
			$this->_address_lines[$addressLine->getID()] = $addressLine->getValue();
		}

		$this->_address_locality = $addressFormFieldSet->getFormField('address_locality')->getValue();
		$this->_address_town = $addressFormFieldSet->getFormField('address_town')->getValue();
		$this->_address_region = $addressFormFieldSet->getFormField('address_region')->getValue();
		$this->_address_province = $addressFormFieldSet->getFormField('address_province')->getValue();
		$this->_address_postal_code = $addressFormFieldSet->getFormField('address_postal_code')->getValue();
		$this->_address_country = $addressFormFieldSet->getFormField('address_country')->getValue();
		$this->_address_additional_info = $addressFormFieldSet->getFormField('address_additional_info')->getValue();

		$nameFormFieldSet = $form->getFormFieldSet('name');
		$this->_first_name = $nameFormFieldSet->getFormField('first_name')->getValue();

		if ( $nameFormFieldSet->issetFormField('middle_name_or_initial') ) {
			$this->_middle_name_or_initial = $nameFormFieldSet->getFormField('middle_name_or_initial')->getValue();
		} else {
			$this->_middle_name_or_initial = null;
		}

		$this->_last_name = $nameFormFieldSet->getFormField('last_name')->getValue();

		$birthdateFormFieldSet = $form->getFormFieldSet('birthdate');
		$this->_birth_month = $birthdateFormFieldSet->getFormField('birthdate_month')->getValue();
		$this->_birth_day = $birthdateFormFieldSet->getFormField('birthdate_day')->getValue();
		$this->_birth_year = $birthdateFormFieldSet->getFormField('birthdate_year')->getValue();

		$passwordFormFieldSet = $form->getFormFieldSet('password');
		$this->_password_field_one = $passwordFormFieldSet->getFormField('password_one')->getValue();
		$this->_password_field_two = $passwordFormFieldSet->getFormField('password_two')->getValue();

		$this->_gender = $form->getFormField('gender')->getValue();
		$this->_primary_email_address = $form->getFormField('email')->getValue();
		$this->_username = $form->getFormField('username')->getValue();
		$this->_primary_phone_number = $form->getFormField('phone_number')->getValue();

		if ( $form->issetFormField('acceptsTOS') && $form->getFormField('acceptsTOS')->getValue() === 'acceptsTOS') {
			$this->_acceptsTOS = true;
		} else {
			$this->_acceptsTOS = false;
		}
	}

	public function __destruct() {}

}

