<?php
/**
 * FormAttributeSet Class File
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
 * @category Plugins
 * @package Forms
 * @subpackage FormAttributeSets
 * @version 1.0
 */

/**
 * FormAttributeSet
 *
 * $short_description
 *
 * $long_description
 *
 * @category Plugins
 * @package Forms
 * @subpackage FormAttributeSets
 */
class FormAttributeSet {

	protected $_formAttributeSetID = null;
	protected $_formAttributeSetData = null;

	/**
	 * @var array an array of FormAttributeSets
	 */
	protected $_formAttributeSets = array();

	protected $_formAttributeSetDAOConnectionName = null;
	protected $_formAttributeSetDAOFactory = null;
	protected $_formAttributeSetDAO = null;
	protected $_formAttributeSetDTO = null;

	protected $_formAttributeSetDefinition = null;

	protected $_formAttributeSetErrors = array();

	protected $_formFields = array();
	protected $_formFieldSets = array();

	protected $_renderedFormAttributeSet = null;
	protected $_renderedErrors = null;

	protected $_dataFormatter = null;
	protected $_displayFormatter = null;

	protected $_appendHTML = null;
	protected $_prependHTML = null;

	protected $_cssClasses = null;

	protected $_displayLocalized = false;
	protected $_displayLocalizer = null;

	protected $_formEncoding = null;

	protected $_inputLocalized = false;
	protected $_inputLocalizer = null;

	protected $_validated = false;
	protected $_validator = null;

	protected $_secure = false;

	public function __construct( $formAttributeSetID = null, $formAttributeSetData = null ) {
		$this->_formAttributeSetID = $formAttributeSetID;
		$this->_formAttributeSetData = $formAttributeSetData;
	}

	public function addFormField( $form_field_id, $formField ) {
		if ( !isset($this->_formFields[$form_field_id]) ) {
			$this->_formFields[$form_field_id] = $formField;
			// $this->_formAttributeSetData[$form_field_id] = $formField->getData();
		} else {
			throw new Exception( 'FormField with ID "' . $form_field_id . '" already exists' );
		}
	}

	public function issetFormField( $form_field_id ) {
		return isset($this->_formFields[$form_field_id]);
	}

	public function getFormField( $form_field_id ) {
		return $this->_formFields[$form_field_id];
	}

	public function getFormFieldData( $form_field_id ) {
		return $this->_formAttributeSetData[$form_field_id];
	}

	public function removeFormField( $form_field_id ) {
		unset($this->_formFields[$form_field_id]);
		unset($this->_formAttributeSetData[$form_field_id]);
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
		return $this->_formAttributeSetData[$form_field_set_id];
	}

	public function removeFormFieldSet( $form_field_set_id ) {
		unset($this->_formFieldSets[$form_field_set_id]);
		unset($this->_formAttributeSetData[$form_field_set_id]);
	}

	public function getData() {
		return $this->_formAttributeSetData;
	}
	
	public function setData( $formAttributeSetData ) {
		$this->_formAttributeSetData = $formAttributeSetData;
	}

	public function getErrors() {
		return $this->_formAttributeSetErrors;
	}

	public function getFields() {
		return $this->_formAttributeSetFields;
	}

	public function getFormFieldSets() {
		return $this->_formAttributeSetFieldSets;
	}

	public function getID() {
		return $this->_formAttributeSetID;
	}

	public function render() {
		$retVal = null;

		// TODO needs to be able to set action and method
		// TODO localize this whole thing
		$html = '<!-- FormAttributeSet: "' . $this->_formAttributeSetID . '" -->' . "\n";

		// This should be able to respect some overall order between fieldsets and fields
		foreach( $this->_formFieldSets as $formFieldSet ) {
			$html .= "\t" . '<!-- FormFieldSet: "' . $formFieldSet->getID() . '" -->' . "\n";

			if ( $formFieldSet->getLegend() !== null && trim( $formFieldSet->getLegend() ) !== '' ) {
				$html .= "\t" . '<fieldset id="fieldset-' . $formFieldSet->getID() . '-form-fieldset" class="' .
					implode( ' ', $formFieldSet->getCSSClasses() ) . '">' . "\n";
				$html .= "\t" . '<legend>' . $formFieldSet->getLegend() . '</legend>' . "\n";
			}

			foreach( $formFieldSet->getFormFields() as $formField ) {
				$formField->setVariablePrepend($this->getID() . '[formFieldSets][' . $formFieldSet->getID() . '][formFields]');
				$html .= $formField->render( true, true, true, false, "\t" );
			}

			if ( $formFieldSet->getLegend() !== null && trim( $formFieldSet->getLegend() ) !== '' ) {
				$html .= "\t" . '</fieldset>' . "\n";
			}
		}

		// This should be able to respect some overall order between fieldsets and fields
		foreach( $this->_formFields as $formField ) {
			$formField->setVariablePrepend($this->getID() . '[formFields]');
			$html .= $formField->render();
		}

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
	public function getDAOConnectionName() {
		return $this->_formAttributeSetDAOConnectionName;
	}

	public function setDAOConnectionName( $formAttributeSetDAOConnectionName ) {
		$this->_formAttributeSetDAOConnectionName = $formAttributeSetDAOConnectionName;
	}

	public function getDAOFactory() {
		return $this->_formAttributeSetDAOFactory;
	}

	public function setDAOFactory( $formAttributeSetDAOFactory ) {
		$this->_formAttributeSetDAOFactory = $formAttributeSetDAOFactory;
	}

	public function getDAO() {
		return $this->_formAttributeSetDAO;
	}

	public function setDAO( $formAttributeSetDAO ) {
		$this->_formAttributeSetDAO = $formAttributeSetDAO;
	}

	public function getDTO() {
		return $this->_formAttributeSetDTO;
	}

	public function setDTO( $formAttributeSetDTO ) {
		$this->_formAttributeSetDTO = $formAttributeSetDTO;
	}

	// Encoding
	public function getEncoding() {
		return $this->_formEncoding;
	}

	public function setEncoding( $formEncoding ) {
		$this->_formEncoding = $formEncoding;

		return $this;
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

