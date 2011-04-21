<?php
/**
 * eGlooDPDirector Class File
 *
 * Contains the class definition for the eGlooDPDirector
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
 * eGlooDPDirector
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
final class eGlooDPDirector {

	// Parsers (this needs to be refactored)
	private static $dataProcessingDefinitionParser;
	private static $singleton;

	// Protected variables for children to inherit
	protected $_connection_name = null;
	protected $_engine_mode = null;

	/**
	 * Private constructor because this class is a singleton
	 */
	private function __construct( $connection_name = 'egPrimary', $engine_mode = null ) {
		if ( isset(static::$singleton) ) {
			throw new Exception('Attempted __construct(): An instance of eGlooDPDirector already exists');
		} else {
			if ( $engine_mode === null ) {
				$connection_info = eGlooConfiguration::getDatabaseConnectionInfo( $connection_name );
				$engine_mode = $connection_info['engine'];
			}

			$this->_connection_name = $connection_name;
			$this->_engine_mode = $engine_mode;

			// We'll do a conditional check, but for now let's just build an XML parser
			self::$dataProcessingDefinitionParser = XML2ArrayDPDefinitionParser::getInstance( $this->_connection_name, $this->_engine_mode );
		}
	}

	public function getDPProcedureDefinition( $statement_class, $statement_id ) {
		return self::$dataProcessingDefinitionParser->getDPProcedureDefinition( $statement_class, $statement_id );
	}

	public function getDPSequenceDefinition( $statement_class, $statement_id ) {
		return self::$dataProcessingDefinitionParser->getDPSequenceDefinition( $statement_class, $statement_id );
	}

	public function getDPStatementDefinition( $statement_class, $statement_id ) {
		return self::$dataProcessingDefinitionParser->getDPStatementDefinition( $statement_class, $statement_id );
	}

	/**
	 * Returns the singleton of this class
	 */
    public static function getInstance( $connection_name = 'egPrimary', $engine_mode = null ) {
		if ( !isset(self::$singleton) ) {
			self::$singleton = new eGlooDPDirector( $connection_name, $engine_mode );
		}

		return self::$singleton;
    }

	final private function __clone() {
		throw new Exception('Attempted __clone(): An instance of eGlooDPDirector already exists');
	}

}
