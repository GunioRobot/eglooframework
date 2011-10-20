<?php
/**
 * ValidatedFormFieldSet Class File
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
 * ValidatedFormFieldSet
 *
 * $short_description
 *
 * $long_description
 *
 * @category Plugins
 * @package Forms
 * @subpackage FormFieldSets
 */
class ValidatedFormFieldSet extends FormFieldSet {

	protected $_validator = null;

	public function validate() {
		$retVal = false;

		// TODO validate this form field set itself -- pick the right validator to do so.
		// Should branch?  Validate all children and then self via validator, or just invoke validator?

		if ( !empty($this->_formFieldChildren) ) {
			foreach( $this->_formFieldChildren as $formFieldID => $formField ) {
				if ( !$formField->validate() ) {
					$this->_formFieldChildErrors[$formFieldID] = $formField->getErrors();
				}
			}
		}

		if ( empty($this->_formFieldChildErrors) ) {
			$retVal = true;
		}

		return $retVal;
	}

	// Validator
	public function getValidator() {
		return $this->_validator;
	}

	public function setValidator( $validator ) {
		$this->_validator = $validator;
	}

}

