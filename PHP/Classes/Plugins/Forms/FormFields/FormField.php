<?php
/**
 * FormField Class File
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
 * @subpackage FormFields
 * @version 1.0
 */

/**
 * FormField
 *
 * $short_description
 *
 * $long_description
 *
 * @category Plugins
 * @package Forms
 * @subpackage FormFields
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
	protected $_formFieldDefaultValue = null;
	protected $_formFieldValue = null;
	protected $_formFieldValueSeeder = null;
	protected $_formFieldValueSeederName = null;

	protected $_appendHTML = '';
	protected $_prependHTML = '';

	protected $_labelAppendHTML = '';
	protected $_labelPrependHTML = '';

	protected $_inputAppendHTML = '';
	protected $_inputPrependHTML = '';

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

	protected $_hasError = false;

	protected $_renderedFormField = null;
	protected $_renderedErrors = null;

	protected $_renderMode = self::RENDER_MODE_EDIT;

	protected $_isRequired = false;
	protected $requiredFieldMarker = '&#9733;';

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

		return $this;
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

		return $this;
	}

	public function removeFormField( $child_form_field_id ) {
		unset($this->_formFieldChildren[$child_form_field_id]);
		// unset($this->_formFieldChildData[$child_form_field_id]);

		return $this;
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

		return $this;
	}

	public function getError( $error_id ) {
		return $this->_formFieldErrors[$error_id];
	}

	public function setError( $error_id, $error_message, $error_token = null ) {
		$this->_formFieldErrors[$error_id] =
			array( 'default_message' => $error_message, 'localization_token' => $error_token );

		return $this;
	}

	public function issetError( $error_id ) {
		return isset( $this->_formFieldErrors[$error_id] );
	}

	public function unsetError( $error_id ) {
		unset( $this->_formFieldErrors[$error_id] );
	}

	public function getErrors( $include_child_errors = true ) {
		// TODO return child errors, too
		return $this->_formFieldErrors;
	}

	public function setErrors( $formfield_errors ) {
		// TODO return child errors, too
		return $this->_formFieldErrors = $formfield_errors;
	}

	public function hasError() {
		return $this->_hasError;
	}

	public function setHasError( $error_state ) {
		$this->_hasError = $error_state;

		return $this;
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

	public function render( $render_errors = true, $render_labels = true, $render_children = true, $render_child_labels = false, $prepend = '', $append = '' ) {
		$retVal = '';

		if ( $this->_renderMode !== self::RENDER_MODE_NONE ) {
			if ( trim($this->_prependHTML) !== '' ) {
				$retVal = $prepend . "\t" . $this->_prependHTML . "\n";
			}
 
			// TODO localize this.  Also, we ignore checkbox labeling to have it wrap the input field
			if ( $render_labels && $this->getDisplayLabel() && trim($this->getDisplayLabel()) !== '' && $this->getFormFieldType() !== 'checkbox' ) {
				$retVal .= $prepend . "\t" . $this->getLabelPrependHTML() . '<label id="formfield-' . $this->getID() . '-form-formfield-label" ' .
					'for="formfield-' . $this->getID() . '-form-formfield">';

				if ( $this->_isRequired ) {
					$retVal .= '<span id="formfield-' . $this->getID() . '-form-formfield-label-required-marker" ' .
						'class="form-formfield-required-marker">' . $this->requiredFieldMarker . '</span> ';
				}

				$retVal .= $this->getDisplayLabel() . '</label>' . $this->getLabelAppendHTML() . "\n";
			}

			switch ( $this->getFormFieldType() ) {
				case 'checkbox' :
					// We do special label rendering to get the checkbox on the left of the label text
					$retVal .= $prepend . "\t" . $this->getLabelPrependHTML() . '<label id="formfield-' . $this->getID() . '-form-formfield-label" ' .
						'for="formfield-' . $this->getID() . '-form-formfield">' . "\n" . $prepend . "\t\t" . '<input id="formfield-' .
						$this->getID() . '-form-formfield" name="' . $this->getVariablePrepend() . '[' . $this->getID() .
						']" class="' . $this->getCSSClassesString() . '" type="checkbox" value="' . $this->getDefaultValue() . '" ';

						if ( $this->getDefaultValue() === $this->getValue() ) {
							$retVal .= 'checked';
						}

						$retVal .= ' />';
					if ( $this->_isRequired ) {
						$retVal .= '<span id="formfield-' . $this->getID() . '-form-formfield-label-required-marker" ' .
							'class="form-formfield-required-marker"> ' . $this->requiredFieldMarker . '</span> ';
					}

					$retVal .= $this->getDisplayLabel() . "\n" . $prepend . "\t" . '</label>' . $this->getLabelAppendHTML() . "\n";
					break;
				case 'container' :
					$retVal .= $prepend . "\t" . '<!-- FormField Container: "' . $this->getID() . '" -->' . "\n";

					foreach( $this->getFormFields() as $formField ) {
						$formField->setVariablePrepend($this->getVariablePrepend() . '[' . $this->getID() . '][formFields]');
						$retVal .= $formField->render( true, true, true, false, "\t" . $prepend );
					}

					break;
				case 'file' :
					$retVal .= $prepend . "\t" . $this->getInputPrependHTML() . '<input id="formfield-' . $this->getID() . '-form-formfield" name="' .
						$this->getVariablePrepend() . '[' . $this->getID() . ']" class="' . $this->getCSSClassesString() . '" type="file" ';

						foreach( $this->_formFieldHTMLAttributes as $htmlAttributeName => $htmlAttributeValue ) {
							$retVal .= $htmlAttributeName . '="' . $htmlAttributeValue . '" ';
						}

						$retVal .= '/>' . $this->getInputAppendHTML() . "\n";
					break;
				case 'hidden' :
					$retVal .= $prepend . "\t" . '<input id="formfield-' . $this->getID() . '-form-formfield" name="' . 
						$this->getVariablePrepend() . '[' . $this->getID() . ']" class="' . $this->getCSSClassesString() .
						'" type="hidden" value="' . $this->getValue() . '" />' . "\n";
					break;
				case 'max_file_size' :
					$retVal .= $prepend . "\t" . '<input id="formfield-' . $this->getID() . '-form-formfield" name="MAX_FILE_SIZE" ' . 
						'class="' . $this->getCSSClassesString() .
						'" type="hidden" value="' . $this->getValue() . '" />' . "\n";
					break;
				case 'password' :
					$retVal .= $prepend . "\t" . $this->getInputPrependHTML() . '<input id="formfield-' . $this->getID() . '-form-formfield" name="' .
						$this->getVariablePrepend() . '[' . $this->getID() . ']" class="' . $this->getCSSClassesString() .
						'" type="password" value="' . $this->getValue() . '" ';

					foreach( $this->_formFieldHTMLAttributes as $htmlAttributeName => $htmlAttributeValue ) {
						$retVal .= $htmlAttributeName . '="' . $htmlAttributeValue . '" ';
					}

					$retVal .= '/>' . $this->getInputAppendHTML() . "\n";

					break;
				case 'radio' :
					$valueSeeder = $this->getValueSeeder();

					foreach( $valueSeeder->getValues() as $key => $value ) {
						$retVal .= $prepend . "\t\t" .  $this->getInputPrependHTML() . '<input id="formfield-' . $this->getID() .
							'-form-formfield" name="' . $this->getVariablePrepend() . '[' . $this->getID() . ']" type="radio" class="' .
							$this->getCSSClassesString() . '" value="' . $key . '"';

						if ( $key == $this->_formFieldValue) {
							$retVal .= ' checked';
						}

						$retVal .= '>' . $value . '</input>' . $this->getInputAppendHTML() . "\n";
					}

					break;
				case 'select' :
					$valueSeeder = $this->getValueSeeder();

					$retVal .= $prepend . "\t" . $this->getInputPrependHTML() . '<select id="formfield-' . $this->getID() . '-form-formfield" name="' .
						$this->getVariablePrepend() . '[' . $this->getID() . ']" class="' . $this->getCSSClassesString() . '">' . "\n";

					foreach( $valueSeeder->getValues() as $key => $value ) {
						$retVal .= $prepend . "\t" . "\t" . '<option value="' . $key . '"';

						if ( $key == $this->_formFieldValue) {
							$retVal .= ' selected="true"';
						}

						$retVal .= '>' . $value . '</option>' . "\n";
					}

					$retVal .= $prepend . "\t" . '</select>' . $this->getInputAppendHTML() . "\n";

					break;
				case 'submit' :
					$retVal .= $prepend . "\t" . $this->getInputPrependHTML() . '<input id="formfield-' . $this->getID() . '-form-formfield" name="' .
						$this->getVariablePrepend() . '[' . $this->getID() . ']" class="' . $this->getCSSClassesString() .
						'" type="submit" value="' . $this->getValue() . '" />' . $this->getInputAppendHTML() . "\n";
					break;
				case 'text' :
					$retVal .= $prepend . "\t" . $this->getInputPrependHTML() . '<input id="formfield-' . $this->getID() . '-form-formfield" name="' .
						$this->getVariablePrepend() . '[' . $this->getID() . ']" class="' . $this->getCSSClassesString() . '" type="text" value="' .
						$this->getValue() . '" ';

						foreach( $this->_formFieldHTMLAttributes as $htmlAttributeName => $htmlAttributeValue ) {
							$retVal .= $htmlAttributeName . '="' . $htmlAttributeValue . '" ';
						}

						$retVal .= '/>' . $this->getInputAppendHTML() . "\n";
					break;
				case 'textarea' :
					$retVal .= $prepend . "\t" . '<textarea id="formfield-' . $this->getID() . '-form-formfield" name="' .
						$this->getVariablePrepend() . '[' . $this->getID() . ']" class="' . $this->getCSSClassesString() . '" ';

					foreach( $this->_formFieldHTMLAttributes as $htmlAttributeName => $htmlAttributeValue ) {
						$retVal .= $htmlAttributeName . '="' . $htmlAttributeValue . '" ';
					}

					$retVal .= '>' . $this->getValue() . '</textarea>' . "\n";
					break;
				default :
					eGlooLogger::writeLog( eGlooLogger::EMERGENCY, 'Form: Invalid input type "' . $this->getFormFieldType() .
						'" specified in FormField "' . $this->getID() );
					break;
			}

			if ( trim($this->_appendHTML) !== '' ) {
				$retVal .= $prepend . "\t" . $this->_appendHTML . "\n";
			}

			if ( $render_errors && $this->_hasError && trim($this->_errorMessage) !== '' ) {
				$retVal .= $prepend . "\t" . '<div id="formfield-' . $this->getID() .
					'-form-formfield-errors" class="form-formfield-errors">' . "\n";
				$retVal .= $prepend . "\t\t" . $this->_errorMessage . "\n";

				if ( !empty( $this->_formFieldErrors ) ) {
					// TODO Localize this
					$retVal .= $prepend . "\t\t" . '<ul id="formfield-' . $this->getID() .
						'-form-formfield-errors-list" class="form-formfield-errors-list">' . "\n";

					foreach( $this->_formFieldErrors as $formFieldError ) {
						$retVal .= $prepend . "\t\t\t" . '<li id="formfield-' . $this->getID() .
							'-form-formfield-errors-list-item" class="form-formfield-errors-list-item">';

						// TODO localize the messages here
						$retVal .= $formFieldError['default_message'];

						$retVal .= '</li>' . "\n";
					}

					$retVal .= $prepend . "\t\t" . '</ul>' . "\n";
				}

				$retVal .= $prepend . "\t" . '</div>' . "\n";
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

		return $this;
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

	// Display Label
	public function getDisplayLabel() {
		return $this->_displayLabel;
	}

	public function setDisplayLabel( $displayLabel ) {
		$this->_displayLabel = $displayLabel;

		return $this;
	}

	// Display Label Token
	public function getDisplayLabelToken() {
		return $this->_displayLabelToken;
	}

	public function setDisplayLabelToken( $displayLabelToken ) {
		$this->_displayLabelToken = $displayLabelToken;

		return $this;
	}

	// Element Ordering
	public function swapElements( $first_element_id, $second_element_id ) {
		$first_index = $this->_formFieldElementOrder[$first_element_id];
		$second_index = $this->_formFieldElementOrder[$second_element_id];

		$this->_formFieldElementOrder[$first_element_id] = $second_index;
		$this->_formFieldElementOrder[$second_element_id] = $first_index;

		asort($this->_formFieldElementOrder);

		return $this;
	}

	public function insertElementBefore( $first_element_id, $second_element_id ) {
		// $first_index = $this->_formFieldElementOrder[$first_element_id];
		// $second_index = $this->_formFieldElementOrder[$second_element_id];
		// 
		// $this->_formFieldElementOrder[$first_element_id] = $second_index;
		// $this->_formFieldElementOrder[$second_element_id] = $first_index;
		// 
		// asort($this->_formFieldElementOrder);

		return $this;
	}

	public function insertElementAfter( $first_element_id, $second_element_id ) {
		// $first_index = $this->_formFieldElementOrder[$first_element_id];
		// $second_index = $this->_formFieldElementOrder[$second_element_id];
		// 
		// $this->_formFieldElementOrder[$first_element_id] = $second_index;
		// $this->_formFieldElementOrder[$second_element_id] = $first_index;
		// 
		// asort($this->_formFieldElementOrder);

		return $this;
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

		return $this;
	}

	// Error Message
	public function getErrorMessage() {
		return $this->_errorMessage;
	}

	public function setErrorMessage( $errorMessage ) {
		$this->_errorMessage = $errorMessage;

		return $this;
	}

	// Error Message Token
	public function getErrorMessageToken() {
		return $this->_errorMessageToken;
	}

	public function setErrorMessageToken( $errorMessageToken ) {
		$this->_errorMessageToken = $errorMessageToken;

		return $this;
	}

	// Error Handler
	public function getErrorHandler() {
		return $this->_errorHandler;
	}

	public function setErrorHandler( $errorHandler ) {
		$this->_errorHandler = $errorHandler;

		return $this;
	}

	// FormField Type
	public function getFormFieldType() {
		return $this->_formFieldType;
	}

	public function setFormFieldType( $formFieldType ) {
		$this->_formFieldType = $formFieldType;

		return $this;
	}

	// FormField Default Value
	public function getDefaultValue() {
		return $this->_formFieldDefaultValue;
	}

	public function setDefaultValue( $formFieldDefaultValue ) {
		$this->_formFieldDefaultValue = $formFieldDefaultValue;

		return $this;
	}

	// FormField Value
	public function getValue() {
		return $this->_formFieldValue;
	}

	public function setValue( $formFieldValue ) {
		$this->_formFieldValue = $formFieldValue;

		return $this;
	}

	// FormField Value Seeder
	public function getValueSeeder() {
		if ( !$this->_formFieldValueSeeder ) {
			$formFieldValueSeederName = $this->_formFieldValueSeederName;

			$this->_formFieldValueSeeder = $formFieldValueSeederName::getInstance();
		}

		return $this->_formFieldValueSeeder;
	}

	public function setValueSeeder( $formFieldValueSeeder ) {
		$this->_formFieldValueSeeder = $formFieldValueSeeder;

		return $this;
	}

	// FormField Value Seeder
	public function getValueSeederName() {
		return $this->_formFieldValueSeederName;
	}

	public function setValueSeederName( $formFieldValueSeederName ) {
		$this->_formFieldValueSeederName = $formFieldValueSeederName;

		return $this;
	}

	// FormField HTML Attributes
	public function getFormFieldHTMLAttribute( $attribute_key ) {
		return $this->_formFieldHTMLAttributes[$attribute_key];
	}

	public function setFormFieldHTMLAttribute( $attribute_key, $attribute_value ) {
		$this->_formFieldHTMLAttributes[$attribute_key] = $attribute_value;

		return $this;
	}

	public function getFormFieldHTMLAttributes() {
		return $this->_formFieldHTMLAttributes;
	}

	public function setFormFieldHTMLAttributes( $attributes ) {
		$this->_formFieldHTMLAttributes = $attributes;

		return $this;
	}

	// HTML Append
	public function getAppendHTML() {
		return $this->_appendHTML;
	}

	public function setAppendHTML( $appendHTML ) {
		$this->_appendHTML = $appendHTML;

		return $this;
	}

	//  HTML Prepend
	public function getPrependHTML() {
		return $this->_prependHTML;
	}

	public function setPrependHTML( $prependHTML ) {
		$this->_prependHTML = $prependHTML;

		return $this;
	}

	// Label HTML Append
	public function getLabelAppendHTML() {
		return $this->_labelAppendHTML;
	}

	public function setLabelAppendHTML( $labelAppendHTML ) {
		$this->_labelAppendHTML = $labelAppendHTML;

		return $this;
	}

	// Label HTML Prepend
	public function getLabelPrependHTML() {
		return $this->_labelPrependHTML;
	}

	public function setLabelPrependHTML( $labelPrependHTML ) {
		$this->_labelPrependHTML = $labelPrependHTML;

		return $this;
	}

	// Input HTML Append
	public function getInputAppendHTML() {
		return $this->_inputAppendHTML;
	}

	public function setInputAppendHTML( $inputAppendHTML ) {
		$this->_inputAppendHTML = $inputAppendHTML;

		return $this;
	}

	//  Input HTML Prepend
	public function getInputPrependHTML() {
		return $this->_inputPrependHTML;
	}

	public function setInputPrependHTML( $inputPrependHTML ) {
		$this->_inputPrependHTML = $inputPrependHTML;

		return $this;
	}

	// Render Mode
	public function getRenderMode() {
		return $this->_renderMode;
	}

	public function setRenderMode( $renderMode ) {
		$this->_renderMode = $renderMode;

		return $this;
	}

	// Whether this field is required or not
	public function isRequired() {
		return $this->_isRequired;
	}

	public function setIsRequired( $isRequired = true ) {
		$this->_isRequired = $isRequired;

		return $this;
	}

	// Required Field Marker
	public function getRequiredMarker() {
		return $this->requiredFieldMarker;
	}

	public function setRequiredMarker( $requiredFieldMarker ) {
		$this->requiredFieldMarker = $requiredFieldMarker;

		return $this;
	}

	// Variable Container
	public function getVariablePrepend() {
		return $this->_variablePrepend;
	}

	public function setVariablePrepend( $variablePrepend ) {
		$this->_variablePrepend = $variablePrepend;

		return $this;
	}

	public function getVariableAppend() {
		return $this->_variablePrepend;
	}

	public function setVariableAppend( $variableAppend ) {
		$this->_variableAppend = $variableAppend;

		return $this;
	}

	public function __destruct() {
		
	}

}

