<?php
/**
 * JavascriptDispatcher Class File
 *
 * Contains the class definition for the JavascriptDispatcher, a final
 * class responsible for dispatching javascript requests to the appropriate
 * javascript template file for parsing.
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
 * JavascriptDispatcher
 * 
 * Provides a class definition for the JavascriptDispatcher.
 *
 * @package Template
 */
class JavascriptDispatcher extends TemplateDispatcher {

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
     * It then populates a hash of [JavascriptDispatcher] -> [JavascriptDispatch XML Object]
     */
    protected function loadDispatchNodes(){
        eGlooLogger::writeLog( eGlooLogger::$DEBUG, "JavascriptDispatcher: Processing XML" );

        //read the xml onces... global location to do this... it looks like it does this once per request.
        $requestXMLObject = simplexml_load_file( $this->DISPATCH_XML_LOCATION . 
            $this->application . '/InterfaceBundles/' . $this->interfaceBundle . '/Javascript/Dispatch.xml'  );

        foreach( $requestXMLObject->xpath( '/eGlooJavascript:Clients' ) as $javascriptClients ) {
                $uniqueKey = ( 'JavascriptDispatcher' );
                $this->dispatchNodes[ $uniqueKey  ] = $javascriptClients->asXML();
        }
    }

    /**
     * returns the singleton of this class
     */
    public static function getInstance( $application, $interfaceBundle ) {
        if ( !isset(self::$singletonDispatcher) ) {
            $cacheGateway = CacheGateway::getCacheGateway();
            
            if ( (self::$singletonDispatcher = $cacheGateway->getObject( 'JavascriptDispatcherNodes', '<type>' ) ) == null ) {
                eGlooLogger::writeLog( eGlooLogger::$DEBUG, "JavascriptDispatcher: Building Singleton" );
                self::$singletonDispatcher = new JavascriptDispatcher( $application, $interfaceBundle );
                $cacheGateway->storeObject( 'JavascriptDispatcherNodes', self::$singletonDispatcher, '<type>' );
            } else {
                eGlooLogger::writeLog( eGlooLogger::$DEBUG, "JavascriptDispatcher: Singleton pulled from cache" );
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
        if ( !isset( $this->dispatchNodes[ 'JavascriptDispatcher' ]) ){
           eGlooLogger::writeLog( eGlooLogger::$DEBUG, "JavascriptDispatcher: Dispatch Nodes unset" );
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
        $javascriptClients = simplexml_load_string( $this->dispatchNodes[ 'JavascriptDispatcher' ] );

        $userClient = null;
        $userMajorVersion = null;
        $userMinorVersion = null;
        $userPlatform = null;
        $dispatchPath = null;
        
        foreach( $javascriptClients->xpath( 'child::Client' ) as $client ) {
            $matchFormat = (string) $client['matches'];
            $match = preg_match ( $matchFormat, $userAgent ); 
            
            if( $match ) {
                eGlooLogger::writeLog( eGlooLogger::$DEBUG, 'JavascriptDispatcher: Matched ' . (string) $client['id']);
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
            foreach( $javascriptClients->xpath( 'Client[@id=\'Default\']/child::DefaultDispatchMap/child::DispatchMap' ) as $map ) {
                if( $userRequestID === (string) $map['id'] ) {
                    $dispatchPath = (string) $map;
                    break;
                }
            }
        } else {
            // TODO throw exception
        }

        return trim( $dispatchPath );
    }

}
