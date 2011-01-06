<?php
/**
 * ValidatedForm Class File
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
 * @copyright 2011 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package $package
 * @subpackage $subpackage
 * @version 1.0
 */

/**
 * ValidatedForm
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class ValidatedForm extends Form {

	public function validate() {
		$retVal = false;

		// TODO validate this form itself -- pick the right validator to do so.
		// Should branch?  Validate all children and then self via validator, or just invoke validator?

		foreach( $this->_formFieldSets as $formFieldSetID => $formFieldSet ) {
			// if ( !$formFieldSet->validate() ) {
			// 	$this->_formErrors[$formFieldSetID] = $formFieldSet->getErrors();
			// }
		}
		
		foreach( $this->_formFields as $formFieldID => $formField ) {
			// if ( !$formField->validate() ) {
			// 	$this->_formErrors[$formFieldID] = $formField->getErrors();
			// }
		}

		if ( empty($this->_formErrors) ) {
			$retVal = true;
		}

		return $retVal;
	}

}

