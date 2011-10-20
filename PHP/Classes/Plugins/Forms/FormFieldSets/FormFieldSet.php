<?php
/**
 * FormFieldSet Class File
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
 * @subpackage FormFieldSets
 * @version 1.0
 */

/**
 * FormFieldSet
 *
 * $short_description
 *
 * $long_description
 *
 * @category Plugins
 * @package Forms
 * @subpackage FormFieldSets
 */
class FormFieldSet {

	const RENDER_MODE_EDIT 			= 0x01;
	const RENDER_MODE_EDIT_DISABLED = 0x02;
	const RENDER_MODE_EDIT_HIDDEN 	= 0x03;

	const RENDER_MODE_READ			= 0x04;
	const RENDER_MODE_READ_HIDDEN 	= 0x05;

	const RENDER_MODE_NONE			= 0x06;

	protected $_formFieldSetID = null;
	protected $_formFieldSetLegend = null;
	protected $_formFieldSetLegendToken = null;

	protected $_formFieldSetDefinition = null;

	protected $_formFieldSetElementOrder = array();

	protected $_formFieldChildErrors = array();

	protected $_formFieldChildren = array();
	protected $_formFieldChildData = array();

	protected $_errorMessage = null;
	protected $_errorMessageToken = null;

	protected $_errorHandler = null;

	protected $_appendHTML = '';

	protected $_cssClasses = array();

	protected $_prependHTML = '';

	protected $_hasError = false;

	protected $_renderMode = self::RENDER_MODE_EDIT;

	protected $_isRequired = false;
	protected $requiredFieldMarker = '&#9733;';

	protected $_renderedFormFieldSet = null;
	protected $_renderedErrors = null;

	protected $_variablePrepend = null;
	protected $_variableAppend = null;

	public function __construct( $formFieldSetID = null, $formFieldSetLegend = null ) {
		$this->_formFieldSetID = $formFieldSetID;
		$this->_formFieldSetLegend = $formFieldSetLegend;
	}

	public function addFormField( $child_form_field_id, $formField ) {
		if ( !isset($this->_formFieldChildren[$child_form_field_id]) ) {
			$this->_formFieldChildren[$child_form_field_id] = $formField;
			$elementCount = count($this->_formFieldSetElementOrder);
			$this->_formFieldSetElementOrder[$child_form_field_id] = $elementCount + 1;
			// $this->_formFieldChildData[$child_form_field_id] = $formField->getData();
		} else {
			throw new Exception( 'FormField child with ID "' . $child_form_field_id . '" already exists' );
		}
	}

	public function getFormField( $child_form_field_id ) {
		return $this->_formFieldChildren[$child_form_field_id];
	}

	public function issetFormField( $child_form_field_id ) {
		return isset($this->_formFieldChildren[$child_form_field_id]);
	}

	public function getFormFields() {
		return $this->_formFieldChildren;
	}

	public function getFormFieldData( $child_form_field_id ) {
		return $this->_formFieldChildData[$child_form_field_id];
	}

	public function setFormField( $child_form_field_id, $formField ) {
		$this->_formFieldChildren[$child_form_field_id] = $formField;
		// $this->_formFieldChildData[$child_form_field_id] = $formField->getData();

		return $this;
	}

	public function removeFormField( $child_form_field_id ) {
		unset($this->_formFieldChildren[$child_form_field_id]);
		// unset($this->_formFieldChildData[$child_form_field_id]);

		return $this;
	}

	public function getChildErrors() {
		// This should probably inquire from the FormFields themselves
		// return $this->_formFieldChildErrors;
	}

	public function hasError() {
		return $this->_hasError;
	}

