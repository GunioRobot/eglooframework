<?php
/**
 * AuthorizeNetGateway Class File
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
 * AuthorizeNetGateway
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class AuthorizeNetGateway extends PaymentGateway {

	/* Public Data Members */
	
	/* Protected Data Members */

	protected $_postURLDeveloper = 'https://apitest.authorize.net/xml/v1/request.api';
	protected $_postURLProduction = 'https://api.authorize.net/xml/v1/request.api';

	/* Private Data Members */

	public function submitXMLAPIRequest( $xml, $server = null ) {
		// TODO add an eGlooConfiguration check
	}

	public function parseXMLAPIResponse() {
		
	}

}

