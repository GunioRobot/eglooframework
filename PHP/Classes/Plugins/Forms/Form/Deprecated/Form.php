<?php
/**
 * Form Class File
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
 * @subpackage Form
 * @version 1.0
 */

/**
 * Form
 *
 * $short_description
 *
 * $long_description
 *
 * @category Plugins
 * @package Forms
 * @subpackage Form
 */
class Form {

	protected $_formID = null;
	protected $_formData = null;
	protected $_formLegend = null;
	protected $_formLegendToken = null;

	/**
	 * @var array an array of FormAttributeSets
	 */
	protected $_formAttributeSets = array();

	protected $_formDAOConnectionName = null;
	protected $_formDAOFactory = null;
	protected $_formDAO = null;
	protected $_formDTO = null;

	protected $_formDefinition = null;

	protected $_formElementOrder = array();

	protected $_formErrors = array();

	protected $_formFields = array();
	protected $_formFieldSets = array();

	protected $_formAction = null;
	protected $_formEncoding = null;
	protected $_formMethod = 'post';

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

	protected $_isCRUDable = false;

	protected $_createTriggers = array();
	protected $_readTriggers = array();
	protected $_updateTriggers = array();
	protected $_destroyTriggers = array();

	protected $_crudResult = null;

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
			$elementCount = count($this->_formElementOrder);
			$this->_formElementOrder[$form_field_id] = $elementCount + 1;
			// $this->_formData[$form_field_id] = $formField->getData();
		} else {
			throw new Exception( 'FormField with ID "' . $form_field_id . '" already exists' );
		}

		return $this;
	}

	public function issetFormField( $form_field_id ) {
		return isset($this->_formFields[$form_field_id]);
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

		return $this;
	}

	public function addFormFieldSet( $form_field_set_id, $formFieldSet ) {
		if ( !isset($this->_formFields[$form_field_set_id]) ) {
			$this->_formFieldSets[$form_field_set_id] = $formFieldSet;
			$elementCount = count($this->_formElementOrder);
			$this->_formElementOrder[$form_field_set_id] = $elementCount + 1;
		} else {
			throw new Exception( 'FormFieldSet with ID "' . $form_field_set_id . '" already exists' );
		}

		return $this;
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

		return $this;
	}

	public function getFormData() {
		return $this->_formData;
	}

	public function setFormData( $formData ) {
		$this->_formData = $formData;

		return $this;
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

	public function render( $render_errors = true ) {
		$retVal = null;

		// TODO needs to be able to set action and method
		// TODO localize this whole thing
		$html = '<!-- Form: "' . $this->_formID . '" -->' . "\n";
		$html .= '<form id="' . $this->_formID . '" method="' . $this->_formMethod . '"';
		
		if ( isset($this->_formAction) ) {
			$html .= ' action="' . $this->_formAction . '"';
		}

		if ( isset($this->_formEncoding) ) {
			$html .= ' enctype="' . $this->_formEncoding . '"';
		}

		$html .= '>' . "\n";

		if ( $this->_formLegend !== null && trim( $this->_formLegend ) !== '' ) {
			$html .= '<fieldset id="' . $this->_formID . '-form-fieldset" class="' .
				implode( ' ', $this->_cssClasses ) . '">';
			$html .= '<legend>' . $this->_formLegend . '</legend>';
		}

		foreach($this->_formElementOrder as $element_id => $element_position) {
			if ( isset( $this->_formFieldSets[$element_id] ) ) {
				$formFieldSet = $this->_formFieldSets[$element_id];

				$variablePrepend = $this->getFormID() . '[formFieldSets][' . $formFieldSet->getID() . '][formFields]';
				$formFieldSet->setVariablePrepend($variablePrepend);

				$html .= $formFieldSet->render( $render_errors );

			} else if ( isset( $this->_formFields[$element_id] ) ) {
				$formField = $this->_formFields[$element_id];

				$formField->setVariablePrepend($this->getFormID() . '[formFields]');
				$html .= $formField->render( $render_errors );
			}
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

		return $this;
	}

	// Action
	public function getAction() {
		return $this->_formAction;
	}

	public function setAction( $action ) {
		$this->_formAction = $action;

		return $this;
	}

	// CRUD
	public function isCRUDable() {
		return $this->_isCRUDable;
	}

	public function setIsCRUDable( $isCRUDable ) {
		$this->_isCRUDable = $isCRUDable;

		return $this;
	}

	public function setCRUDCreateTriggers( $createTriggers ) {
		$this->_createTriggers = $createTriggers;

		return $this;
	}

	public function getCRUDCreateTriggers() {
		return $this->_createTriggers;
	}

	public function setCRUDReadTriggers( $readTriggers ) {
		$this->_readTriggers = $readTriggers;

		return $this;
	}

	public function getCRUDReadTriggers() {
		return $this->_readTriggers;
	}

	public function setCRUDUpdateTriggers( $updateTriggers ) {
		$this->_updateTriggers = $updateTriggers;

		return $this;
	}

	public function getCRUDUpdateTriggers() {
		return $this->_updateTriggers;
	}

	public function setCRUDDestroyTriggers( $destroyTriggers ) {
		$this->_destroyTriggers = $destroyTriggers;

		return $this;
	}

	public function getCRUDDestroyTriggers() {
		return $this->_destroyTriggers;
	}

	public function getCRUDResult() {
		return $this->_crudResult;
	}

	public function setCRUDResult( $crudResult ) {
		$this->_crudResult = $crudResult;
	}

	// CSS
	public function addCSSClass( $class_name ) {
		$this->_cssClasses[$class_name] = $class_name;

		return $this;
	}

	public function removeCSSClass( $class_name ) {
		unset($this->_cssClasses[$class_name]);

		return $this;
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

		return $this;
	}

	// DAO/DTO
	public function getFormDAOConnectionName() {
		return $this->_formDAOConnectionName;
	}

	public function setFormDAOConnectionName( $formDAOConnectionName ) {
		$this->_formDAOConnectionName = $formDAOConnectionName;

		return $this;
	}

	public function getFormDAOFactory() {
		return $this->_formDAOFactory;
	}

	public function setFormDAOFactory( $formDAOFactory ) {
		$this->_formDAOFactory = $formDAOFactory;

		return $this;
	}

	public function getFormDAO() {
		return $this->_formDAO;
	}

	public function setFormDAO( $formDAO ) {
		$this->_formDAO = $formDAO;

		return $this;
	}

	public function getFormDTO() {
		return $this->_formDTO;
	}

	public function setFormDTO( $formDTO ) {
		$this->_formDTO = $formDTO;

		return $this;
	}

	// Element Ordering
	public function swapElements( $first_element_id, $second_element_id ) {
		$first_index = $this->_formElementOrder[$first_element_id];
		$second_index = $this->_formElementOrder[$second_element_id];

		$this->_formElementOrder[$first_element_id] = $second_index;
		$this->_formElementOrder[$second_element_id] = $first_index;

		asort($this->_formElementOrder);

		return $this;
	}

	public function insertElementBefore( $first_element_id, $second_element_id ) {
		// $first_index = $this->_formElementOrder[$first_element_id];
		// $second_index = $this->_formElementOrder[$second_element_id];
		// 
		// $this->_formElementOrder[$first_element_id] = $second_index;
		// $this->_formElementOrder[$second_element_id] = $first_index;
		// 
		// asort($this->_formElementOrder);

		return $this;
	}

	public function insertElementAfter( $first_element_id, $second_element_id ) {
		// $first_index = $this->_formElementOrder[$first_element_id];
		// $second_index = $this->_formElementOrder[$second_element_id];
		// 
		// $this->_formElementOrder[$first_element_id] = $second_index;
		// $this->_formElementOrder[$second_element_id] = $first_index;
		// 
		// asort($this->_formElementOrder);

		return $this;
	}

	public function setElementOrder( $element_order ) {
		$count = 1;

		foreach( $element_order as $element ) {
			if ( isset($this->_formElementOrder[$element]) ) {
				$this->_formElementOrder[$element] = $count;
				$count++;
			}
		}

		asort( $this->_formElementOrder );

		return $this;
	}

	/**
	 * Returns protected class member $_formAttributeSets
	 *
	 * @return array an array of FormAttributeSets
	 */
	public function getFormAttributeSets() {
		return $this->_formAttributeSets;
	}

	/**
	 * Sets protected class member $_formAttributeSets
	 *
	 * @param formAttributeSets array an array of FormAttributeSets
	 */
	public function setFormAttributeSets( $formAttributeSets ) {
		$this->_formAttributeSets = $formAttributeSets;

		return $this;
	}

	/**
	 * Returns value for key in protected class member $_formAttributeSets
	 *
	 * @param $attribute_set_name string the name of the FormAttributeSet to return
	 *
	 * @return array an array of FormAttributeSets
	 */
	public function getFormAttributeSet( $attribute_set_name ) {
		return $this->_formAttributeSets[ $attribute_set_name ];
	}

	/**
	 * Returns protected class member $_formAttributeSets
	 *
	 * @param $attribute_set_name string the name of the FormAttributeSet to set
	 * @param $form_attribute_set FormAttributeSet the FormAttributeSet to set
	 *
	 * @return array an array of FormAttributeSets
	 */
	public function setFormAttributeSet( $attribute_set_name, $form_attribute_set ) {
		return $this->_formAttributeSets[ $attribute_set_name ] = $form_attribute_set;
	}

	// Encoding Type
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

		return $this;
	}

	public function getDisplayFormatter() {
		return $this->_displayFormatter;
	}

	public function setDisplayFormatter( $displayFormatter ) {
		$this->_displayFormatter = $displayFormatter;

		return $this;
	}

	// HTML
	public function getAppendHTML() {
		return $this->_appendHTML;
	}

	public function setAppendHTML( $appendHTML ) {
		$this->_appendHTML = $appendHTML;

		return $this;
	}

	public function getPrependHTML() {
		return $this->_prependHTML;
	}

	public function setPrependHTML( $prependHTML ) {
		$this->_prependHTML = $prependHTML;

		return $this;
	}

	// Legend
	public function getFormLegend() {
		return $this->_formLegend;
	}

	public function setFormLegend( $formLegend ) {
		$this->_formLegend = $formLegend;

		return $this;
	}

	public function getFormLegendToken() {
		return $this->_formLegendToken;
	}

	public function setFormLegendToken( $formLegendToken ) {
		$this->_formLegendToken = $formLegendToken;

		return $this;
	}

	// Localization
	public function isDisplayLocalized() {
		return $this->_displayLocalized;
	}

	public function setDisplayLocalized( $isDisplayLocalized ) {
		$this->_displayLocalized = $isDisplayLocalized;

		return $this;
	}

	public function getDisplayLocalizer() {
		return $this->_displayLocalizer;
	}

	public function setDisplayLocalizer( $displayLocalizer ) {
		$this->_displayLocalizer = $displayLocalizer;

		return $this;
	}

	public function isInputLocalized() {
		return $this->_inputLocalized;
	}

	public function setInputLocalized( $isInputLocalized ) {
		$this->_inputLocalized = $isInputLocalized;

		return $this;
	}

	public function getInputLocalizer() {
		return $this->_inputLocalizer;
	}

	public function setInputLocalizer( $inputLocalizer ) {
		$this->_inputLocalizer = $inputLocalizer;

		return $this;
	}

	// Method
	public function getMethod() {
		return $this->_formMethod;
	}

	public function setMethod( $method ) {
		$this->_formMethod = $method;

		return $this;
	}

	// Validator
	public function getValidator() {
		return $this->_validator;
	}

	public function setValidator( $validator ) {
		$this->_validator = $validator;

		return $this;
	}

	public function __destruct() {
		
	}

}

