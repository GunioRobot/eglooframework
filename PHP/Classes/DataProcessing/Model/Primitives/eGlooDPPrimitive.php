<?php
/**
 * eGlooDPPrimitive Class File
 *
 * Contains the class definition for the eGlooDPPrimitive
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
 * eGlooDPPrimitive
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
abstract class eGlooDPPrimitive extends eGlooDPObject {

	/**
	 * @var string Class of this primitive
	 */
	protected $_class = null;

	/**
	 * @var string ID of this primitive
	 */
	protected $_id = null;

	/**
	 * @var string Name of the connection we'll be using
	 */
	protected $_connection_name = null;

	/**
	 * @var integer Engine mode of the connection we're using
	 */
	protected $_engine_mode = null;

	public function __construct( $class = null, $id = null, $connection_name = null, $engine_mode ) {
		$this->_class = $class;
		$this->_id = $id;

		if ( $connection_name !== null ) {
			
		} else {
			
		}

		if ( $engine_mode !== null ) {
			
		} else {
			
		}
	}

	public function __destruct() {
		
	}

	public function bind( $parameter, $value ) {
		
	}

	public function unbind( $parameter ) {
		
	}

	abstract public function execute( $id = null, $parameters = null );

	/**
	 * Returns protected class member $_class
	 *
	 * @return string Class of this primitive
	 */
	public function getClass() {
		return $this->_class;
	}

	/**
	 * Sets protected class member $_class
	 *
	 * @param class string Class of this primitive
	 */
	public function setClass( $class ) {
		$this->_class = $class;
	}

	/**
	 * Returns protected class member $_connection_name
	 *
	 * @return string Name of the connection we'll be using
	 */
	public function getConnectionName() {
		return $this->_connection_name;
	}

	/**
	 * Sets protected class member $_connection_name
	 *
	 * @param connection_name string Name of the connection we'll be using
	 */
	public function setConnectionName( $connection_name ) {
		$this->_connection_name = $connection_name;
	}

	/**
	 * Returns protected class member $_engine_mode
	 *
	 * @return integer Engine mode of the connection we're using
	 */
	public function getEngineMode() {
		return $this->_engine_mode;
	}

	/**
	 * Sets protected class member $_engine_mode
	 *
	 * @param engine_mode integer Engine mode of the connection we're using
	 */
	public function setEngineMode( $engine_mode ) {
		$this->_engine_mode = $engine_mode;
	}

	/**
	 * Returns protected class member $_id
	 *
	 * @return string ID of this primitive
	 */
	public function getID() {
		return $this->_id;
	}

	/**
	 * Sets protected class member $_id
	 *
	 * @param id string ID of this primitive
	 */
	public function setID( $id ) {
		$this->_id = $id;
	}

	public static function executeOnce() {
		echo 'b';
	}

}

