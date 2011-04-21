<?php
/**
 * eGlooDPDefinitionParser Class File
 *
 * Contains the class definition for the eGlooDPDefinitionParser
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
 * eGlooDPDefinitionParser
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
abstract class eGlooDPDefinitionParser {

	// We DO NOT declare this so that child classes will define it and will be responsible
	// for containing their own singletons.  This is a performance boost
	// protected static $singleton;

	// The location of the DataProcessing.xml we're concerned with parsing
	protected $REQUESTS_XML_LOCATION = '';

	// An array representing the DPSequence node definitions parsed out of the DataProcessing.xml
	protected $dataProcessingSequences = null;

	// An array representing the DPProcedure node definitions parsed out of the DataProcessing.xml
	protected $dataProcessingProcedures = null;

	// An array representing the DPStatement node definitions parsed out of the DataProcessing.xml
	protected $dataProcessingStatements = null;

	// Protected variables for children to inherit
	protected $_connection_name = null;
	protected $_engine_mode = null;

	/**
	 * eGlooDPDefinitionParser Constructor
	 *
	 * We create a final private constructor so that no class that inherits from this abstract class
	 * can instantiate objects without implementing the singleton pattern.  We enforce this pattern
	 * because no eGlooDPDefinitionParser needs to have more than one object instantiated at any
	 * time.
	 * 
	 * The constructor takes two arguments: the data connection name to parse DP nodes for and the relevant
	 * engine mode to process them for (lazy loading).
	 *
	 * @param $connection_name a string representing the string of the connection we want to deal with
	 * @param $engine_mode an integer constant referenced from ConnectionManager for the engine mode we're in
	 *
	 * @throws eGlooDPDefinitionParserException if attempting to construct a second instance of this class 
	 */
	final private function __construct( $connection_name, $engine_mode ) {
		// Make sure we haven't already constructed a singleton instance.  Throw exception if we have
		if ( isset(static::$singleton) ) {
			throw new eGlooDPDefinitionParserException('Attempted __construct(): An instance of ' . get_called_class() . ' already exists');
		} else {
			$this->connection_name = $connection_name;
			$this->engine_mode = $engine_mode;
		}

		// $this is injected; magic method invocation
		static::init();
	}

	/**
	 * A final static method for getting a singleton instance of this class that all subclasses can reference
	 *
	 * To enforce the singleton pattern down the inheritance tree we provide the getInstance method for getting
	 * a singleton instance of the relevant subclass being invoked.  Because we can get away with using late
	 * static binding, this method is left as final and no subclass should override it.
	 *
	 * @return the instantiated singleton of whichever subclass this method was invoked in using late static binding
	 */
	final public static function getInstance( $connection_name = 'egPrimary', $engine_mode = null ) {
		if ( !isset(static::$singleton) ) {
			static::$singleton = new static( $connection_name, $engine_mode );
		}

        return static::$singleton;
	}

	abstract public function getDPProcedureDefinition( $statement_class, $statement_id );

	abstract public function getDPSequenceDefinition( $statement_class, $statement_id );

	abstract public function getDPStatementDefinition( $statement_class, $statement_id );

	/**
	 * A definition parser must include this method to handle loading  and processing request
	 * nodes and requests attribute sets from the requests definition XML (Requests.xml)
	 */
	abstract protected function loadDataProcessingNodes();

	/**
	 * This method gets called when a definition parser is instantiated.  It allows
	 * subclasses to handle their initialization without overriding their parent's constructor
	 */
	protected function init() {
		static::loadDataProcessingNodes();
	}

	/**
	 * We disallow object cloning to enforce the singleton pattern
	 *
	 * @throws eGlooDPDefinitionParserException if this method is invoked
	 */
	final private function __clone() {
		throw new eGlooDPDefinitionParserException('Attempted __clone(): An instance of ' . get_called_class() . ' already exists');
	}

}
