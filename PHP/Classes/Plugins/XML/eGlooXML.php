<?php
/**
 * eGlooXML Class File
 *
 * Contains the class definition for the eGlooXML
 * 
 * Copyright 2011 eGloo LLC
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *		  http://www.apache.org/licenses/LICENSE-2.0
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
 * @version 1.0
 */

/**
 * eGlooXML
 *
 * $short_description
 *
 * $long_description
 *
 * @category Plugins
 * @package XML
 */
class eGlooXML {

	// Process flags
	const PROCESS_ATTRIBUTES	= 0x01;
	const PROCESS_CHILDREN		= 0x02;
	const PROCESS_ALL			= 0xffff;

	// Build flags
	const BUILD_ATTRIBUTES		= 0x01;
	const BUILD_CHILDREN		= 0x02;
	const BUILD_STRUCTURE		= 0x04;
	const BUILD_ALL				= 0xffff;

	// Return flags
	const RETURN_ARRAY			= 0x01;
	const RETURN_FLATTENED		= 0x02;

	/**
	 * @var array Attributes of this XML node
	 */
	protected $_attributes = array();

	/**
	 * @var array Children of this XML DOM
	 */
	protected $_children = null;

	/**
	 * @var object SimpleXMLElement object
	 */
	protected $_simpleXMLObject = null;

	public function __construct( $xml ) {
		if ( is_string($xml) ) {
			if ( file_exists($xml) && is_file($xml) && is_readable($xml) ) {
				$this->_simpleXMLObject = simplexml_load_file( $xml );
			} else {
				$this->_simpleXMLObject = new SimpleXMLElement( $xml );
			}
		} else if ( $xml instanceof SimpleXMLElement ) {
			$this->_simpleXMLObject = $xml;
		} else {
			throw new Exception( 'eGlooXML does not support instantiation from type ' . gettype($xml) . '.' );
		}

		if ( !$this->_simpleXMLObject ) {
			$error_message = 'eGlooXML: simplexml_load_file( "' . $xml . '" ): ' . var_export(libxml_get_errors(), true);
			eGlooLogger::writeLog( eGlooLogger::EMERGENCY, $error_message );
			throw new Exception( $error_message );
		}
	}

	/**
	 * Executes a given XPath expression and returns the result
	 *
	 * @return mixed Result of XPath expression
	 */
	public function xpath( $xpath, $return = self::RETURN_ARRAY ) {
		$retVal = null;

		if ( $this->_simpleXMLObject->xpath( $xpath ) ) {
			$retVal = array();

			foreach( $this->_simpleXMLObject->xpath( $xpath ) as $xpath_result ) {
				try {
					if ( $xpath_result !== false && !empty($xpath_result)) {
						$retVal[] = new eGlooXML( $xpath_result );
					}
				} catch ( Exception $e ) {
					// Ignore, logged inside constructor
				}
			}
		}

		if ( $return === self::RETURN_FLATTENED ) {
			if ( empty($retVal) ) {
				$retVal = null;
			} else if ( count($retVal) === 1 ) {
				$retVal = $retVal[0];
			}
		} else if ( empty($retVal) ) {
			$retVal = array();
		}

		return $retVal;
	}

