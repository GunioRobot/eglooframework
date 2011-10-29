<?php
/**
 * RegistrationFormValidator Class File
 *
 * Contains the class definition for the RegistrationFormValidator
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
 * RegistrationFormValidator
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class RegistrationFormValidator extends FormValidator {

	public function validate( $registrationForm ) {
		$retVal = true;

		foreach( $registrationForm->getFormFieldSets() as $formFieldSet ) {
			$formFieldSetValidatorName = $formFieldSet->getValidator();
			$formFieldSetValidatorObj = new $formFieldSetValidatorName();

			if ( !$formFieldSetValidatorObj->validate($formFieldSet) ) {
				$retVal = false;
			}
		}

		// Not sure this is necessary
		// foreach( $registrationForm->getFormFields() as $formField ) {
		// 	$formFieldValidatorName = $formField->getValidator();
		// 	$formFieldValidatorObj = new $formFieldValidatorName();
		//
		// 	$formFieldValidatorObj->validate($formField);
		// }

		// TODO Run validation and such on FormAttributeSets dumped in

		return $retVal;
	}

}

