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
 * eGloo\Plugin\XML\XMLChild Class File
 *
 * Contains the class definition for the eGloo\Plugin\XML\XMLChild
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
 * eGloo\Plugin\XML\XMLChild
 *
 * $short_description
 *
 * $long_description
 *
 * @category Plugins
 * @package XML
 * @subpackage Components
 */
class XMLChild {

	/**
	 * @var array XML child node attributes
	 */
	protected $_attributes = null;

	/**
	 * Returns protected class member $_attributes
	 *
	 * @return array XML child node attributes
	 */
	public function getAttributes() {
		return $this->_attributes;
	}

	/**
	 * Sets protected class member $_attributes
	 *
	 * @param attributes array XML child node attributes
	 */
	public function setAttributes( $attributes ) {
		$this->_attributes = $attributes;
	}

	/**
	 * @var array XML children of this node
	 */
	protected $_children = null;

	/**
	 * Returns protected class member $_children
	 *
	 * @return array XML children of this node
	 */
	public function getChildren() {
		return $this->_children;
	}

	/**
	 * Sets protected class member $_children
	 *
	 * @param children array XML children of this node
	 */
	public function setChildren( $children ) {
		$this->_children = $children;
	}

	/**
	 * @var mixed Value of this XML child node
	 */
	protected $_value = null;

	/**
	 * Returns protected class member $_value
	 *
	 * @return mixed Value of this XML child node
	 */
	public function getValue() {
		return $this->_value;
	}

	/**
	 * Sets protected class member $_value
	 *
	 * @param value mixed Value of this XML child node
	 */
	public function setValue( $value ) {
		$this->_value = $value;
	}

	/**
	 * Get child by XPath
	 *
	 * @param $xpath XPath expression for child
	 * @return eGloo\Plugin\XML\XMLChild
	 */
	public function getChild( $xpath ) {
		return $this->_children[$xpath];
	}

	/**
	 * Set child by XPath
	 *
	 * @param $xpath XPath expression for child
	 * @param eGloo\Plugin\XML\XMLChild Child to set
	 */
	public function setChild( $xpath, XMLChild $child ) {
		$this->_children[$xpath] = $child;
	}

	/**
	 * Get eGloo\Plugin\XML\XMLChild object as array
	 *
	 * @return array Array representation of this eGloo\Plugin\XML\XMLChild
	 */
	public function toArray() {
		
	}

}

