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

	public function __construct( $class = null, $id = null ) {
		$this->_class = $class;
		$this->_id = $id;
	}

	public function __destruct() {
		
	}

	public function bind( $parameter, $value ) {
		
	}

	public function unbind( $parameter ) {
		
	}

	abstract public function execute();

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

	public static function execute() {
		
	}

}

