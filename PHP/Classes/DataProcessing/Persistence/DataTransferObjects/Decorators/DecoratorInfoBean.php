<?php
/**
 * DecoratorInfoBean Class File
 *
 * Needs to be commented
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
 * @category DataProcessing
 * @package Persistence
 * @subpackage DataTransferObjects
 * @version 1.0
 */

/**
 * DecoratorInfoBean
 * 
 * This class is simply a data holder for current decorator info
 *
 * @category DataProcessing
 * @package Persistence
 * @subpackage DataTransferObjects
 */
class DecoratorInfoBean {

	protected static $singleton;
    
	private $namespaces = null;

	final private function __construct() {
		if ( isset(self::$singleton) ) {
			throw new Exception('Attempted __construct(): An instance of DecoratorInfoBean already exists');
		}

		$this->namespaces = array('DefaultDecorator' => array());
	} 

	public function setValue( $key, $value, $namespace = 'DefaultDecorator' ) {
		if (!isset($this->namespaces[$namespace])) {
			$this->namespaces[$namespace] = array();
		}

		$this->namespaces[$namespace][$key] = $value;
	}

	public function getValue( $key, $namespace = 'DefaultDecorator' ) {
		$retVal = null;

		if (isset($this->namespaces[$namespace]) && isset($this->namespaces[$namespace][$key])) {
			$retVal = $this->namespaces[$namespace][$key];
		} else if (!isset($this->namespaces[$namespace])) {
			throw new Exception('Invalid DecoratorInfoBean namespace requested: ' . $namespace);
		} else if (!isset($this->namespaces[$namespace][$key])) {
			throw new Exception('Invalid DecoratorInfoBean key requested: "' . $namespace . '" in namespace: ' . $namespace);
		}

		return $retVal;
	}

	public function issetValue( $key, $namespace = 'DefaultDecorator' ) {
		$retVal = false;

		if (isset($this->namespaces[$namespace]) && isset($this->namespaces[$namespace][$key])) {
			$retVal = true;
		}

		return $retVal;
	}

	public function unsetValue( $key, $namespace = 'DefaultDecorator' ) {
		if (isset($this->namespaces[$namespace]) && isset($this->namespaces[$namespace][$key])) {
			unset($this->namespaces[$namespace][$key]);
		} else if (!isset($this->namespaces[$namespace])) {
			throw new Exception('Invalid DecoratorInfoBean namespace requested: ' . $namespace);
		}
	}

	public function createNamespace( $namespace ) {
		if (!isset($this->namespaces[$namespace])) {
			$this->namespaces[$namespace] = array();
		} else {
			throw new Exception('DecoratorInfoBean namespace already exists: ' . $namespace);
		}
	}

	public function getNamespaces() {
		return $this->namespaces;
	}

	public function resetNamespace( $namespace ) {
		$this->unsetNamespace($namespace);
		$this->createNamespace($namespace);
	}

	public function issetNamespace( $namespace ) {
		return isset($this->namespaces[$namespace]);
	}


	public function unsetNamespace( $namespace ) {
		unset($this->namespaces[$namespace]);
	}

	/**
	 * getInstance()
	 */
	final public static function getInstance() {
		if ( !isset(self::$singleton) ) {
			self::$singleton = new DecoratorInfoBean();
		}
	
		return self::$singleton;
	}

	public function __toString() {
		return $this->namespaces;
	}

	/**
	 * We disallow object cloning to enforce the singleton pattern
	 */
	final private function __clone() {
		throw new Exception('Attempted __clone(): An instance of DecoratorInfoBean already exists');
	}

}

