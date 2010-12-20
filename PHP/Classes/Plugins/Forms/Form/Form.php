<?php
/**
 * Form Class File
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
 * Form
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class Form {

	protected $_formID = null;
	protected $_formData = null;

	protected $_formDefinition = null;

	protected $_formErrors = array();

	protected $_formFields = array();
	protected $_formFieldSets = array();

	protected $_renderedForm = null;
	protected $_renderedErrors = null;

	public function __construct( $formID = null, $formData = null ) {
		$this->_formID = $formID;
		$this->_formData = $formData;
	}

	public function addFormField( $form_field_id, $formField ) {
		if ( !isset($this->_formFields[$form_field_id]) ) {
			$this->_formFields[$form_field_id] = $formField;
			$this->_formData[$form_field_id] = $formField->getData();
		} else {
			throw new Exception( 'FormField with ID "' . $form_field_id . '" already exists' );
		}
	}

	public function getFormField( $form_field_id ) {
		return $this->_formFields[$form_field_id];
	}

	public function getFormFieldData( $form_field_id ) {
		return $this->_formData[$form_field_id];
	}

	public function removeFormField( $form_field_id ) {
		unset($this->_formFields[$form_field_id]);
		unset($this->_formData[$form_field_id]);
	}

	public function addFormFieldSet( $form_field_set_id, $formFieldSet ) {
		if ( !isset($this->_formFields[$form_field_set_id]) ) {
			$this->_formFields[$form_field_set_id] = $formFieldSet;
			$this->_formData[$form_field_set_id] = $formField->getData();
		} else {
			throw new Exception( 'FormFieldSet with ID "' . $form_field_set_id . '" already exists' );
		}
	}

	public function getFormFieldSet( $form_field_set_id ) {
		return $this->_formFieldSet[$form_field_set_id];
	}

	public function getFormFieldSetData( $form_field_set_id ) {
		return $this->_formData[$form_field_set_id];
	}

	public function removeFormFieldSet( $form_field_set_id ) {
		unset($this->_formFieldSet[$form_field_set_id]);
		unset($this->_formData[$form_field_set_id]);
	}

	public function getFormData() {
		return $this->_formData;
	}

	public function setFormData( $formData ) {
		$this->_formData = $formData;
	}

	public function getFormErrors() {
		return $this->_formErrors;
	}

	public function getFormFields() {
		return $this->_formFields;
	}

	public function getFormFieldSets() {
		return $this->_formFieldSets;
	}

	public function getFormID() {
		return $this->_formID;
	}

	public function render() {
		$retVal = null;

		

		return $retVal;
	}

	public function renderErrors() {
		$retVal = null;

		

		return $retVal;
	}

	public function getErrorsByFieldID( $field_id ) {
		return $this->_formErrors[$field_id];
	}

	public function setErrorsByFieldID( $field_id, $error_value ) {
		$this->_formErrors[$field_id] = $error_value;
	}

	public function __destruct() {
		
	}

}