	/**
	 * Returns XML node as a hydrated array
	 *
	 * @param $return_flags Flags specifying return format
	 * @param $build_flags Flags specifying build options
	 * @param $process_flags Flags specifying process options
	 *
	 * @return array Hydrated representation of this XML node
	 */
	public function getHydratedArray( $return_flags = self::RETURN_ARRAY, $build_flags = self::BUILD_ALL, $process_flags = self::PROCESS_ALL ) {
		$retVal = null;

		$retVal = array();

		$objectName = $this->_simpleXMLObject->getName();

		if ( $this->getChildCount() === 0 && $this->getAttributeCount() === 0 ) {
			$retVal = (string) $this->_simpleXMLObject;
		} else {
			$children = array();

			if ( $process_flags & self::PROCESS_CHILDREN ) {
				foreach( $this->_simpleXMLObject->children() as $child ) {
					if ( $build_flags & self::BUILD_STRUCTURE ) {
						if ( $child->getName() !== eGlooInflector::pluralize($this->_simpleXMLObject->getName()) ) {
							$elementGroup = eGlooInflector::pluralize($child->getName());
						} else {
							$elementGroup = $child->getName();
						}

						if ( isset($children[$child->getName()]) ) {
							$children[$elementGroup] = array();
						}

						$childObjWrapper = null;

						try {
							if ( $child !== false && !empty($child)) {
								$childObjWrapper = new eGlooXML( $child );
							}
						} catch ( Exception $e ) {
							// Ignore, logged inside constructor
						}

						if ( !$childObjWrapper ) {
							continue;
						}

						// if ( isset($children[$elementGroup][$child->getName()]) ) {
						// 	$children[$elementGroup][$child->getName()] = array();
						// }

						if ( $childObjWrapper->hasNodeID() ) {
							$children[$elementGroup][$childObjWrapper->getNodeID()] = $childObjWrapper->getHydratedArray();
						} else {
							$children[$elementGroup][] = $childObjWrapper->getHydratedArray();
						}

						ksort($children[$elementGroup]);
					} else {
						$childObjWrapper = new eGlooXML( $child );

						if ( $childObjWrapper->hasNodeID() ) {
							$children[$childObjWrapper->getNodeID()] = $childObjWrapper->getHydratedArray();
						} else {
							$children[] = $childObjWrapper->getHydratedArray();
						}
					}
				}

				ksort($children);
			}

			$attributes = array();

			if ( $process_flags & self::PROCESS_ATTRIBUTES ) {
				foreach( $this->_simpleXMLObject->attributes() as $key => $value ) {
					if ( isset($this->$key) ) {
						$attributes[$key] = $this->$key->getValue();
					} else if ( !isset($this->$key) && !isset($this->_attributes[$key]) && (string) $value !== '' ) {
						$attributes[$key] = (string) $value;
					}
				}

				ksort($attributes);
			}

			if ( $build_flags === self::BUILD_ALL ) {
				$retVal = array_merge( $children, $attributes );
			} else if ( $build_flags & self::BUILD_ATTRIBUTES ) {
				$retVal = $attributes;
			} else if ( $build_flags & self::BUILD_CHILDREN ) {
				$retVal = $children;
			}

			ksort($retVal);
		}

		return $retVal;
	}

	/**
	 * Returns the ID for this XML node
	 *
	 * @return string ID of this XML node
	 */
	public function getNodeID() {
		$retVal = isset($this->_simpleXMLObject['id']) ? (string) $this->_simpleXMLObject['id'] : null;

		return $retVal;
	}

	/**
	 * Returns true if there is an ID for this XML node, false otherwise
	 *
	 * @return string ID of this XML node
	 */
	public function hasNodeID() {
		$retVal = isset($this->_simpleXMLObject['id']) ? true : false;

		return $retVal;
	}

	/**
	 * Returns number of attributes for this XML node
	 *
	 * @return integer Count of attributes for this XML DOM
	 */
	public function getAttributeCount() {
		return count( $this->_simpleXMLObject->attributes() );
	}

	/**
	 * Returns protected class member $_attributes
	 *
	 * @return array Attributes of this XML node
	 */
	public function getAttributes() {
		return $this->_attributes;
	}

	/**
	 * Sets protected class member $_attributes
	 *
	 * @param attributes array Attributes of this XML node
	 */
	public function setAttributes( $attributes ) {
		$this->_attributes = $attributes;
	}

	/**
	 * Returns true or false if an attribute is set (processed)
	 *
	 * @return bool If the given attribute key is set
	 */
	public function issetAttribute( $key ) {
		return isset( $this->_attributes[$key] );
	}

	/**
	 * Returns attribute value requested by key from class member $_attributes
	 *
	 * @return string Attribute value by key
	 */
	public function getAttribute( $key ) {
		return $this->_attributes[$key];
	}

	/**
	 * Sets value for key in protected class member $_attributes
	 *
	 * @param key string Attribute key
	 * @param value string Attribute value
	 */
	public function setAttribute( $key, $value ) {
		$this->_attributes[$key] = $value;
	}

	/**
	 * Returns number of children for this XML node
	 *
	 * @return integer Count of children for this XML DOM
	 */
	public function getChildCount() {
		return $this->_simpleXMLObject->count();
	}

	/**
	 * Returns protected class member $_children
	 *
	 * @return array Children of this XML DOM
	 */
	public function getChildren() {
		return $this->_children;
	}

