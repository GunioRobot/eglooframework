<?php
/**
 * ValidatedFormField Class File
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
 * @package $package
 * @subpackage $subpackage
 * @version 1.0
 */

/**
 * ValidatedFormField
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class ValidatedFormField extends FormField {

	/**
	 * @var boolean State of validation
	 */
	protected $_valid = false;

	/**
	 * Returns protected class member $_valid
	 *
	 * @return boolean State of validation
	 */
	public function getValid() {
		return $this->_valid;
	}

	/**
	 * Sets protected class member $_valid
	 *
	 * @param valid boolean State of validation
	 */
	public function setValid( $valid ) {
		$this->_valid = $valid;
	}

	public function validate() {
		$retVal = false;

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

		// TODO validate this form field itself -- pick the right validator to do so.
		// Should branch?  Validate all children and then self via validator, or just invoke validator?

		return $retVal;
	}

	

}

