<?php
/**
 * XHTMLXML2ArrayDispatcher Class File
 *
 * $file_block_description
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
 * @author George Cooper
 * @copyright 2011 eGloo, LLC
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
				$dispatchPathPrepend = (string) $request['path'];

				$dispatches[$requestClassString][$requestIDString] = array('Localizations' => array(), 'path' => $dispatchPathPrepend);

				foreach( $request->xpath( 'child::Localization' ) as $localization ) {
					$newLocalization = array();

					$newLocalization['countryCode'] = (string) $localization['countryCode'];
					$newLocalization['languageCode'] = (string) $localization['languageCode'];
					$newLocalization['variesOnUserAgent'] = (string) $localization['variesOnUserAgent'] === 'true' ? true : false;

					if ( $newLocalization['variesOnUserAgent'] ) {
						$newLocalization['Clients'] = array();

						foreach( $localization->xpath( 'child::Client' ) as $client ) {
							$defaultDispatchMapArray = $client->xpath( 'child::DefaultDispatchMap' );

							$clientPath = isset($client['path']) && trim( (string) $client['path'] ) !== '' ? $client['path'] : null;

							$newClient = array(
								'id' => (string) $client['id'],
								'matches' => (string) $client['matches'],
								'path' => $clientPath
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
						$newLocalization['dispatchPath'] = (string) $localization;
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
	public function dispatch( $requestInfoBean, $userRequestIDOverride = null, $userRequestClassOverride = null ) {
		$userRequestClass = $userRequestClassOverride !== null ? $userRequestClassOverride : $requestInfoBean->getRequestClass();
		$userRequestID = $userRequestIDOverride !== null ? $userRequestIDOverride : $requestInfoBean->getRequestID();
		$requestLookup = $userRequestClass . $userRequestID;

		// TODO only if not cache
		$dispatchCacheRegionHandler = CacheManagementDirector::getCacheRegionHandler('Dispatches');
		$nodeCacheID = eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'XHTMLXML2ArrayDispatcherNodes';
		
		if ( ($this->dispatchNodes = $dispatchCacheRegionHandler->getObject( $nodeCacheID, 'Dispatching', true ) ) == null ) {
			eGlooLogger::writeLog( eGlooLogger::DEBUG, "XHTMLXML2ArrayDispatcher: Dispatch Nodes pulled from cache" );
			$this->loadDispatchNodes();
			$dispatchCacheRegionHandler->storeObject( $nodeCacheID, $this->dispatchNodes, 'Dispatching', 0, true );
		} else {
			eGlooLogger::writeLog( eGlooLogger::DEBUG, "XHTMLXML2ArrayDispatcher: Dispatch Nodes pulled from cache" );
		}

		eGlooLogger::writeLog( eGlooLogger::DEBUG, 'XHTMLXML2ArrayDispatcher: Request lookup "' . $requestLookup . '"');

		/**
		 * Ensure that there is a request that corresponds to this request class
		 * and id, if not, return false.
		 */
		if ( !isset( $this->dispatchNodes[ $requestLookup ]) ) {
			$error_message = 'XHTMLXML2ArrayDispatcher: Dispatch node not found for request class : "' . $userRequestClass .
				'" and request ID "' . $userRequestID . '".  Please review your XHTML Dispatch.xml';
			eGlooLogger::writeLog( eGlooLogger::DEBUG, $error_message );

			if (eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {
				throw new ErrorException($error_message);
			}

			return false;
		}

		$userAgent = eGlooHTTPRequest::getUserAgent();

		/**
		 * If this is a valid request class/id, get the request denoted 
		 * by this request class and id.
		 */
		$requestNode = $this->dispatchNodes[ $requestLookup ];

		$countryCode	= '';
		$languageCode	= '';

		$dispatchPath		= null;
		$localizationNode	= null;

		$dispatchPathPrepend = isset($requestNode[$userRequestClass][$userRequestID]['path']) ? $requestNode[$userRequestClass][$userRequestID]['path'] . '/' : '';

		foreach( $requestNode[$userRequestClass][$userRequestID]['Localizations'] as $localization ) {
			if ( $countryCode === $localization['countryCode'] ) {
				$localizationNode = $localization;

				if ( $languageCode === $localization['languageCode'] ) {
					$localizationNode = $localization;
					break;
				}
			}
		}

		// die_r($localizationNode);

		// TODO move this drilling code, along with the code from the CSS and JS Dispatch, into
		// a utility class.	 No sense duplicating lines
		if ( $localizationNode !== null ) {
			if ( $localizationNode['variesOnUserAgent'] ) {
				eGlooLogger::writeLog( eGlooLogger::DEBUG, "XHTMLXML2ArrayDispatcher: Processing Clients" );

				$userClient = null;

				foreach( $localizationNode['Clients'] as $client ) {
					$matchFormat = $client['matches'];
					$match = preg_match ( $matchFormat, $userAgent ); 
		
					if( $match ) {
						$userClient = $client;
						break;
					}
				}

				if ( $userClient !== null ) {
					if ( isset($userClient['defaultDispatchMap']) ) {
						if ( isset( $userClient['path'] ) ) {
							$dispatchPath = $dispatchPathPrepend . $userClient['path'] . '/' . $userClient['defaultDispatchMap'];
						} else {
							$dispatchPath = $dispatchPathPrepend . $userClient['defaultDispatchMap'];
						}
					}
				} else {
					// TODO throw exception
				}
			} else {
				$dispatchPath = $dispatchPathPrepend . $localizationNode['dispatchPath'];
			}
		} else {
			// TODO throw exception
		}

		$dispatchPath = trim( $dispatchPath );

		if ( $dispatchPath === '' ) {
			$error_message = "XHTMLXML2ArrayDispatcher: Dispatch path did not match for request class : '" . $userRequestClass . "' and request ID '" . $userRequestID . "'";
			eGlooLogger::writeLog( eGlooLogger::DEBUG, $error_message );

			throw new ErrorException($error_message);
		} else {
			$dispatchPath = eGlooConfiguration::getApplicationsPath() . '/' . $this->application . '/InterfaceBundles/' . 
				$this->interfaceBundle . '/XHTML/' . $dispatchPath;
		}

		return $dispatchPath;
	}

}