	/**
	 * Sets protected class member $_children
	 *
	 * @param children array Children of this XML DOM
	 */
	public function setChildren( $children ) {
		$this->_children = $children;
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
	 * Returns protected class member $_simpleXMLObject
	 *
	 * @return object SimpleXMLElement object
	 */
	public function getSimpleXMLObject() {
		return $this->_simpleXMLObject;
	}

	/**
	 * Sets protected class member $_simpleXMLObject
	 *
	 * @param simpleXMLObject object SimpleXMLElement object
	 */
	public function setSimpleXMLObject( $simpleXMLObject ) {
		$this->_simpleXMLObject = $simpleXMLObject;
	}

	/**
	 * Get eGlooXML object as array
	 *
	 * @return array Array representation of this eGlooXML
	 */
	public function toArray() {
		
	}

	public function __isset( $node_name ) {
		$retVal = false;

		if ( isset( $this->_attributes[$node_name] ) && $this->_attributes[$node_name]->getValue() !== null ) {
			$retVal = true;
		} else if ( isset( $this->_children[$node_name] ) && $this->_children[$node_name]->getValue() !== null ) {
			$retVal = true;
		} else if ( !isset( $this->_attributes[$node_name] ) && $this->_simpleXMLObject->xpath( '@' . $node_name) ) {
			$attribute_values = $this->_simpleXMLObject->xpath( '@' . $node_name);
			$attribute_value = (string) $attribute_values[0];

			$this->_attributes[$node_name] = new eGlooXMLNode( $attribute_value, eGlooXMLNode::ATTRIBUTE );

			$retVal = true;
		} else if ( !isset( $this->_children[$node_name] ) && $this->_simpleXMLObject->xpath( $node_name) ) {
			// TODO
		} else {
			// TODO
		}

		return $retVal;
	}

	public function __unset( $node_name ) {
		if ( isset( $this->_attributes[$node_name] ) ) {
			$this->_attributes[$node_name]->setValue(null);

			if ( $this->_simpleXMLObject->xpath( '@' . $node_name) ) {
				$this->_simpleXMLObject[$node_name] = null;
			}
		} else if ( isset( $this->_children[$node_name] ) ) {
			$this->_children[$node_name]->setValue(null);

			if ( $this->_simpleXMLObject->xpath( '@' . $node_name) ) {
				// TODO
			}
		}
	}

	public function __get( $node_name ) {
		$retVal = null;

		if ( isset( $this->_attributes[$node_name] ) ) {
			$retVal = $this->_attributes[$node_name];
		} else if ( isset( $this->_children[$node_name] ) ) {
			$retVal = $this->_children[$node_name];
		} else if ( $this->_simpleXMLObject->xpath( '@' . $node_name) ) {
			$attribute_values = $this->_simpleXMLObject->xpath( '@' . $node_name);
			$attribute_value = (string) $attribute_values[0];

			$this->_attributes[$node_name] = new eGlooXMLNode( $attribute_value, eGlooXMLNode::ATTRIBUTE );

			$retVal = $this->_attributes[$node_name];
		} else if ( $this->_simpleXMLObject->xpath( '@' . $node_name) ) {
			// TODO
		}

		return $retVal;
	}

	public function __call( $method_name, $arguments ) {
		
	}

	/**
	 * Return the string representation of this XML object
	 *
	 * @return string String representation of this XML object
	 */
	public function __toString() {
		return (string) $this->_simpleXMLObject;
	}

	// From http://recursive-design.com/blog/2007/04/05/format-xml-with-php/
	public static function formatXMLString( $xml ) {	

	  // add marker linefeeds to aid the pretty-tokeniser (adds a linefeed between all tag-end boundaries)
	  $xml = preg_replace('/(>)(<)(\/*)/', "$1\n$2$3", $xml);

	  // now indent the tags
	  $token	  = strtok($xml, "\n");
	  $result	  = ''; // holds formatted version as it is built
	  $pad		  = 0; // initial indent
	  $matches	  = array(); // returns from preg_matches()

	  // scan each line and adjust indent based on opening/closing tags
	  while ($token !== false) : 

		// test for the various tag states

		// 1. open and closing tags on same line - no change
		if (preg_match('/.+<\/\w[^>]*>$/', $token, $matches)) : 
			$indent = -1;
			$pad++;
		// 2. closing tag - outdent now
		elseif (preg_match('/^<\/\w/', $token, $matches)) :
		  if ( $indent === -1 ) {
			$indent = 0;
		  }
		  $pad--;
		// 3. opening tag - don't pad this one, only subsequent tags
		elseif (preg_match('/^<\w[^>]*[^\/]>.*$/', $token, $matches)) :
		  $indent=1;
		// 4. no indentation needed
		else :
		  $indent = 0; 
		endif;

		// pad the line with the required number of leading spaces
		$line	 = str_pad($token, strlen($token)+$pad, "\t", STR_PAD_LEFT);
		$result .= $line . "\n"; // add to the cumulative result, with linefeed
		$token	 = strtok("\n"); // get the next token
		$pad	+= $indent; // update the pad size for subsequent lines
	  endwhile; 

	  return $result;
	}
}

