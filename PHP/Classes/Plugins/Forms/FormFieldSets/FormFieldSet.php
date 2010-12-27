<?php
/**
 * FormFieldSet Class File
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
 * FormFieldSet
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class FormFieldSet {

	protected $_formFieldSetID = null;
	protected $_formFieldSetLegend = null;
	protected $_formFieldSetLegendToken = null;

	protected $_formFieldSetDefinition = null;

	protected $_formFieldChildErrors = array();

	protected $_formFieldChildren = array();
	protected $_formFieldChildData = array();

	protected $_renderedFormFieldSet = null;
	protected $_renderedErrors = null;

	public function __construct( $formFieldSetID = null, $formFieldSetLegend = null ) {
		$this->_formFieldSetID = $formFieldSetID;
		$this->_formFieldSetLegend = $formFieldSetLegend;
	}

	public function addFormField( $child_form_field_id, $formField ) {
		if ( !isset($this->_formFieldChildren[$child_form_field_id]) ) {
			$this->_formFieldChildren[$child_form_field_id] = $formField;
			// $this->_formFieldChildData[$child_form_field_id] = $formField->getData();
		} else {
			throw new Exception( 'FormField child with ID "' . $child_form_field_id . '" already exists' );
		}
	}

	public function getFormField( $child_form_field_id ) {
		return $this->_formFieldChildren[$child_form_field_id];
	}
	
	public function getFormFieldData( $child_form_field_id ) {
		return $this->_formFieldChildData[$child_form_field_id];
	}

	public function setFormField( $child_form_field_id, $formField ) {
		$this->_formFieldChildren[$child_form_field_id] = $formField;
		// $this->_formFieldChildData[$child_form_field_id] = $formField->getData();
	}

	public function removeFormField( $child_form_field_id ) {
		unset($this->_formFieldChildren[$child_form_field_id]);
		// unset($this->_formFieldChildData[$child_form_field_id]);
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
		return $this->_formFieldSetID;
	}

	public function render( $render_legend = true, $render_children = true, $render_child_labels = true, $render_frameset = true ) {
		$retVal = null;

		

		return $retVal;
	}

	public function renderErrors() {
		$retVal = null;

		

		return $retVal;
	}

	public function getErrorsByChildID( $child_field_id ) {
		return $this->_formFieldChildErrors[$child_field_id];
	}

	public function setErrorsByChildID( $child_field_id, $error_value ) {
		$this->_formFieldChildErrors[$child_field_id] = $error_value;
	}

	// Legend
	public function getLegend() {
		return $this->_formFieldSetLegend;
	}

	public function setLegend( $formFieldSetLegend ) {
		$this->_formFieldSetLegend = $formFieldSetLegend;
	}

	public function getLegendToken() {
		return $this->_formFieldSetLegendToken;
	}

	public function setLegendToken( $formFieldSetLegendToken ) {
		$this->_formFieldSetLegendToken = $formFieldSetLegendToken;
	}

	public function __destruct() {
		
	}

}

