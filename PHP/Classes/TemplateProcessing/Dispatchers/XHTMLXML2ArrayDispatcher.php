<?php
/**
 * XHTMLXML2ArrayDispatcher Class File
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
 * XHTMLXML2ArrayDispatcher
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class XHTMLXML2ArrayDispatcher extends TemplateDispatcher {

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
	}

	/**
	 * This method reads the xml file from disk into a document object model.
	 * It then populates a hash of [XHTMLXML2ArrayDispatcher] -> [XHTMLDispatch XML Object]
	 */
	protected function loadDispatchNodes(){
		$this->DISPATCH_XML_LOCATION = eGlooConfiguration::getApplicationsPath() . '/';

		eGlooLogger::writeLog( eGlooLogger::DEBUG, "XHTMLXML2ArrayDispatcher: Processing XML" );

		//read the xml onces... global location to do this... it looks like it does this once per request.
		$requestXMLObject = simplexml_load_file( $this->DISPATCH_XML_LOCATION . 
			$this->application . '/InterfaceBundles/' . $this->interfaceBundle . '/XHTML/Dispatch.xml'	);

		$dispatches = array();

		foreach( $requestXMLObject->xpath( '/eGlooXHTML:Requests/RequestClass' ) as $requestClass ) {
			$requestClassString = (string) $requestClass['id'];
			$dispatches[$requestClassString] = array();

			foreach( $requestClass->xpath( 'child::Request' ) as $request ) {
				$requestIDString = (string) $request['id'];
				$dispatches[$requestClassString][$requestIDString] = array('Localizations' => array());

				foreach( $request->xpath( 'child::Localization' ) as $localization ) {
					$newLocalization = array();

					$newLocalization['countryCode'] = (string) $localization['countryCode'];
					$newLocalization['languageCode'] = (string) $localization['languageCode'];
					$newLocalization['variesOnUserAgent'] = (string) $localization['variesOnUserAgent'] === 'true' ? true : false;

					if ( $newLocalization['variesOnUserAgent'] ) {
						$newLocalization['Clients'] = array();

						foreach( $localization->xpath( 'child::Client' ) as $client ) {
							$defaultDispatchMapArray = $client->xpath( 'child::DefaultDispatchMap' );

							$newClient = array(
								'id' => (string) $client['id'],
								'matches' => (string) $client['matches'],
								'path' => (string) $client['path']
							);

							if (empty($defaultDispatchMapArray)) {
								$newClient['dispatch'] = trim( (string) $client );
							} else {
								foreach( $defaultDispatchMapArray as $map ) {
									if ( isset( $map['path'] ) ) {
										$defaultDispatchMap = (string) $map['path'] . '/' . (string) $map;
									} else {
										$defaultDispatchMap = (string) $map;
									}
									
									$newClient['defaultDispatchMap'] = trim( $defaultDispatchMap );
								}
							}

							$newLocalization['Clients'][] = $newClient;
						}
					} else {
						$newLocalization['dispatchPath'] = (string) $localizationNode;
					}

					$dispatches[$requestClassString][$requestIDString]['Localizations'][] = $newLocalization;
				}

				$uniqueKey = ( (string) $requestClass['id'] ) . ( (string) $request['id']  );
				$this->dispatchNodes[ $uniqueKey  ] = $dispatches;
			}
		}
	}

	/**
	 * returns the singleton of this class
	 */
	public static function getInstance( $application, $interfaceBundle ) {
		if ( !isset(self::$singletonDispatcher) ) {
			eGlooLogger::writeLog( eGlooLogger::DEBUG, "XHTMLXML2ArrayDispatcher: Building Singleton" );
			self::$singletonDispatcher = new XHTMLXML2ArrayDispatcher( $application, $interfaceBundle );
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

		// TODO only if not cache
		$dispatchCacheRegionHandler = CacheManagementDirector::getCacheRegionHandler('Dispatches');
		$nodeCacheID = eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'XHTMLXML2ArrayDispatcherNodes';
		
		if ( ($this->dispatchNodes = $dispatchCacheRegionHandler->getObject( $nodeCacheID, 'ContentDispatching' ) ) == null ) {
			eGlooLogger::writeLog( eGlooLogger::DEBUG, "XHTMLXML2ArrayDispatcher: Dispatch Nodes pulled from cache" );
			$this->loadDispatchNodes();
			$dispatchCacheRegionHandler->storeObject( $nodeCacheID, $this->dispatchNodes, 'ContentDispatching' );
		} else {
			eGlooLogger::writeLog( eGlooLogger::DEBUG, "XHTMLXML2ArrayDispatcher: Dispatch Nodes pulled from cache" );
		}

		die_r($this->dispatchNodes);

		eGlooLogger::writeLog( eGlooLogger::DEBUG, 'XHTMLXML2ArrayDispatcher: Request lookup "' . $requestLookup . '"');

		/**
		 * Ensure that there is a request that corresponds to this request class
		 * and id, if not, return false.
		 */
		if ( !isset( $this->dispatchNodes[ $requestLookup ]) ) {
			$error_message = "XHTMLXML2ArrayDispatcher: Dispatch node not found for request class : '" . $userRequestClass . "' and request ID '" . $userRequestID . "'";
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

		$countryCode	= '';
		$languageCode	= '';

		$dispatchPath		= null;
		$localizationNode	= null;

		foreach( $requestNode->xpath( 'child::Localization' ) as $localization ) {
			if ( $countryCode === (string) $localization['countryCode'] ) {
				$localizationNode = $localization;
				
				if ( $languageCode === (string) $localization['languageCode'] ) {
					$localizationNode = $localization;
					break;
				}
			}
		}

		// TODO move this drilling code, along with the code from the CSS and JS Dispatch, into
		// a utility class.	 No sense duplicating lines
		if ( $localizationNode !== null ) {
			if ( (string) $localizationNode['variesOnUserAgent'] === 'true' ) {
				eGlooLogger::writeLog( eGlooLogger::DEBUG, "XHTMLXML2ArrayDispatcher: Processing Clients" );
				
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

		$dispatchPath = $this->DISPATCH_XML_LOCATION . $this->application . '/InterfaceBundles/' . 
			$this->interfaceBundle . '/XHTML/' . (string) $requestNode['path'] . '/' . $dispatchPath;

		$dispatchPath = trim( $dispatchPath );

		if ( $dispatchPath === '' ) {
			$error_message = "XHTMLXML2ArrayDispatcher: Dispatch path did not match for request class : '" . $userRequestClass . "' and request ID '" . $userRequestID . "'";
			eGlooLogger::writeLog( eGlooLogger::DEBUG, $error_message );

			if (eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {
				throw new ErrorException($error_message);
			}

			return false;
		}

		return $dispatchPath;
	}

}
