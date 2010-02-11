<?php
/**
 * StyleSheetDispatcher Class File
 *
 * Contains the class definition for the StyleSheetDispatcher, a final
 * class responsible for dispatching style sheet requests to the appropriate
 * style sheet template file for parsing.
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
 * @author George Cooper
 * @copyright 2008 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package Template
 * @version 1.0
 */

/**
 * StyleSheetDispatcher
 * 
 * Provides a class definition for the StyleSheetDispatcher.
 *
 * @package Template
 */
class StyleSheetDispatcher extends TemplateDispatcher {

    /**
     * Static Constants
     */
    private static $singletonDispatcher;
    
    /**
     * XML Variables
     */
    private $DISPATCH_XML_LOCATION = '';
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
     * It then populates a hash of [StyleSheetDispatcher]->[StyleSheetDispatch
     * XML Object]
     */
    protected function loadDispatchNodes(){
        eGlooLogger::writeLog( eGlooLogger::DEBUG, "StyleSheetDispatcher: Processing XML" );

        //read the xml onces... global location to do this... it looks like it does this once per request.
        $requestXMLObject = simplexml_load_file( $this->DISPATCH_XML_LOCATION . 
            $this->application . '/InterfaceBundles/' . $this->interfaceBundle . '/CSS/Dispatch.xml'  );

        foreach( $requestXMLObject->xpath( '/eGlooStyleSheet:Clients' ) as $styleSheetClients ) {
                $uniqueKey = ( 'StyleSheetDispatcher' );
                $this->dispatchNodes[ $uniqueKey  ] = $styleSheetClients->asXML();
        }
    }

    /**
     * returns the singleton of this class
     */
    public static function getInstance( $application, $interfaceBundle ) {
        if ( !isset(self::$singletonDispatcher) ) {
            $cacheGateway = CacheGateway::getCacheGateway();
            
            if ( (self::$singletonDispatcher = $cacheGateway->getObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'StyleSheetDispatcherNodes', '<type>' ) ) == null ) {
                eGlooLogger::writeLog( eGlooLogger::DEBUG, "StyleSheetDispatcher: Building Singleton" );
                self::$singletonDispatcher = new StyleSheetDispatcher( $application, $interfaceBundle );
                $cacheGateway->storeObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'StyleSheetDispatcherNodes', self::$singletonDispatcher, '<type>' );
            } else {
                eGlooLogger::writeLog( eGlooLogger::DEBUG, "StyleSheetDispatcher: Singleton pulled from cache" );
            }
        }
        
        return self::$singletonDispatcher;
    }

    /**
     * Only functional method available to the public.  
     */
    public function dispatch($requestInfoBean) {
    	    	
        /**
         * Ensure that there is a request that corresponds to this request class
         * and id, if not, return false.
         */
        if ( !isset( $this->dispatchNodes[ 'StyleSheetDispatcher' ]) ){
           eGlooLogger::writeLog( eGlooLogger::DEBUG, "StyleSheetDispatcher: Dispatch Nodes unset" );
           return false;
           // TODO throw exception
        }

        if ( !isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
            $userAgent = 'Default';
            // TODO skip checks or error out ?
        } else {
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
        }
        
        $userRequestID = $requestInfoBean->getRequestID();
        
        /**
         * If this is a valid request class/id, get the request denoted 
         * by this request class and id.
         */
        $styleSheetClients = simplexml_load_string( $this->dispatchNodes[ 'StyleSheetDispatcher' ] );

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

//		eGlooLogger::writeLog( eGlooLogger::DEBUG, "DISPATCH CSS PATH: " .  $dispatchPath );
        return trim( $dispatchPath );
    }

}
