<?php
/**
 * RequestInfoBean Class File
 *
 * Needs to be commented
 * 
 * Copyright 2011 eGloo, LLC
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
 * @author Keith Buel
 * @copyright 2011 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @category DataProcessing
 * @package Persistence
 * @subpackage DataTransferObjects
 * @version 1.0
 */

/**
 * RequestInfoBean
 * 
 * This class is simply a data holder for current validated request info.
 *
 * @category DataProcessing
 * @package Persistence
 * @subpackage DataTransferObjects
 */
class RequestInfoBean {

	private $arguments = null;
	private $requestProcessorID = null;
	private $errorRequestProcessorID = null;
	private $requestClass = null;
	private $requestID = null;
	private $_wildCardRequest = false;
	private $_wildCardRequestClass = null;
	private $_wildCardRequestID = null;

	// Sanitized
	private $COOKIES = null;
	private $FILES = null;
	private $GET = null; 
	private $POST = null;

	private $forms = null;
	
	// Unset Required
	private $UNSET_COOKIES = null;
	private $UNSET_FILES = null;
	private $UNSET_GET = null; 
	private $UNSET_POST = null;

	private $unsetForms = null;
	
	// Invalid provided
	private $INVALID_COOKIES = null;
	private $INVALID_FILES = null;
	private $INVALID_GET = null; 
	private $INVALID_POST = null;

	private $invalidForms = null;

	private $decoratorArray = array();

	private $application = null;
	private $interfaceBundle = null;

	protected static $singleton;
	
	final private function __construct() {
		if ( isset(self::$singleton) ) {
		throw new Exception('Attempted __construct(): An instance of RequestInfoBean already exists');
		}

		// $this injected; magic method invocation
		$this->init();
	} 

	/**
	 * getInstance()
	 */
	final public static function getInstance() {
		if ( !isset(self::$singleton) ) {
			self::$singleton = new RequestInfoBean();
		}
	
		return self::$singleton;
	}

	/**
	 * This method gets called when a definition parser is instantiated.  It allows
	 * subclasses to handle their initialization without overriding their parent's constructor
	 */
	protected function init() {
		$this->COOKIES = array();
		$this->FILES = array();
		$this->GET = array();
		$this->POST = array();

		$this->forms = array();

		$this->UNSET_COOKIES = array();
		$this->UNSET_FILES = array();
		$this->UNSET_GET = array();
		$this->UNSET_POST = array();

		$this->unsetForms = array();

		$this->INVALID_COOKIES = array();
		$this->INVALID_FILES = array();
		$this->INVALID_GET = array();
		$this->INVALID_POST = array();

		$this->invalidForms = array();
	}

	public function issetCOOKIE( $key ) {
		$retVal = false;

		if ( isset( $this->COOKIES[$key] ) ) {
			$retVal = true;
		}

		return $retVal;
	}

	public function getCOOKIE( $key ) {
		if ( !isset( $this->COOKIES[$key] ) ) {
			trigger_error( 'SECURITY ALERT: Attempted to access unset COOKIE with unvalidated key \'' . $key . '\'', E_USER_ERROR );
		}

		return $this->COOKIES[$key];
	}

	public function getCOOKIEArray() {
		return $this->COOKIES;
	}

	public function setCOOKIE( $key, $value ) {
		if ( !isset( $this->COOKIES[$key] ) ) {
			$this->COOKIES[$key] = $value;
		} else if ( $this->COOKIES[$key] !== $value ) {
			throw new RequestInfoBeanException( 'Programmer Error: Attempted to change COOKIE key \'' . 
				$key . '\' (value = \'' . $this->COOKIES[$key] . '\') to \'' . $value . '\'' );
		}
	}

	public function issetFILES( $key ) {
		$retVal = false;
		
		if ( isset( $this->FILES[$key] ) ) {
			$retVal = true;
		}

		return $retVal;
	}

	public function getFILES( $key ) {
		if ( !isset( $this->FILES[$key] ) ) {
			trigger_error( 'SECURITY ALERT: Attempted to access unset FILES with unvalidated key \'' . $key . '\'', E_USER_ERROR );
		}

		return $this->FILES[$key];
	}

	public function getFILESArray() {
		return $this->FILES;
	}

	public function setFILES( $key, $value ) {
		if ( !isset( $this->FILES[$key] ) ) {
			$this->FILES[$key] = $value;
		} else if ( $this->FILES[$key] !== $value ) {
			throw new RequestInfoBeanException( 'Programmer Error: Attempted to change FILES key \'' . 
				$key . '\' (value = \'' . $this->FILES[$key] . '\') to \'' . $value . '\'' );
		}		 
	}