	public function setHasError( $error_state ) {
		$this->_hasError = $error_state;

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

	public function render( $render_errors = true, $render_legend = true, $render_children = true, $render_child_labels = true, $render_frameset = true ) {
		$retVal = '';

		if ( $this->_renderMode !== self::RENDER_MODE_NONE ) {
			$retVal = "\t" . '<!-- FormFieldSet: "' . $this->getID() . '" -->' . "\n";

			if ( trim($this->getPrependHTML()) !== '' ) {
				$retVal .= "\t" . $this->getPrependHTML() . "\n";
			}

			if ( $this->getLegend() !== null && trim( $this->getLegend() ) !== '' ) {
				$retVal .= "\t" . '<fieldset id="fieldset-' . $this->getID() . '-form-fieldset" class="' .
					implode( ' ', $this->getCSSClasses() ) . '">' . "\n";

				$retVal .= "\t" . '<legend>';

				if ( $this->_isRequired ) {
					$retVal .= '<span id="fieldset-' . $this->getID() . '-form-fieldset-frameset-required-marker" ' .
						'class="form-fieldset-required-marker">' . $this->requiredFieldMarker . '</span> ';
				}

				$retVal .= $this->getLegend() . '</legend>' . "\n";
			}

			foreach( $this->getFormFields() as $formField ) {
				$formField->setVariablePrepend($this->getVariablePrepend());
				$retVal .= $formField->render( true, true, true, false, "\t" );
			}

			if ( $render_errors && $this->_hasError && trim($this->_errorMessage) !== '' ) {
				$retVal .= "\t" . '<div id="fieldset-' . $this->getID() .
					'-form-fieldset-errors" class="form-fieldset-errors">' . "\n";
				$retVal .= "\t\t" . $this->_errorMessage . "\n";

				if ( !empty( $this->_formFieldChildErrors ) ) {
					// TODO Localize this
					$retVal .= "\t\t" . '<ul id="fieldset-' . $this->getID() .
						'-form-fieldset-errors-list" class="form-fieldset-errors-list">' . "\n";

					foreach( $this->_formFieldErrors as $formFieldError ) {
						$retVal .= "\t\t\t" . '<li id="fieldset-' . $this->getID() .
							'-form-fieldset-errors-list-item" class="form-fieldset-errors-list-item">';

						// TODO localize the messages here
						$retVal .= $formFieldError['default_message'];

						$retVal .= '</li>' . "\n";
					}

					$retVal .= "\t\t" . '</ul>' . "\n";
				}

				$retVal .= "\t" . '</div>' . "\n";
			}

			if ( $this->getLegend() !== null && trim( $this->getLegend() ) !== '' ) {
				$retVal .= "\t" . '</fieldset>' . "\n";
			}

			if ( trim($this->getAppendHTML()) !== '' ) {
				$retVal .= "\t" . $this->getAppendHTML() . "\n";
			}
			

		}

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

	// Element Ordering
	public function swapElements( $first_element_id, $second_element_id ) {
		$first_index = $this->_formFieldSetElementOrder[$first_element_id];
		$second_index = $this->_formFieldSetElementOrder[$second_element_id];

		$this->_formFieldSetElementOrder[$first_element_id] = $second_index;
		$this->_formFieldSetElementOrder[$second_element_id] = $first_index;

		asort($this->_formFieldSetElementOrder);

		return $this;
	}

	public function insertElementBefore( $first_element_id, $second_element_id ) {
		// $first_index = $this->_formFieldSetElementOrder[$first_element_id];
		// $second_index = $this->_formFieldSetElementOrder[$second_element_id];
		// 
		// $this->_formFieldSetElementOrder[$first_element_id] = $second_index;
		// $this->_formFieldSetElementOrder[$second_element_id] = $first_index;
		// 
		// asort($this->_formFieldSetElementOrder);

		return $this;
	}

	public function insertElementAfter( $first_element_id, $second_element_id ) {
		// $first_index = $this->_formFieldSetElementOrder[$first_element_id];
		// $second_index = $this->_formFieldSetElementOrder[$second_element_id];
		// 
		// $this->_formFieldSetElementOrder[$first_element_id] = $second_index;
		// $this->_formFieldSetElementOrder[$second_element_id] = $first_index;
		// 
		// asort($this->_formFieldSetElementOrder);

		return $this;
	}

	public function setElementOrder( $element_order ) {
		$count = 1;

		foreach( $element_order as $element ) {
			if ( isset($this->_formFieldSetElementOrder[$element]) ) {
				$this->_formFieldSetElementOrder[$element] = $count;
				$count++;
			}
		}

		asort( $this->_formFieldSetElementOrder );

		return $this;
	}

	// HTML Prepend/Append
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

	// Render Mode
	public function getRenderMode() {
		return $this->_renderMode;
	}

	public function setRenderMode( $renderMode ) {
		$this->_renderMode = $renderMode;

		return $this;
	}


	// Legend
	public function getLegend() {
		return $this->_formFieldSetLegend;
	}

	public function setLegend( $formFieldSetLegend ) {
		$this->_formFieldSetLegend = $formFieldSetLegend;

		return $this;
	}

	public function getLegendToken() {
		return $this->_formFieldSetLegendToken;
	}

	public function setLegendToken( $formFieldSetLegendToken ) {
		$this->_formFieldSetLegendToken = $formFieldSetLegendToken;

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

