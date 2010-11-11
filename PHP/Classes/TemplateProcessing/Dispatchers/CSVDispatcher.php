<?php
/**
 * CSVDispatcher Class File
 *
 * $file_block_description
 * 
 * Copyright 2010 eGloo, LLC
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
 * @copyright 2010 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package $package
 * @subpackage $subpackage
 * @version 1.0
 */

/**
 * CSVDispatcher
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class CSVDispatcher extends TemplateDispatcher {

    /**
     * Static Constants
     */
    private static $singletonDispatcher;
    
    /**
     * XML Variables
     */
    private $DISPATCH_XML_LOCATION = "Templates/Applications/";
    private $dispatchNodes = array();


	private $application = null;
	private $interfaceBundle = null;

    /**
     * Private constructor because this class is a singleton
     */
    private function __construct( $application, $interfaceBundle ) {
		$this->application = $application;
		$this->interfaceBundle = $interfaceBundle;
        
		$this->DISPATCH_XML_LOCATION = eGlooConfiguration::getApplicationsPath() . '/';
        $this->loadDispatchNodes();  
    }
    
    /**
     * This method reads the xml file from disk into a document object model.
     * It then populates a hash of [CSVDispatcher] -> [CSVDispatch XML Object]
     */
    protected function loadDispatchNodes(){
        eGlooLogger::writeLog( eGlooLogger::DEBUG, "CSVDispatcher: Processing XML" );

		$applicationCSVDispatchXML = $this->DISPATCH_XML_LOCATION . 
            $this->application . '/InterfaceBundles/' . $this->interfaceBundle . '/CSV/Dispatch.xml';

		if ( !file_exists($applicationCSVDispatchXML) || !is_file($applicationCSVDispatchXML) || !is_readable($applicationCSVDispatchXML) ) {
			$applicationCSVDispatchXML = eGlooConfiguration::getFrameworkRootPath() . '/Templates/Core/Generic/CSV/Dispatch.xml';
		}

        //read the xml onces... global location to do this... it looks like it does this once per request.
        $requestXMLObject = simplexml_load_file( $applicationCSVDispatchXML );
        
        foreach( $requestXMLObject->xpath( '/eGlooCSV:Requests/RequestClass' ) as $requestClass ) {
            foreach( $requestClass->xpath( 'child::Request' ) as $request ){
                $uniqueKey = ( (string) $requestClass['id'] ) . ( (string) $request['id']  );
                $this->dispatchNodes[ $uniqueKey  ] = $request->asXML();            
            }
        }
        
    }

    /**
     * returns the singleton of this class
     */
    public static function getInstance( $application, $interfaceBundle ) {
        if ( !isset(self::$singletonDispatcher) ) {
            $cacheGateway = CacheGateway::getCacheGateway();
            
            if ( (self::$singletonDispatcher = $cacheGateway->getObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'CSVDispatcherNodes', 'ContentDispatching' ) ) == null ) {
                eGlooLogger::writeLog( eGlooLogger::DEBUG, "CSVDispatcher: Building Singleton" );
                self::$singletonDispatcher = new CSVDispatcher( $application, $interfaceBundle );
                $cacheGateway->storeObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'CSVDispatcherNodes', self::$singletonDispatcher, 'ContentDispatching' );
            } else {
                eGlooLogger::writeLog( eGlooLogger::DEBUG, "CSVDispatcher: Singleton pulled from cache" );
            }
        }
        
        return self::$singletonDispatcher;
    }

    /**
     * Only functional method available to the public.  
     */
    public function dispatch( $requestInfoBean ) {

        $userRequestClass = $requestInfoBean->getRequestClass();
        $userRequestID = $requestInfoBean->getRequestID();
        $requestLookup = $userRequestClass . $userRequestID;
        eGlooLogger::writeLog( eGlooLogger::DEBUG, 'CSVDispatcher: Request lookup "' . $requestLookup . '"');
        
        /**
         * Ensure that there is a request that corresponds to this request class
         * and id, if not, return false.
         */
        if ( !isset( $this->dispatchNodes[ $requestLookup ]) ) {
			$error_message = "CSVDispatcher: Dispatch node not found for request class : '" . $userRequestClass . "' and request ID '" . $userRequestID . "'";
			eGlooLogger::writeLog( eGlooLogger::DEBUG, $error_message );

			if (eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {
				throw new ErrorException($error_message);
			}

			return false;
        }
               
        if ( !isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
            $userAgent = 'Default';
            // TODO skip checks or error out ?
        } else {
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
        }
        
		/**
         * If this is a valid request class/id, get the request denoted 
         * by this request class and id.
         */
        $requestNode = simplexml_load_string( $this->dispatchNodes[ $requestLookup ] );

        $countryCode    = '';
        $languageCode   = '';

        $dispatchPath       = null;
        $localizationNode   = null;

        foreach( $requestNode->xpath( 'child::Localization' ) as $localization ) {
            if ( $countryCode === (string) $localization['countryCode'] ) {
                $localizationNode = $localization;
                
                if ( $languageCode === (string) $localization['languageCode'] ) {
                    $localizationNode = $localization;
                    break;
                }
            }
        }
/*
        if ( $localizationNode !== null ) {
            $dispatchPath = (string) $localizationNode;
        } else {
            // TODO throw exception
        }
*/

        // TODO move this drilling code, along with the code from the CSS and JS Dispatch, into
        // a utility class.  No sense duplicating lines
        if ( $localizationNode !== null ) {
        	if ( (string) $localizationNode['variesOnUserAgent'] === 'true' ) {
		        eGlooLogger::writeLog( eGlooLogger::DEBUG, "CSVDispatcher: Processing Clients" );
        		
        		foreach( $localizationNode->xpath( 'child::Client' ) as $client ) {
		            $matchFormat = (string) $client['matches'];
		            $match = preg_match ( $matchFormat, $userAgent ); 
		
		            if( $match ) {
		                $userClient = $client;
		                break;
		            }
		        }

		        if ( $userClient !== null ) {
		            foreach( $userClient->xpath( 'child::DefaultDispatchMap' ) as $map ) {    
						if ( isset( $map['path'] ) ) {
							$dispatchPath = (string) $map['path'] . '/' . (string) $map;
						} else {
							$dispatchPath = (string) $map;
						}
		            }
		        } else {
		        	// TODO throw exception
		        }
        	} else {
            	$dispatchPath = (string) $localizationNode;
        	}
        } else {
            // TODO throw exception
        }
        
        die_r("lkjasdf");
        $dispatchPath = $this->DISPATCH_XML_LOCATION . $this->application . '/InterfaceBundles/' . 
            $this->interfaceBundle . '/CSV/' . (string) $requestNode['path'] . '/' . $dispatchPath;

		$dispatchPath = trim( $dispatchPath );

		if ( $dispatchPath === '' ) {
			$error_message = "CSVDispatcher: Dispatch path did not match for request class : '" . $userRequestClass . "' and request ID '" . $userRequestID . "'";
			eGlooLogger::writeLog( eGlooLogger::DEBUG, $error_message );

			if (eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {
				throw new ErrorException($error_message);
			}

			return false;
		}

        return $dispatchPath;
    }

}