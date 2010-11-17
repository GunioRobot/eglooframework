<?php
/**
 * StyleSheetXML2ArrayDispatcher Class File
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
 * StyleSheetXML2ArrayDispatcher
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class StyleSheetXML2ArrayDispatcher extends TemplateDispatcher {

	/**
	 * Static Constants
	 */
	private static $singletonDispatcher;
	
	/**
	 * XML Variables
	 */
	private $DISPATCH_XML_LOCATION = null;
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
	 * It then populates a hash of [StyleSheetXML2ArrayDispatcher]->[StyleSheetDispatch
	 * XML Object]
	 */
	protected function loadDispatchNodes(){
		eGlooLogger::writeLog( eGlooLogger::DEBUG, "StyleSheetXML2ArrayDispatcher: Processing XML" );

		//read the xml onces... global location to do this... it looks like it does this once per request.
		$requestXMLObject = simplexml_load_file( $this->DISPATCH_XML_LOCATION . 
			$this->application . '/InterfaceBundles/' . $this->interfaceBundle . '/CSS/Dispatch.xml'  );

		foreach( $requestXMLObject->xpath( '/eGlooStyleSheet:Clients' ) as $styleSheetClients ) {
				$uniqueKey = ( 'StyleSheetXML2ArrayDispatcher' );
				$this->dispatchNodes[ $uniqueKey  ] = $styleSheetClients->asXML();
		}
	}

	/**
	 * returns the singleton of this class
	 */
	public static function getInstance( $application, $interfaceBundle ) {
		if ( !isset(self::$singletonDispatcher) ) {
			$dispatchCacheRegionHandler = CacheManagementDirector::getCacheRegionHandler('Dispatches');

			if ( (self::$singletonDispatcher = $dispatchCacheRegionHandler->getObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'StyleSheetXML2ArrayDispatcherNodes', 'ContentDispatching' ) ) == null ) {
				eGlooLogger::writeLog( eGlooLogger::DEBUG, "StyleSheetXML2ArrayDispatcher: Building Singleton" );
				self::$singletonDispatcher = new StyleSheetXML2ArrayDispatcher( $application, $interfaceBundle );
				$dispatchCacheRegionHandler->storeObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'StyleSheetXML2ArrayDispatcherNodes', self::$singletonDispatcher, 'ContentDispatching' );
			} else {
				eGlooLogger::writeLog( eGlooLogger::DEBUG, "StyleSheetXML2ArrayDispatcher: Singleton pulled from cache" );
			}
		}
		
		return self::$singletonDispatcher;
	}

	/**
	 * Only functional method available to the public.	
	 */
	public function dispatch($requestInfoBean, $userRequestID = null) {
				
		/**
		 * Ensure that there is a request that corresponds to this request class
		 * and id, if not, return false.
		 */
		if ( !isset( $this->dispatchNodes[ 'StyleSheetXML2ArrayDispatcher' ]) ){
			$error_message = "StyleSheetXML2ArrayDispatcher: Dispatch node missing";
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
		
		$userRequestID = $userRequestID !== null ? $userRequestID : $requestInfoBean->getRequestID();
		
		/**
		 * If this is a valid request class/id, get the request denoted 
		 * by this request class and id.
		 */
		$styleSheetClients = simplexml_load_string( $this->dispatchNodes[ 'StyleSheetXML2ArrayDispatcher' ] );

		$userClient = null;
		$userMajorVersion = null;
		$userMinorVersion = null;
		$userPlatform = null;
		$dispatchPath = null;
		
		foreach( $styleSheetClients->xpath( 'child::Client' ) as $client ) {
			$matchFormat = (string) $client['matches'];
			$match = preg_match ( $matchFormat, $userAgent ); 

			if( $match ) {
				$userClient = $client;
				break;
			}
		}

		if ( $userClient !== null ) {
			foreach( $userClient->xpath( 'child::MajorVersion' ) as $majorVersion ) {
				$matchFormat = (string) $majorVersion['matches'];
				$match = preg_match ( $matchFormat, $userAgent ); 
	
				if( $match ) {
					$userMajorVersion = $majorVersion;
					break;
				}
			}
		}

		if ( $userMajorVersion !== null ) {
			foreach( $userMajorVersion->xpath( 'child::MinorVersion' ) as $minorVersion ) {
				$matchFormat = (string) $minorVersion['matches'];
				$match = preg_match ( $matchFormat, $userAgent ); 
	
				if( $match ) {
					$userMinorVersion = $minorVersion;
					break;
				}
			}
		}

		if ( $userMinorVersion !== null ) {
			foreach( $userMinorVersion->xpath( 'child::Platform' ) as $platform ) {	   
				if( $userAgent === (string) $platform->UserAgent ) {
					$userPlatform = $platform;
					break;
				}
			}
		}

		if ( $userPlatform !== null ) {
			foreach( $userPlatform->xpath( 'child::DispatchMap' ) as $map ) {	 
				if( $userRequestID === (string) $map['id'] ) {
					$dispatchPath = (string) $map;
					break;
				}
			}
		}
   
		if ( $dispatchPath === null && $userMinorVersion !== null ) {
			foreach( $userMinorVersion->xpath( 'child::DefaultDispatchMap/child::DispatchMap' ) as $map ) {
				if( $userRequestID === (string) $map['id'] ) {
					$dispatchPath = (string) $map;
					break;
				}
			}			 
		}
	  
		if ( $dispatchPath === null && $userMajorVersion !== null ) {
			foreach( $userMajorVersion->xpath( 'child::DefaultDispatchMap/child::DispatchMap' ) as $map ) {
				if( $userRequestID === (string) $map['id'] ) {
					$dispatchPath = (string) $map;
					break;
				}
			}			 
		}

		if ( $dispatchPath === null && $userClient !== null ) {
			foreach( $userClient->xpath( 'child::DefaultDispatchMap/child::DispatchMap' ) as $map ) {
				if( $userRequestID === (string) $map['id'] ) {
					$dispatchPath = (string) $map;
					break;
				}
			}
		}

		if ( $dispatchPath === null ) {
			foreach( $styleSheetClients->xpath( 'Client[@id=\'Default\']/child::DefaultDispatchMap/child::DispatchMap' ) as $map ) {
				if( $userRequestID === (string) $map['id'] ) {
					$dispatchPath = (string) $map;
					break;
				}
			}
		} else {
			// TODO throw exception
		}

		$dispatchPath = trim( $dispatchPath );

		if ( $dispatchPath === '' ) {
			$error_message = "StyleSheetXML2ArrayDispatcher: Dispatch node not found for '" . $userRequestID . ".css'" ;
			eGlooLogger::writeLog( eGlooLogger::DEBUG, $error_message );

			if (eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {
				throw new ErrorException($error_message);
			}

			return false;
		}

		return $dispatchPath;
	}

}
