<?php
/**
 * eGlooXMLChild Class File
 *
 * Contains the class definition for the eGlooXMLChild
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
 * @category $category
 * @package $package
 * @subpackage $subpackage
 * @version 1.0
 */

/**
 * eGlooXMLChild
 *
 * $short_description
 *
 * $long_description
 *
 * @category $category
 * @package $package
 * @subpackage $subpackage
 */
class eGlooXMLChild {

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
	 * @return eGlooXMLChild
	 */
	public function getChild( $xpath ) {
		return $this->_children[$xpath];
	}

	/**
	 * Set child by XPath
	 *
	 * @param $xpath XPath expression for child
	 * @param eGlooXMLChild Child to set
	 */
	public function setChild( $xpath, eGlooXMLChild $child ) {
		$this->_children[$xpath] = $child;
	}

	/**
	 * Get eGlooXMLChild object as array
	 *
	 * @return array Array representation of this eGlooXMLChild
	 */
	public function toArray() {
		
	}

}

