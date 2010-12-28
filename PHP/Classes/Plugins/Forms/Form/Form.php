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
	protected $_formLegend = null;
	protected $_formLegendToken = null;

	protected $_formDAO = null;
	protected $_formDTO = null;

	protected $_formDefinition = null;

	protected $_formErrors = array();

	protected $_formFields = array();
	protected $_formFieldSets = array();

	protected $_renderedForm = null;
	protected $_renderedErrors = null;

	protected $_dataFormatter = null;
	protected $_displayFormatter = null;

	protected $_appendHTML = null;
	protected $_prependHTML = null;

	protected $_cssClasses = null;

	protected $_displayLocalized = false;
	protected $_displayLocalizer = null;

	protected $_inputLocalized = false;
	protected $_inputLocalizer = null;

	protected $_validated = false;
	protected $_validator = null;

	protected $_secure = false;

	public function __construct( $formID = null, $formData = null ) {
		$this->_formID = $formID;
		$this->_formData = $formData;
	}

	public function addFormField( $form_field_id, $formField ) {
		if ( !isset($this->_formFields[$form_field_id]) ) {
			$this->_formFields[$form_field_id] = $formField;
			// $this->_formData[$form_field_id] = $formField->getData();
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
			$this->_formFieldSets[$form_field_set_id] = $formFieldSet;
		} else {
			throw new Exception( 'FormFieldSet with ID "' . $form_field_set_id . '" already exists' );
		}
	}

	public function getFormFieldSet( $form_field_set_id ) {
		return $this->_formFieldSets[$form_field_set_id];
	}

	public function getFormFieldSetData( $form_field_set_id ) {
		return $this->_formData[$form_field_set_id];
	}

	public function removeFormFieldSet( $form_field_set_id ) {
		unset($this->_formFieldSets[$form_field_set_id]);
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

		// TODO needs to be able to set action and method
		// TODO localize this whole thing
		$html = '<!-- Form: "' . $this->_formID . '" -->' . "\n";
		$html .= '<form method="POST">' . "\n";

		if ( $this->_formLegend !== null && trim( $this->_formLegend ) !== '' ) {
			$html .= '<fieldset id="' . $this->_formID . '-form-fieldset" class="' .
				implode( ' ', $this->_cssClasses ) . '">';
			$html .= '<legend>' . $this->_formLegend . '</legend>';
		}

		// This should be able to respect some overall order between fieldsets and fields
		foreach( $this->_formFieldSets as $formFieldSet ) {
			$html .= "\t" . '<!-- FieldSet: "' . $formFieldSet->getID() . '" -->' . "\n";

			if ( $formFieldSet->getLegend() !== null && trim( $formFieldSet->getLegend() ) !== '' ) {
				$html .= "\t" . '<fieldset id="fieldset-' . $formFieldSet->getID() . '-form-fieldset" class="' .
					implode( ' ', $formFieldSet->getCSSClasses() ) . '">' . "\n";
				$html .= "\t" . '<legend>' . $formFieldSet->getLegend() . '</legend>' . "\n";
			}

			foreach( $formFieldSet->getFormFields() as $formField ) {
				$html .= $formField->render( true, true, false, "\t" );
			}

			if ( $formFieldSet->getLegend() !== null && trim( $formFieldSet->getLegend() ) !== '' ) {
				$html .= "\t" . '</fieldset>' . "\n";
			}
		}

		// This should be able to respect some overall order between fieldsets and fields
		foreach( $this->_formFields as $formField ) {
			$html .= $formField->render();
		}

		if ( $this->_formLegend !== null && trim( $this->_formLegend ) !== '' ) {
			$html .= '</fieldset>' . "\n";
		}

		$html .= '</form>' . "\n";

		$retVal = $html;

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

	// CSS
	public function addCSSClass( $class_name ) {
		$this->_cssClasses[$class_name] = $class_name;
	}

	public function removeCSSClass( $class_name ) {
		unset($this->_cssClasses[$class_name]);
	}

	public function getCSSClasses() {
		return $this->_cssClasses;
	}

	public function getCSSClassesString() {
		return implode( ' ', $this->_cssClasses );
	}

	public function setCSSClasses( $cssClasses ) {
		if ( is_string( $cssClasses ) ) {
			$classes = explode( ' ', $cssClasses );

			foreach($classes as $class) {
				$this->_cssClasses[$class] = $class;
			}
		} else if ( is_array( $cssClasses ) ) {
			$this->_cssClasses = array();

			foreach($cssClasses as $class) {
				$this->_cssClasses[$class] = $class;
			}
		}
	}


	// DAO/DTO
	public function setFormDAO( $formDAO ) {
		$this->_formDAO = $formDAO;
	}

	public function getFormDAO() {
		return $this->_formDAO;
	}

	public function setFormDTO( $formDTO ) {
		$this->_formDTO = $formDTO;
	}

	public function getFormDTO() {
		return $this->_formDTO;
	}

	// Formatters
	public function getDataFormatter() {
		return $this->_dataFormatter;
	}

	public function setDataFormatter( $dataFormatter ) {
		$this->_dataFormatter = $dataFormatter;
	}

	public function getDisplayFormatter() {
		return $this->_displayFormatter;
	}

	public function setDisplayFormatter( $displayFormatter ) {
		$this->_displayFormatter = $displayFormatter;
	}

	// HTML
	public function getAppendHTML() {
		return $this->_appendHTML;
	}

	public function setAppendHTML( $appendHTML ) {
		$this->_appendHTML = $appendHTML;
	}

	public function getPrependHTML() {
		return $this->_prependHTML;
	}

	public function setPrependHTML( $prependHTML ) {
		$this->_prependHTML = $prependHTML;
	}

	// Legend
	public function getFormLegend() {
		return $this->_formLegend;
	}

	public function setFormLegend( $formLegend ) {
		$this->_formLegend = $formLegend;
	}

	public function getFormLegendToken() {
		return $this->_formLegendToken;
	}

	public function setFormLegendToken( $formLegendToken ) {
		$this->_formLegendToken = $formLegendToken;
	}

	// Localization
	public function isDisplayLocalized() {
		return $this->_displayLocalized;
	}

	public function setDisplayLocalized( $isDisplayLocalized ) {
		$this->_displayLocalized = $isDisplayLocalized;
	}

	public function getDisplayLocalizer() {
		return $this->_displayLocalizer;
	}

	public function setDisplayLocalizer( $displayLocalizer ) {
		$this->_displayLocalizer = $displayLocalizer;
	}

	public function isInputLocalized() {
		return $this->_inputLocalized;
	}

	public function setInputLocalized( $isInputLocalized ) {
		$this->_inputLocalized = $isInputLocalized;
	}

	public function getInputLocalizer() {
		return $this->_inputLocalizer;
	}

	public function setInputLocalizer( $inputLocalizer ) {
		$this->_inputLocalizer = $inputLocalizer;
	}

	// Validator
	public function getValidator() {
		return $this->_validator;
	}

	public function setValidator( $validator ) {
		$this->_validator = $validator;
	}

	public function __destruct() {
		
	}

}