	public function issetGET( $key ) {
		$retVal = false;
		
		if ( isset( $this->GET[$key] ) ) {
			$retVal = true;
		}

		return $retVal;
	}

	public function issetInvalidGET( $key ) {
		$retVal = false;
		
		if ( isset( $this->INVALID_GET[$key] ) ) {
			$retVal = true;
		}

		return $retVal;
	}

	public function isRequiredGETUnset( $key ) {
		$retVal = false;
		
		if ( isset( $this->UNSET_GET[$key] ) ) {
			$retVal = true;
		}

		return $retVal;
	}

	public function getGET( $key ) {
		if ( !isset( $this->GET[$key] ) ) {
			trigger_error( 'SECURITY ALERT: Attempted to access unset GET with unvalidated key \'' . $key . '\'', E_USER_ERROR );
		}
		
		return $this->GET[$key];
	}	 

	public function getGETArray() {
		return $this->GET;
	}

	public function getInvalidGET( $key ) {
		if ( !isset( $this->INVALID_GET[$key] ) ) {
			trigger_error( 'Programmer Error: Requested GET key \'' . $key . '\' not found in invalid GET list', E_USER_ERROR );
		}

		return $this->INVALID_GET[$key];
	}

	public function getInvalidGETArray() {
		return $this->INVALID_GET;
	}

	public function getUnsetGETArray() {
		return $this->UNSET_GET;
	}

	public function setGET( $key, $value ) {
		if ( !isset( $this->GET[$key] ) ) {
			$this->GET[$key] = $value;
		} else if ( $this->GET[$key] !== $value ) {
			throw new RequestInfoBeanException( 'Programmer Error: Attempted to change GET key \'' . 
				$key . '\' (value = \'' . $this->GET[$key] . '\') to \'' . $value . '\'' );
		}	 
	}

	public function setInvalidGET( $key, $value ) {
		$this->INVALID_GET[$key] = $value;
	}

	public function setUnsetRequiredGET( $key ) {
		$this->UNSET_GET[$key] = $key;
	}

	public function issetPOST( $key ) {
		$retVal = false;
		
		if ( isset( $this->POST[$key] ) ) {
			$retVal = true;
		}

		return $retVal;
	}

	public function issetInvalidPOST( $key ) {
		$retVal = false;
		
		if ( isset( $this->INVALID_POST[$key] ) ) {
			$retVal = true;
		}

		return $retVal;
	}

	public function isRequiredPOSTUnset( $key ) {
		$retVal = false;
		
		if ( isset( $this->UNSET_POST[$key] ) ) {
			$retVal = true;
		}

		return $retVal;
	}

	public function getPOST( $key ) {
		if ( !isset( $this->POST[$key] ) ) {
			trigger_error( 'SECURITY ALERT: Attempted to access unset POST with unvalidated key \'' . $key . '\'', E_USER_ERROR );
		}
		
		return $this->POST[$key];
	}

	public function getPOSTArray() {
		return $this->POST;
	}

	public function getInvalidPOST( $key ) {
		if ( !isset( $this->INVALID_POST[$key] ) ) {
			trigger_error( 'Programmer Error: Requested POST key \'' . $key . '\' not found in invalid POST list', E_USER_ERROR );
		}

		return $this->INVALID_POST[$key];
	}

	public function getInvalidPOSTArray() {
		return $this->INVALID_POST;
	}

	public function getUnsetPOSTArray() {
		return $this->UNSET_POST;
	}

	public function setPOST( $key, $value ) {
		if ( !isset( $this->POST[$key] ) ) {
			$this->POST[$key] = $value;
		} else if ( $this->POST[$key] !== $value ) {
			throw new RequestInfoBeanException( 'Programmer Error: Attempted to change POST key \'' . 
				$key . '\' (value = \'' . $this->POST[$key] . '\') to \'' . $value . '\'' );
		}	 
	}

	public function setInvalidPOST( $key, $value ) {
		$this->INVALID_POST[$key] = $value;
	}

	public function setUnsetRequiredPOST( $key ) {
		$this->UNSET_POST[$key] = $key;
	}

	public function issetForm( $formID ) {
		$retVal = false;
		
		if ( isset( $this->forms[$formID] ) ) {
			$retVal = true;
		}

		return $retVal;
	}

	public function issetInvalidForm( $formID ) {
		$retVal = false;
		
		if ( isset( $this->invalidForms[$formID] ) ) {
			$retVal = true;
		}

		return $retVal;
	}

