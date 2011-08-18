<?php
/**
 * SystemInfoBean Class File
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
 * @category DataProcessing
 * @package Persistence
 * @subpackage DataTransferObjects
 * @version 1.0
 */

/**
 * SystemInfoBean
 *
 * $short_description
 *
 * $long_description
 *
 * @category DataProcessing
 * @package Persistence
 * @subpackage DataTransferObjects
 */
class SystemInfoBean {

	protected static $singleton;
    
	private $namespaces = null;

	final private function __construct() {
		if ( isset(self::$singleton) ) {
			throw new Exception('Attempted __construct(): An instance of SystemInfoBean already exists');
		}

		$this->namespaces = array('SystemInfo' => array());
	} 

	public function setValue( $key, $value, $namespace = 'SystemInfo' ) {
		if (!isset($this->namespaces[$namespace])) {
			$this->namespaces[$namespace] = array();
		}

		$this->namespaces[$namespace][$key] = $value;
	}

	public function getValue( $key, $namespace = 'SystemInfo' ) {
		$retVal = null;

		if (isset($this->namespaces[$namespace]) && isset($this->namespaces[$namespace][$key])) {
			$retVal = $this->namespaces[$namespace][$key];
		} else if (!isset($this->namespaces[$namespace])) {
			throw new Exception('Invalid SystemInfoBean namespace requested: ' . $namespace);
		} else if (!isset($this->namespaces[$namespace][$key])) {
			throw new Exception('Invalid SystemInfoBean key requested: "' . $namespace . '" in namespace: ' . $namespace);
		}

		return $retVal;
	}

	public function appendValue( $key, $value, $namespace = 'SystemInfo' ) {
		$retVal = null;

		// if ( gettype($value) === 'string' || gettype($value) === 'array' ) {
			if ( $this->issetValue($key, $namespace) ) {
				$existingValue = $this->getValue( $key, $namespace );
			} else {
				$existingValue = array();
			}

			if ( gettype($existingValue) === 'string' ) {
				$retVal = $existingValue . $value;
				$this->setValue( $key, $retVal, $namespace );
			} else if ( gettype($existingValue) === 'array' ) {
				$existingValue[] = $value;
				$retVal = $existingValue;
				$this->setValue( $key, $retVal, $namespace );
			} else {
				throw new Exception('Value being appended to is type ' . gettype($existingValue) . ', not array or string');
			}
		// } else {
		// 	throw new Exception('Value being appended is type ' . gettype($value) . ', not array or string');
		// }

		return $retVal;
	}

	public function issetValue( $key, $namespace = 'SystemInfo' ) {
		$retVal = false;

		if (isset($this->namespaces[$namespace]) && isset($this->namespaces[$namespace][$key])) {
			$retVal = true;
		}

		return $retVal;
	}

	public function unsetValue( $key, $namespace = 'SystemInfo' ) {
		if (isset($this->namespaces[$namespace]) && isset($this->namespaces[$namespace][$key])) {
			unset($this->namespaces[$namespace][$key]);
		} else if (!isset($this->namespaces[$namespace])) {
			throw new Exception('Invalid SystemInfoBean namespace requested: ' . $namespace);
		}
	}

	public function createNamespace( $namespace ) {
		if (!isset($this->namespaces[$namespace])) {
			$this->namespaces[$namespace] = array();
		} else {
			throw new Exception('SystemInfoBean namespace already exists: ' . $namespace);
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
			self::$singleton = new SystemInfoBean();
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
		throw new Exception('Attempted __clone(): An instance of SystemInfoBean already exists');
	}

}
