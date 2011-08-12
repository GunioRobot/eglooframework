<?php
/**
 * PaymentGateway Class File
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
 * PaymentGateway
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
abstract class PaymentGateway {

	/* Class Constants */
	
	// Authorization for an amount
	const CREDIT_AUTHORIZE = 'authorize';
	
	// Capture funds from prior authorization
	const CREDIT_PRIOR_AUTH_CAPTURE = 'prior_auth_capture';
	
	// Authorize and capture
	const CREDIT_AUTHORIZE_CAPTURE = 'auth_capture';
	
	// Setup a credit card reference
	const CREDIT_REFERENCE_SET = 'reference_set';

	// Capture funds using credit card reference
	const CREDIT_REFERENCE_TXN = 'reference_txn';

	// Remove a credit card reference
	const CREDIT_REFERENCE_REMOVE = 'reference_remove';

	// Credit funds to a credit card reference
	const CREDIT_REFERENCE_CREDIT = 'reference_credit';

	// Credit funds to a credit card account
	const CREDIT_CREDIT = 'credit';

	// Void a credit card transaction before it clears
	const CREDIT_VOID = 'void';

	/* Public Data Members */
	
	/* Protected Data Members */

	protected $_postURLDeveloper = null;
	protected $_postURLProduction = null;

	/* Private Data Members */

	public function submitAPIRequest( $xml, $server = null ) {
		// TODO add an eGlooConfiguration check
	}

	public function parseAPIResponse() {
		
	}


}

