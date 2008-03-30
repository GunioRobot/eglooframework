<?php
/**
 * RequestInfoBean Class File
 *
 * Needs to be commented
 * 
 * Copyright 2008 eGloo, LLC
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
 * @author Keith Buel
 * @copyright 2008 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package Data Transfer Objects
 * @version 1.0
 */

/**
 * RequestInfoBean
 * 
 * This class is simply a data holder for current validated request info.
 *
 * @package Data Transfer Objects
 */
class RequestInfoBean {
 	
 	private $requestProcessorID = null;
    private $requestClass = null;
    private $requestID = null;
    private $arguments = null;

    private $COOKIES = null;
    private $FILES = null;
    private $GET = null; 
    private $POST = null;
    
    private $decoratorArray = array();

    public function __construct() {
        $this->COOKIES = array();
        $this->FILES = array();
        $this->GET = array();
        $this->POST = array();
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

    public function setCOOKIE( $key, $value ) {
        if ( !isset( $this->COOKIES[$key] ) ) {
            $this->COOKIES[$key] = $value;
        } else if ( $this->COOKIES[$key] !== $value ) {
            throw new RequestInfoException( 'Programmer Error: Attempted to change COOKIE key \'' . 
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

    public function setFILES( $key, $value ) {
        if ( !isset( $this->FILES[$key] ) ) {
            $this->FILES[$key] = $value;
        } else if ( $this->FILES[$key] !== $value ) {
            throw new RequestInfoException( 'Programmer Error: Attempted to change FILES key \'' . 
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

    public function getGET( $key ) {
        if ( !isset( $this->GET[$key] ) ) {
            trigger_error( 'SECURITY ALERT: Attempted to access unset GET with unvalidated key \'' . $key . '\'', E_USER_ERROR );
        }
        
        return $this->GET[$key];
    }    

    public function setGET( $key, $value ) {
        if ( !isset( $this->GET[$key] ) ) {
            $this->GET[$key] = $value;
        } else if ( $this->GET[$key] !== $value ) {
            throw new RequestInfoException( 'Programmer Error: Attempted to change GET key \'' . 
                $key . '\' (value = \'' . $this->GET[$key] . '\') to \'' . $value . '\'' );
        }    
    }    

    public function issetPOST( $key ) {
        $retVal = false;
        
        if ( isset( $this->POST[$key] ) ) {
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
 	
    public function setPOST( $key, $value ) {
        if ( !isset( $this->POST[$key] ) ) {
            $this->POST[$key] = $value;
        } else if ( $this->POST[$key] !== $value ) {
            throw new RequestInfoException( 'Programmer Error: Attempted to change POST key \'' . 
                $key . '\' (value = \'' . $this->POST[$key] . '\') to \'' . $value . '\'' );
        }    
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
    
    public function setRequestProcessorID( $requestProcessorID ) {
        $this->requestProcessorID = $requestProcessorID;
    }
 	
 	public function getRequestProcessorID() {
        return $this->requestProcessorID;
    } 	


    public function setDecoratorArray( $decoratorArray ) {
        $this->decoratorArray = $decoratorArray;
    }
 	
 	public function getDecoratorArray() {
        return $this->decoratorArray;
    } 	
 	
 }
 

?>
