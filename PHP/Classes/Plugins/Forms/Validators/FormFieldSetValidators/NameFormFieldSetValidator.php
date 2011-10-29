<?php
/**
 * NameFormFieldSetValidator Class File
 *
 * Contains the class definition for the NameFormFieldSetValidator
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
 * NameFormFieldSetValidator
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class NameFormFieldSetValidator extends FormFieldSetValidator {

	public function validate( $nameFormFieldSet ) {
		$retVal = true;

		$firstNameField = $nameFormFieldSet->getFormField('first_name');

		if ( !preg_match('~^[a-zA-Z -]{1,32}$~', $firstNameField->getValue() ) ) {
			$firstNameField->setHasError( true );
			$retVal = false;
		} else {
			$firstNameField->setHasError( false );
		}

		// TODO check middle initial, suffix, prefix, etc.

		$lastNameField = $nameFormFieldSet->getFormField('last_name');

		if ( !preg_match('~^[a-zA-Z -]{1,32}$~', $lastNameField->getValue() ) ) {
			$lastNameField->setHasError( true );
			$retVal = false;
		} else {
			$lastNameField->setHasError( false );
		}

		if ( $retVal ) {
			// TODO Validation - should branch based upon localization
			// True for now
			// $retVal = true;
		}

		return $retVal;
	}

}

