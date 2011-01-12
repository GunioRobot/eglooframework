<?php
/**
 * PasswordFormFieldSetValidator Class File
 *
 * Contains the class definition for the PasswordFormFieldSetValidator
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
 * PasswordFormFieldSetValidator
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class PasswordFormFieldSetValidator extends FormFieldSetValidator {

	public function validate( $passwordFormFieldSet ) {
		$retVal = true;

		$firstPasswordField = $passwordFormFieldSet->getFormField('password_one');

		if ( !preg_match('~^[a-zA-Z!@#$%^&*()+=/*_{};:\'"<>,.? -]{6,32}$~', $firstPasswordField->getValue() ) ) {
			$firstPasswordField->setHasError( true );
			$retVal = false;
		} else {
			$firstPasswordField->setHasError( false );
		}

		// TODO check middle initial, suffix, prefix, etc.

		$secondPasswordField = $passwordFormFieldSet->getFormField('password_two');

		// if ( !preg_match('~^[a-zA-Z ]{1,32}$~', $secondPasswordField->getValue() ) ) {
		// 	$secondPasswordField->setHasError( true );
		// 	$retVal = false;
		// } else {
		// 	$secondPasswordField->setHasError( false );
		// }

		if ( $firstPasswordField->getValue() !== $secondPasswordField->getValue() ) {
			$secondPasswordField->setHasError( true );
			$retVal = false;
		}

		if ( $retVal ) {
			// TODO Validation - should branch based upon localization
			// True for now
			// $retVal = true;
		}

		return $retVal;
	}

}

