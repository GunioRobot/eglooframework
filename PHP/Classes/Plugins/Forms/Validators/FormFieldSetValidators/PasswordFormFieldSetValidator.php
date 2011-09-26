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

		// This validator presumes that the DBAL or DB driver is performing string escaping
		// If that is not the case in the environment you are operating in, reduce this set appropriately.
		if ( !preg_match('~^[a-zA-Z!@#$%^&*()+=/*_{};:<>,.? -]{6,32}$~', $firstPasswordField->getValue() ) ) {
			$firstPasswordField->setHasError( true );

			if ( !preg_match('~^.{6,32}$~', $firstPasswordField->getValue() ) ) {
				$firstPasswordField->setError( 
					'password_length', 'Password must be between 6 and 32 characters long', 'password_length_error_message' );
			}

			// Example...
			if ( preg_match('~[0-9]+~', $firstPasswordField->getValue() ) ) {
				$firstPasswordField->setError( 
					'password_digits', 'Password cannot contain digits', 'password_digit_error_message' );
			}

			// Example...
			if ( preg_match('~[\'"]+~', $firstPasswordField->getValue() ) ) {
				$firstPasswordField->setError( 
					'password_invalid_characters', 'Password cannot contain the following characters: \',"',
					'password_invalid_characters_error_message' );
			}

			$firstPasswordField->setValue('');
			$retVal = false;
		} else {
			$firstPasswordField->setHasError( false );
		}

		$secondPasswordField = $passwordFormFieldSet->getFormField('password_two');

		if ( $retVal && $firstPasswordField->getValue() !== $secondPasswordField->getValue() ) {
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

