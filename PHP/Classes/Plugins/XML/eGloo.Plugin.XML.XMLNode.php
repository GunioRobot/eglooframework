<?php
namespace eGloo\Plugin\XML;

use \eGloo\Configuration as Configuration;
use \eGloo\Utility\Logger as Logger;

use \eGloo\Performance\Caching\Gateway as CacheGateway;

use \DOMDocument as DOMDocument;
use \ErrorException as ErrorException;
use \Exception as Exception;
use \SimpleXMLElement as SimpleXMLElement;

/**
 * eGloo\Plugin\XML\XMLNode Class File
 *
 * Contains the class definition for the eGloo\Plugin\XML\XMLNode
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
 * @category Plugins
 * @package XML
 * @subpackage Components
 * @version 1.0
 */

/**
 * eGloo\Plugin\XML\XMLNode
 *
 * $short_description
 *
 * $long_description
 *
 * @category Plugins
 * @package XML
 * @subpackage Components
 */
class XMLNode {

	const ATTRIBUTE		= 0x00;
	const ELEMENT		= 0x01;

	/**
	 * @var integer Type of this node
	 */
	protected $_type = null;

	/**
	 * @var mixed Value of this node
	 */
	protected $_value = null;

	/**
	 * Constructor
	 *
	 * @param node mixed Node
	 * @param node_type integer Type of this node
	 * @param parameters array Parameters for constructing this node
	 */
	public function __construct( $node, $node_type = self::ELEMENT, $parameters = array() ) {
		if ( $node_type === self::ATTRIBUTE ) {
			if ( is_string($node) ) {
				$this->_value = $node;
			}
		} else if ( $node_type === self::ELEMENT ) {
			
		} else {
			// TODO
		}
	}

	/**
	 * Takes the node value and makes sure it is valid against a set of value filters
	 *
	 * @return this A reference to this object to enable chaining
	 */
	public function filter( $filters ) {
		if ( !in_array($this->_value, $filters) ) {
			$this->_value = null;
			// TODO throw exception?  Unsure
		}

		return $this;
	}

	/**
	 * Takes the node value and sets it to lower case.  Assumes value is string
	 *
	 * @return this A reference to this object to enable chaining
	 */
	public function toLowerCase() {
		$this->_value = strtolower($this->_value);

		return $this;
	}

	/**
	 * Returns protected class member $_type
	 *
	 * @return integer Type of this node
	 */
	public function getType() {
		return $this->_type;
	}

	/**
	 * Sets protected class member $_type
	 *
	 * @param type integer Type of this node
	 */
	public function setType( $type ) {
		$this->_type = $type;
	}

	/**
	 * Returns protected class member $_value
	 *
	 * @return mixed Value of this node
	 */
	public function getValue() {
		return $this->_value;
	}

	/**
	 * Sets protected class member $_value
	 *
	 * @param value mixed Value of this node
	 */
	public function setValue( $value ) {
		$this->_value = $value;
	}

	/**
	 * Return the string representation of this node
	 *
	 * @return string String representation of this node
	 */
	public function __toString() {
		return $this->_value;
	}

}

