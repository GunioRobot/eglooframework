<?php
/**
 * eGlooDPStatement Class File
 *
 * Contains the class definition for the eGlooDPStatement
 * 
 * Copyright 2011 eGloo LLC
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
 * @copyright 2011 eGloo LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package $package
 * @subpackage $subpackage
 * @version 1.0
 */

/**
 * eGlooDPStatement
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class eGlooDPStatement extends eGlooDPPrimitive {

	public function execute( $id = null, $parameters = null ) {
		if ( $this->_class === null ) {
			throw new Exception( 'No statement class provided for execution' );
		}

		if ( $id === null ) {
			if ( $this->_id !== null ) {
				$id = $this->_id;
			} else {
				throw new Exception( 'No statement ID provided for execution' );
			}
		}

		$eglooDPDirector = eGlooDPDirector::getInstance();

		$statement_definition = $eglooDPDirector->getDPStatementDefinition( $this->_class, $id );

		$statement_variant = null;

		if ( isset( $statement_definition['statement_variants'][$this->_connection_name]['engineModes'][$this->_engine_mode] ) ) {
			echo_r('here');
			$statement_variant = $statement_definition['statement_variants'][$this->_connection_name]['engineModes'][$this->_engine_mode];
		}

		echo_r($statement_variant);

		die_r( $statement_definition );
	}

}

