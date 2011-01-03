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

	const RENDER_MODE_EDIT 			= 0x01;
	const RENDER_MODE_EDIT_DISABLED = 0x02;
	const RENDER_MODE_EDIT_HIDDEN 	= 0x03;

	const RENDER_MODE_READ			= 0x04;
	const RENDER_MODE_READ_HIDDEN 	= 0x05;

	const RENDER_MODE_NONE			= 0x06;

	protected $_formFieldID = null;
	protected $_formFieldData = null;
	// protected $_formFieldLabel = null;
	protected $_formFieldType = null;
	protected $_formFieldValue = null;
	protected $_formFieldValueSeeder = null;
	protected $_formFieldValueSeederName = null;

	protected $_appendHTML = '';

	protected $_cssClasses = null;

	protected $_displayLabel = null;
	protected $_displayLabelToken = null;

	protected $_errorMessage = null;
	protected $_errorMessageToken = null;

	protected $_errorHandler = null;

	protected $_formFieldDefinition = null;

	protected $_formFieldElementOrder = array();

	protected $_formFieldErrors = array();
	protected $_formFieldChildErrors = array();

	protected $_formFieldChildren = array();
	protected $_formFieldChildData = array();

	protected $_formFieldHTMLAttributes = array();

	protected $_prependHTML = '';

	protected $_renderedFormField = null;
	protected $_renderedErrors = null;

	protected $_renderMode = self::RENDER_MODE_EDIT;

	protected $_variablePrepend = null;
	protected $_variableAppend = null;

	public function __construct( $formFieldID = null, $formFieldData = null, $displayLabel = null, $displayLabelToken = null ) {
		$this->_formFieldID = $formFieldID;
		$this->_formFieldData = $formFieldData;
		$this->_displayLabel = $displayLabel;
		$this->_displayLabelToken = $displayLabelToken;
	}

	public function addFormField( $child_form_field_id, $formField ) {
		if ( !isset($this->_formFieldChildren[$child_form_field_id]) ) {
			$this->_formFieldChildren[$child_form_field_id] = $formField;
			$elementCount = count($this->_formFieldElementOrder);
			$this->_formFieldElementOrder[$child_form_field_id] = $elementCount + 1;
			// $this->_formFieldChildData[$child_form_field_id] = $this->getData();
		} else {
			throw new Exception( 'FormField child with ID "' . $child_form_field_id . '" already exists' );
		}
	}

	public function getFormField( $child_form_field_id ) {
		return $this->_formFieldChildren[$child_form_field_id];
	}

	public function getFormFields() {
		return $this->_formFieldChildren;
	}

	public function getFormFieldData( $child_form_field_id ) {
		return $this->_formFieldChildData[$child_form_field_id];
	}

	public function setFormField( $child_form_field_id, $formField ) {
		$this->_formFieldChildren[$child_form_field_id] = $formField;
		// $this->_formFieldChildData[$child_form_field_id] = $this->getData();
	}

	public function removeFormField( $child_form_field_id ) {
		unset($this->_formFieldChildren[$child_form_field_id]);
		// unset($this->_formFieldChildData[$child_form_field_id]);
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

	public function render( $render_labels = true, $render_children = true, $render_child_labels = false, $prepend = '', $append = '' ) {
		$retVal = '';

		if ( $this->_renderMode !== self::RENDER_MODE_NONE ) {
			if ( trim($this->_prependHTML) !== '' ) {
				$retVal = $prepend . "\t" . $this->_prependHTML . "\n";
			}

			// TODO localize this.  Also, we ignore checkbox labeling to have it wrap the input field
			if ( $render_labels && $this->getDisplayLabel() && trim($this->getDisplayLabel()) !== '' && $this->getFormFieldType() !== 'checkbox' ) {
				$retVal .= $prepend . "\t" . '<label id="formfield-' . $this->getID() . '-form-formfield-label" ' .
					'for="formfield-' . $this->getID() . '-form-formfield">' . $this->getDisplayLabel() . '</label>' . "\n";
			}

			switch ( $this->getFormFieldType() ) {
				case 'checkbox' :
					// We do special label rendering to get the checkbox on the left of the label text
					$retVal .= $prepend . "\t" . '<label id="formfield-' . $this->getID() . '-form-formfield-label" ' .
						'for="formfield-' . $this->getID() . '-form-formfield">' . "\n" . $prepend . "\t\t" . '<input id="formfield-' .
						$this->getID() . '-form-formfield" name="' . $this->getVariablePrepend() . '[' . $this->getID() .
						']" class="' . $this->getCSSClassesString() . '" type="checkbox" value="' . $this->getFormFieldValue() .
						'" />' . $this->getDisplayLabel() . "\n" . $prepend . "\t" . '</label>' . "\n";

					break;
				case 'container' :
					$retVal .= $prepend . "\t" . '<!-- FormField Container: "' . $this->getID() . '" -->' . "\n";

					foreach( $this->getFormFields() as $formField ) {
						$formField->setVariablePrepend($this->getVariablePrepend() . '[' . $this->getID() . '][formFields]');
						$retVal .= $formField->render( true, true, false, "\t" . $prepend );
					}

					break;
				case 'hidden' :
					$retVal .= $prepend . "\t" . '<input id="formfield-' . $this->getID() . '-form-formfield" name="' . 
						$this->getVariablePrepend() . '[' . $this->getID() . ']" class="' . $this->getCSSClassesString() .
						'" type="hidden" value="' . $this->getFormFieldValue() . '" />' . "\n";
					break;
				case 'password' :
					$retVal .= $prepend . "\t" . '<input id="formfield-' . $this->getID() . '-form-formfield" name="' .
						$this->getVariablePrepend() . '[' . $this->getID() . ']" class="' . $this->getCSSClassesString() .
						'" type="password" value="' . $this->getFormFieldValue() . '" />' . "\n";
					break;
				case 'select' :
					$valueSeeder = $this->getFormFieldValueSeeder();

					$retVal .= $prepend . "\t" . '<select id="formfield-' . $this->getID() . '-form-formfield" name="' .
						$this->getVariablePrepend() . '[' . $this->getID() . ']" class="' . $this->getCSSClassesString() . '">' . "\n";

					foreach( $valueSeeder->getValues() as $key => $value ) {
						$retVal .= $prepend . "\t" . "\t" . '<option value="' . $key . '">' . $value . '</option>' . "\n";
					}

					$retVal .= $prepend . "\t" . '</select>' . "\n";

					break;
				case 'submit' :
					$retVal .= $prepend . "\t" . '<input id="formfield-' . $this->getID() . '-form-formfield" name="' .
						$this->getVariablePrepend() . '[' . $this->getID() . ']" class="' . $this->getCSSClassesString() .
						'" type="submit" value="' . $this->getFormFieldValue() . '" />' . "\n";
					break;
				case 'text' :
					$retVal .= $prepend . "\t" . '<input id="formfield-' . $this->getID() . '-form-formfield" name="' .
						$this->getVariablePrepend() . '[' . $this->getID() . ']" class="' . $this->getCSSClassesString() . '" type="text" value="' .
						$this->getFormFieldValue() . '" />' . "\n";
					break;
				case 'textarea' :
					$retVal .= $prepend . "\t" . '<textarea id="formfield-' . $this->getID() . '-form-formfield" name="' .
						$this->getVariablePrepend() . '[' . $this->getID() . ']" class="' . $this->getCSSClassesString() . '">' .
						$this->getFormFieldValue() . '</textarea>' . "\n";
					break;
				default :
					eGlooLogger::writeLog( eGlooLogger::EMERGENCY, 'Form: Invalid input type "' . $this->getFormFieldType() .
						'" specified in FormField "' . $this->getID() );
					break;
			}

			if ( trim($this->_appendHTML) !== '' ) {
				$retVal .= $prepend . "\t" . $this->_appendHTML . "\n";
			}

		}

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

	// Append HTML
	public function getAppendHTML() {
		return $this->_appendHTML;
	}

	public function setAppendHTML( $appendHTML ) {
		$this->_appendHTML = $appendHTML;
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

	// Display Label
	public function getDisplayLabel() {
		return $this->_displayLabel;
	}

	public function setDisplayLabel( $displayLabel ) {
		$this->_displayLabel = $displayLabel;
	}

	// Display Label Token
	public function getDisplayLabelToken() {
		return $this->_displayLabelToken;
	}

	public function setDisplayLabelToken( $displayLabelToken ) {
		$this->_displayLabelToken = $displayLabelToken;
	}

	// Element Ordering
	public function swapElements( $first_element_id, $second_element_id ) {
		$first_index = $this->_formFieldElementOrder[$first_element_id];
		$second_index = $this->_formFieldElementOrder[$second_element_id];

		$this->_formFieldElementOrder[$first_element_id] = $second_index;
		$this->_formFieldElementOrder[$second_element_id] = $first_index;

		asort($this->_formFieldElementOrder);
	}

	public function insertElementBefore( $first_element_id, $second_element_id ) {
		// $first_index = $this->_formFieldElementOrder[$first_element_id];
		// $second_index = $this->_formFieldElementOrder[$second_element_id];
		// 
		// $this->_formFieldElementOrder[$first_element_id] = $second_index;
		// $this->_formFieldElementOrder[$second_element_id] = $first_index;
		// 
		// asort($this->_formFieldElementOrder);
	}

	public function insertElementAfter( $first_element_id, $second_element_id ) {
		// $first_index = $this->_formFieldElementOrder[$first_element_id];
		// $second_index = $this->_formFieldElementOrder[$second_element_id];
		// 
		// $this->_formFieldElementOrder[$first_element_id] = $second_index;
		// $this->_formFieldElementOrder[$second_element_id] = $first_index;
		// 
		// asort($this->_formFieldElementOrder);
	}

	public function setElementOrder( $element_order ) {
		$count = 1;

		foreach( $element_order as $element ) {
			if ( isset($this->_formFieldElementOrder[$element]) ) {
				$this->_formFieldElementOrder[$element] = $count;
				$count++;
			}
		}

		asort( $this->_formFieldElementOrder );
	}

	// Error Message
	public function getErrorMessage() {
		return $this->_errorMessage;
	}

	public function setErrorMessage( $errorMessage ) {
		$this->_errorMessage = $errorMessage;
	}

	// Error Message Token
	public function getErrorMessageToken() {
		return $this->_errorMessageToken;
	}

	public function setErrorMessageToken( $errorMessageToken ) {
		$this->_errorMessageToken = $errorMessageToken;
	}

	// Error Handler
	public function getErrorHandler() {
		return $this->_errorHandler;
	}

	public function setErrorHandler( $errorHandler ) {
		$this->_errorHandler = $errorHandler;
	}

	// FormField Type
	public function getFormFieldType() {
		return $this->_formFieldType;
	}

	public function setFormFieldType( $formFieldType ) {
		$this->_formFieldType = $formFieldType;
	}

	// FormField Value
	public function getFormFieldValue() {
		return $this->_formFieldValue;
	}

	public function setFormFieldValue( $formFieldValue ) {
		$this->_formFieldValue = $formFieldValue;
	}

	// FormField Value Seeder
	public function getFormFieldValueSeeder() {
		if ( !$this->_formFieldValueSeeder ) {
			$formFieldValueSeederName = $this->_formFieldValueSeederName;

			$this->_formFieldValueSeeder = $formFieldValueSeederName::getInstance();
		}

		return $this->_formFieldValueSeeder;
	}

	public function setFormFieldValueSeeder( $formFieldValueSeeder ) {
		$this->_formFieldValueSeeder = $formFieldValueSeeder;
	}

	// FormField Value Seeder
	public function getFormFieldValueSeederName() {
		return $this->_formFieldValueSeederName;
	}

	public function setFormFieldValueSeederName( $formFieldValueSeederName ) {
		$this->_formFieldValueSeederName = $formFieldValueSeederName;
	}

	// FormField HTML Attributes
	public function getFormFieldHTMLAttribute( $attribute_key ) {
		return $this->_formFieldHTMLAttributes[$attribute_key];
	}

	public function setFormFieldHTMLAttribute( $attribute_key, $attribute_value ) {
		$this->_formFieldHTMLAttributes[$attribute_key] = $attribute_value;
	}

	public function getFormFieldHTMLAttributes() {
		return $this->_formFieldHTMLAttributes;
	}

	public function setFormFieldHTMLAttributes( $attributes ) {
		$this->_formFieldHTMLAttributes = $attributes;
	}

	// Prepend HTML
	public function getPrependHTML() {
		return $this->_prependHTML;
	}

	public function setPrependHTML( $prependHTML ) {
		$this->_prependHTML = $prependHTML;
	}

	// Render Mode
	public function getRenderMode() {
		return $this->_renderMode;
	}

	public function setRenderMode( $renderMode ) {
		$this->_renderMode = $renderMode;
	}

	// Variable Container
	public function getVariablePrepend() {
		return $this->_variablePrepend;
	}

	public function setVariablePrepend( $variablePrepend ) {
		$this->_variablePrepend = $variablePrepend;
	}

	public function getVariableAppend() {
		return $this->_variablePrepend;
	}

	public function setVariableAppend( $variableAppend ) {
		$this->_variableAppend = $variableAppend;
	}

	public function __destruct() {
		
	}

}

