<?php
/**
 * JavascriptXML2ArrayDispatcher Class File
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
 * JavascriptXML2ArrayDispatcher
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class JavascriptXML2ArrayDispatcher extends TemplateDispatcher {

	/**
	 * Static Constants
	 */
	private static $singletonDispatcher;

	/**
	 * XML Variables
	 */
	private $DISPATCH_XML_LOCATION = null;
	private $dispatchNodes = null;

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
	 * It then populates a hash of [JavascriptXML2ArrayDispatcher] -> [JavascriptDispatch XML Object]
	 */
	protected function loadDispatchNodes(){
		eGlooLogger::writeLog( eGlooLogger::DEBUG, "JavascriptXML2ArrayDispatcher: Processing XML" );

		//read the xml onces... global location to do this... it looks like it does this once per request.
		$requestXMLObject = simplexml_load_file( eGlooConfiguration::getApplicationsPath() . '/' . 
			$this->application . '/InterfaceBundles/' . $this->interfaceBundle . '/Javascript/Dispatch.xml'	 );

		$dispatches = array();

		foreach( $requestXMLObject->xpath( '/eGlooJavascript:Clients' ) as $javascriptClients ) {
			$newDispatch = array('Clients' => array());

			foreach( $javascriptClients->xpath( 'child::Client' ) as $client ) {
				$newClient = array( 'id' => (string) $client['id'], 'matches' => (string) $client['matches'] );

				$majorVersions = array();

				foreach( $client->xpath( 'child::MajorVersion' ) as $majorVersion ) {
					$newMajorVersion = array( 'id' => (string) $majorVersion['id'], 'matches' => (string) $majorVersion['matches'] );

					$minorVersions = array();

					foreach( $majorVersion->xpath( 'child::MinorVersion' ) as $minorVersion ) {
						$newMinorVersion = array( 'id' => (string) $minorVersion['id'], 'matches' => (string) $minorVersion['matches'] );

						$platforms = array();

						foreach( $minorVersion->xpath( 'child::Platform' ) as $platform ) {
							$newPlatform = array( 'id' => (string) $platform['id'], 'matches' => (string) $platform['matches'], 'userAgent' => trim((string) $platform->UserAgent) );
							$newPlatform['DispatchMaps'] = array();

							foreach( $platform->xpath( 'child::DispatchMap' ) as $map ) {
								$newDispatchMap = array( 'id' => (string) $map['id'], 'process' => $this->shouldProcess((string) $map['process']), 'dispatchPath' => trim((string) $map) );
								$newPlatform['DispatchMaps'][$newDispatchMap['id']] = $newDispatchMap;
							}

							$platforms[$newPlatform['id']] = $newPlatform;
						}

						$newMinorVersion['Platforms'] = $platforms;

						$newMinorVersion['DispatchMaps'] = array();

						foreach( $minorVersion->xpath( 'child::DefaultDispatchMap/child::DispatchMap' ) as $map ) {
							$newDispatchMap = array( 'id' => (string) $map['id'], 'process' => $this->shouldProcess((string) $map['process']), 'dispatchPath' => trim((string) $map) );
							$newMinorVersion['DispatchMaps'][$newDispatchMap['id']] = $newDispatchMap;
						}

						$minorVersions[$newMinorVersion['id']] = $newMinorVersion;
					}

					$newMajorVersion['MinorVersions'] = $minorVersions;

					$newMajorVersion['DispatchMaps'] = array();

					foreach( $majorVersion->xpath( 'child::DefaultDispatchMap/child::DispatchMap' ) as $map ) {
						$newDispatchMap = array( 'id' => (string) $map['id'], 'process' => $this->shouldProcess((string) $map['process']), 'dispatchPath' => trim((string) $map) );
						$newMajorVersion['DispatchMaps'][$newDispatchMap['id']] = $newDispatchMap;
					}

					$majorVersions[$newMajorVersion['id']] = $newMajorVersion;
				}

				$newClient['MajorVersions'] = $majorVersions;

				$newClient['DispatchMaps'] = array();

				foreach( $client->xpath( 'child::DefaultDispatchMap/child::DispatchMap' ) as $map ) {
					$newDispatchMap = array( 'id' => (string) $map['id'], 'process' => $this->shouldProcess((string) $map['process']), 'dispatchPath' => trim((string) $map) );
					$newClient['DispatchMaps'][$newDispatchMap['id']] = $newDispatchMap;
				}

				$newDispatch['Clients'][$newClient['id']] = $newClient;
			}

			$dispatches = $newDispatch;
		}

		$this->dispatchNodes = $dispatches;
	}

	/**
	 * returns the singleton of this class
	 */
	public static function getInstance( $application, $interfaceBundle ) {
		if ( !isset(self::$singletonDispatcher) ) {
			eGlooLogger::writeLog( eGlooLogger::DEBUG, "JavascriptXML2ArrayDispatcher: Building Singleton" );
			self::$singletonDispatcher = new JavascriptXML2ArrayDispatcher( $application, $interfaceBundle );
		}

		return self::$singletonDispatcher;
	}

	/**
	 * Only functional method available to the public.	
	 */
	public function dispatch( $requestInfoBean, $userRequestID = null, $userRequestClass = null ) {
		// TODO only if not cache
		$dispatchCacheRegionHandler = CacheManagementDirector::getCacheRegionHandler('Dispatches');
		$nodeCacheID = eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'JavascriptXML2ArrayDispatcher';
		
		if ( ($this->dispatchNodes = $dispatchCacheRegionHandler->getObject( $nodeCacheID, 'Dispatching', true ) ) == null ) {
			eGlooLogger::writeLog( eGlooLogger::DEBUG, "JavascriptXML2ArrayDispatcher: Dispatch Nodes pulled from cache" );
			$this->loadDispatchNodes();
			$dispatchCacheRegionHandler->storeObject( $nodeCacheID, $this->dispatchNodes, 'Dispatching', 0, true );
		} else {
			eGlooLogger::writeLog( eGlooLogger::DEBUG, "JavascriptXML2ArrayDispatcher: Dispatch Nodes pulled from cache" );
		}

		$userAgent = eGlooHTTPRequest::getUserAgent();

		$userRequestID = $userRequestID !== null ? $userRequestID : $requestInfoBean->getRequestID();

		/**
		 * If this is a valid request class/id, get the request denoted 
		 * by this request class and id.
		 */
		$javascriptClients = $this->dispatchNodes;

		$userClient = null;
		$userMajorVersion = null;
		$userMinorVersion = null;
		$userPlatform = null;
		$dispatchPath = null;
		$processTemplate = false;
		
		foreach( $javascriptClients['Clients'] as $clientID => $client ) {
			$matchFormat = (string) $client['matches'];
			$match = preg_match ( $matchFormat, $userAgent ); 
			
			if( $match ) {
				eGlooLogger::writeLog( eGlooLogger::DEBUG, 'JavascriptXML2ArrayDispatcher: Matched ' . (string) $client['id']);
				$userClient = $client;
				break;
			}
		}

		if ( $userClient !== null ) {
			foreach( $userClient['MajorVersions'] as $majorVersion ) {
				$matchFormat = (string) $majorVersion['matches'];
				$match = preg_match ( $matchFormat, $userAgent ); 
	
				if( $match ) {
					$userMajorVersion = $majorVersion;
					break;
				}
			}
		}

		if ( $userMajorVersion !== null ) {
			foreach( $userMajorVersion['MinorVersions'] as $minorVersion ) {
				$matchFormat = (string) $minorVersion['matches'];
				$match = preg_match ( $matchFormat, $userAgent ); 
	
				if( $match ) {
					$userMinorVersion = $minorVersion;
					break;
				}
			}
		}

		if ( $userMinorVersion !== null ) {
			foreach( $userMinorVersion['Platforms'] as $platform ) {	   
				if( $userAgent === (string) $platform->UserAgent ) {
					$userPlatform = $platform;
					break;
				}
			}
		}

		if ( $userPlatform !== null ) {
			foreach( $userPlatform['DispatchMaps'] as $map ) {	 
				if( $userRequestID === (string) $map['id'] ) {
					$dispatchPath = (string) $map['dispatchPath'];
					$processTemplate = $map['process'];
					break;
				}
			}
		}

		if ( $dispatchPath === null && $userMinorVersion !== null ) {
			foreach( $userMinorVersion['DispatchMaps'] as $map ) {
				if( $userRequestID === (string) $map['id'] ) {
					$dispatchPath = (string) $map['dispatchPath'];
					$processTemplate = $map['process'];
					break;
				}
			}
		}

		if ( $dispatchPath === null && $userMajorVersion !== null ) {
			foreach( $userMajorVersion['DispatchMaps'] as $map ) {
				if( $userRequestID === (string) $map['id'] ) {
					$dispatchPath = (string) $map['dispatchPath'];
					$processTemplate = $map['process'];
					break;
				}
			}
		}

		if ( $dispatchPath === null && $userClient !== null ) {
			foreach( $userClient['DispatchMaps'] as $map ) {
				if( $userRequestID === (string) $map['id'] ) {
					$dispatchPath = (string) $map['dispatchPath'];
					$processTemplate = $map['process'];
					break;
				}
			}
		}

		if ( !$dispatchPath || $dispatchPath === '' ) {
			$error_message = 'JavascriptXML2ArrayDispatcher: Dispatch node not found for "' . $userRequestID . '.js".  ' .
				'Please review your Javascript Dispatch.xml';
			eGlooLogger::writeLog( eGlooLogger::DEBUG, $error_message );

			throw new ErrorException($error_message);
		}

		$this->dispatchPath = $dispatchPath;
		$this->processTemplate = $processTemplate;

		return $dispatchPath;
	}

	// Convenience method to convert process value of true/false as string to type bool
	private function shouldProcess( $stringValue ) {
		$retVal = false;

		switch(trim(strtolower($stringValue))) {
			case 'true' :
				$retVal = true;
				break;
			case 'false' :
			default :
				$retVal = false;
				break;
		}

		return $retVal;
	}

}