	public function isRequiredFormUnset( $formID ) {
		$retVal = false;
		
		if ( isset( $this->unsetForms[$formID] ) ) {
			$retVal = true;
		}

		return $retVal;

	}

	public function setUnsetRequiredForm( $formID ) {
		$this->unsetForms[$formID] = $formID;
	}

	public function getForm( $formID ) {
		if ( !isset( $this->forms[$formID] ) ) {
			trigger_error( 'Programmer Error: Requested form \'' . $formID . '\' not found in forms list', E_USER_ERROR );
		}

		return $this->forms[$formID];
	}

	public function setForm( $formID, $formObj ) {
		$this->forms[$formID] = $formObj;
	}

	public function getInvalidForm( $formID ) {
		if ( !isset( $this->invalidForms[$formID] ) ) {
			trigger_error( 'Programmer Error: Requested form \'' . $formID . '\' not found in invalid forms list', E_USER_ERROR );
		}

		return $this->invalidForms[$formID];
	}

	public function setInvalidForm( $formID, $formObj ) {
		$this->invalidForms[$formID] = $formObj;
	}

	public function getFormsArray() {
		return $this->forms;
	}

	public function getInvalidFormsArray() {
		return $this->invalidForms;
	}

	public function getUnsetFormsArray() {
		return $this->invalidForms;
	}

	public function setRequestClass( $requestClass ) {
		$this->requestClass = $requestClass;
	}
	
	public function getRequestClass() {
		return $this->requestClass;	   
	}
	
	public function setRequestID( $requestID ) {
		$this->requestID = $requestID;
	}
	
	public function getRequestID() {
		return $this->requestID;
	}	

	public function isWildCardRequest() {
		return $this->_wildCardRequest;
	}

	public function setWildCardRequest( $wildCardRequest ) {
		$this->_wildCardRequest = $wildCardRequest;
	}

	public function setWildCardRequestClass( $wildCardRequestClass ) {
		$this->_wildCardRequestClass = $wildCardRequestClass;
	}
	
	public function getWildCardRequestClass() {
		return $this->_wildCardRequestClass;	   
	}
	
	public function setWildCardRequestID( $wildCardRequestID ) {
		$this->_wildCardRequestID = $wildCardRequestID;
	}
	
	public function getWildCardRequestID() {
		return $this->_wildCardRequestID;
	}	

	public function setRequestProcessorID( $requestProcessorID ) {
		$this->requestProcessorID = $requestProcessorID;
	}
	
	public function getRequestProcessorID() {
		return $this->requestProcessorID;
	}	

	public function setErrorRequestProcessorID( $errorRequestProcessorID ) {
		$this->errorRequestProcessorID = $errorRequestProcessorID;
	}
	
	public function getErrorRequestProcessorID() {
		return $this->errorRequestProcessorID;
	}	

	/**
	 * A convenience method for getting a fully qualified URI based on validated GET parameters
	 * provided to this request.
	 */
	public function getFullyQualifiedRequestString( $keys_to_ignore = array() ) {
		$retVal = $this->requestClass . '/' . $this->requestID . '?';

		$currentGETArray = $this->GET;

		foreach( $keys_to_ignore as $key ) {
			unset($currentGETArray[$key]);
		}

		$retVal .= http_build_query( $currentGETArray );

		return $retVal;
	}

	/**
	 * A convenience method for grabbing a slug.  If no slug provided, null is just as accurate as
	 * an exception, but it doesn't go all Rick James on your call stack.
	 */
	public function getSlug() {
		$retVal = null;

		if ( $this->requestInfoBean->issetGET('eg_slug') ) {
			$retVal = $this->requestInfoBean->getGET('eg_slug');
		}

		return $retVal;
	}

	public function setDecoratorArray( $decoratorArray ) {
		$this->decoratorArray = $decoratorArray;
	}
	
	public function getDecoratorArray() {
		return $this->decoratorArray;
	}	

	public function getApplication() {
		return $this->application;
	}

	public function setApplication( $application ) {
		$this->application = $application;
	}
	
	public function getInterfaceBundle() {
		return $this->interfaceBundle;
	}

	public function setInterfaceBundle( $interfaceBundle ) {
		$this->interfaceBundle = $interfaceBundle;
	}

	/**
	 * We disallow object cloning to enforce the singleton pattern
	 */
	final private function __clone() {
		throw new Exception('Attempted __clone(): An instance of RequestInfoBean already exists');
	}

}

