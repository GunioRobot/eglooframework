<?php
/**
 * BirthdateFormFieldSetValidator Class File
 *
 * Contains the class definition for the BirthdateFormFieldSetValidator
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
 * @subpackage Validators
 * @version 1.0
 */

/**
 * BirthdateFormFieldSetValidator
 *
 * $short_description
 *
 * $long_description
 *
 * @category Plugins
 * @package Forms
 * @subpackage Validators
 */
class BirthdateFormFieldSetValidator extends FormFieldSetValidator {

	public function validate( $birthdateFormFieldSet ) {
		$retVal = true;

		$birthdateMonthField = $birthdateFormFieldSet->getFormField('birthdate_month');
		$birthdateDayField = $birthdateFormFieldSet->getFormField('birthdate_day');
		$birthdateYearField = $birthdateFormFieldSet->getFormField('birthdate_year');

		$monthIntVal = intval( $birthdateMonthField->getValue() );
		$dayIntVal = intval( $birthdateDayField->getValue() );
		$yearIntVal = intval( $birthdateYearField->getValue() );

		if ( $monthIntVal > 12 || $monthIntVal < 1 ) {
			$birthdateMonthField->setHasError( true );
			$retVal = false;
		} else {
			$birthdateMonthField->setHasError( false );
		}

		if ( $dayIntVal > 31 || $dayIntVal < 1 ) {
			$birthdateDayField->setHasError( true );
			$retVal = false;
		} else {
			$birthdateDayField->setHasError( false );
		}

		$validYears = YearValueSeeder::getInstance()->getValues( false );

		if ( !in_array($yearIntVal, $validYears) ) {
			$birthdateYearField->setHasError( true );
			$retVal = false;
		} else {
			$birthdateYearField->setHasError( false );
		}

		if ( !$retVal ) {
			$birthdateFormFieldSet->setHasError( true );
			// TODO Validation - should branch based upon localization
			// True for now
			// $retVal = true;
		}

		return $retVal;
	}

}

