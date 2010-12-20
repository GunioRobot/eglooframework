<?php
/**
 * FormField Class File
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
 * FormField
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class FormField {

	protected $_formFieldID = null;
	protected $_formFieldData = null;
	protected $_formFieldLabel = null;

	protected $_formFieldDefinition = null;

	protected $_formFieldErrors = array();
	protected $_formFieldChildErrors = array();

	protected $_formFieldChildren = array();
	protected $_formFieldChildData = array();

	protected $_renderedFormField = null;
	protected $_renderedErrors = null;

	public function __construct( $formFieldID = null, $formFieldData = null, $formFieldLabel = null ) {
		$this->_formFieldID = $formFieldID;
		$this->_formFieldData = $formFieldData;
		$this->_formFieldLabel = $formFieldLabel;
	}

	public function addChildFormField( $child_form_field_id, $formField ) {
		if ( !isset($this->_formFieldChildren[$child_form_field_id]) ) {
			$this->_formFieldChildren[$child_form_field_id] = $formField;
			$this->_formFieldChildData[$child_form_field_id] = $formField->getData();
		} else {
			throw new Exception( 'FormField child with ID "' . $child_form_field_id . '" already exists' );
		}
	}

	public function getChildFormField( $child_form_field_id ) {
		return $this->_formFieldChildren[$child_form_field_id];
	}
	
	public function getChildFormFieldData( $child_form_field_id ) {
		return $this->_formFieldChildData[$child_form_field_id];
	}

	public function setChildFormField( $child_form_field_id, $formField ) {
		$this->_formFieldChildren[$child_form_field_id] = $formField;
		$this->_formFieldChildData[$child_form_field_id] = $formField->getData();
	}

	public function removeChildFormField( $child_form_field_id ) {
		unset($this->_formFieldChildren[$child_form_field_id]);
		unset($this->_formFieldChildData[$child_form_field_id]);
	}

	public function getData() {
		return $this->_formFieldData;
	}

	// TODO think about this
	public function getDisplayFormattedData() {
		// return $this->_formFieldData;
	}

	// TODO think about this
	public function getDataFormattedData() {
		// return $this->_formFieldData;
	}

	public function setData( $formFieldData ) {
		$this->_formFieldData = $formFieldData;
	}

	public function getLabel() {
		return $this->_formFieldLabel;
	}

	public function setLabel( $formFieldLabel ) {
		$this->_formFieldLabel = $formFieldLabel;
	}

	public function getErrors( $include_child_errors = true ) {
		// TODO return child errors, too
		return $this->_formFieldErrors;
	}

	public function getChildErrors() {
		return $this->_formFieldChildErrors;
	}

	public function getChildren() {
		return $this->_formFieldChildren;
	}

	public function hasChildren() {
		$retVal = false;
		
		if ( !empty($this->_formFieldChildren) ) {
			$retVal = true;
		}

		return $retVal;
	}

	public function getID() {
		return $this->_formFieldID;
	}

	public function render( $render_labels = true, $render_children = true, $render_child_labels = false ) {
		$retVal = null;

		

		return $retVal;
	}

	public function renderErrors( $render_child_errors = true ) {
		$retVal = null;

		

		return $retVal;
	}

	public function getErrorsByChildID( $child_field_id ) {
		return $this->_formFieldChildErrors[$child_field_id];
	}

	public function setErrorsByChildID( $child_field_id, $error_value ) {
		$this->_formFieldChildErrors[$child_field_id] = $error_value;
	}

	public function __destruct() {
		
	}

}

