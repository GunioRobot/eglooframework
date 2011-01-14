<?php
/**
 * CubeContentProcessor Class File
 *
 * Needs to be commented
 * 
 * Copyright 2011 eGloo, LLC
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
 * @copyright 2011 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package ContentProcessing
 * @version 1.0
 */

/**
 * CubeContentProcessor
 * 
 * Needs to be commented
 *
 * @package ContentProcessing
 */
class CubeContentProcessor extends ContentProcessor {
    
    private $_cubeDTO = null;
    private $_view = null;
    
    public function __construct( $cubeDTO, $view ) {
		$this->_cubeDTO = $cubeDTO;
		$this->_view = $view;
    }
    
    public function prepareContent() {
    	
    	if( $this->_view === 'ContentViewContent'){
    		$this->prepareContentViewContent();
    	} else if ( $this->_view === 'PreferencesViewContent' ){
    		$this->preparePreferencesViewContent();
    	}	

    }
    
    private function prepareContentViewContent(){
    	//get the requestQueryMapper.xml
    	$xmlPath = $this->_cubeDTO->getDirectoryLocation() . "/xml/RequestQueryMapper.xml";
    	$documentRoot = $this->loadXML($xmlPath);
       	$contentViewQueryNodes = $documentRoot->xpath("/Mappings/Request[@name='ContentViewInit'][1]/Query");
       	$contentViewQueryNode = $contentViewQueryNodes[0];
       	
       	$queryName = (string) $contentViewQueryNode['name'];
       	$queryType = (string) $contentViewQueryNode['type'];
       	
 		if( $queryType === "System" ){
 			$cubeContentStrategy = eval( "return new $queryName();");
 			$cubeContentStrategy->setCubeDTO( $this->_cubeDTO );
 			$cubeContentStrategy->setTemplateEngine( $this->_templateEngine );
 			$cubeContentStrategy->prepareContentViewContent();
 		} else if( $queryType === "PLSelect" ){
 			$cubeContentStrategy = new PLFunctionCubeContentStrategy();
 			$cubeContentStrategy->setCubeDTO( $this->_cubeDTO );
 			$cubeContentStrategy->setTemplateEngine( $this->_templateEngine );
 			$cubeContentStrategy->setQueryFunctionName( $queryName );
 			$cubeContentStrategy->prepareContentViewContent();
 			
 		}
       	
       	eGlooLogger::writeLog( eGlooLogger::DEBUG, "k3b: " . $contentViewQueryNode['name']  );
	}
    
    
    private function preparePreferencesViewContent(){
    	$xmlPath = $this->_cubeDTO->getDirectoryLocation() . "/xml/RequestQueryMapper.xml";
    	$documentRoot = $this->loadXML($xmlPath);
       	$contentViewQueryNodes = $documentRoot->xpath("/Mappings/Request[@name='PreferencesViewInit'][1]/Query");
       	$contentViewQueryNode = $contentViewQueryNodes[0];
       	
       	$queryName = (string) $contentViewQueryNode['name'];
       	$queryType = (string) $contentViewQueryNode['type'];
       	
 		if( $queryType === "System" ){
 			$cubeContentStrategy = eval( "return new $queryName();");
 			$cubeContentStrategy->setCubeDTO( $this->_cubeDTO );
 			$cubeContentStrategy->setTemplateEngine( $this->_templateEngine );
 			$cubeContentStrategy->preparePreferencesViewContent();
 		} 
    	
    }
    
    
    private function loadXML($path){
    	$requestXMLObject = null;
    	
    	/**
		 * check the cache gateway for this xml
		 */
		$cacheGateway = CacheGateway::getCacheGateway();
        
        if ( ( $requestXMLObject = $cacheGateway->getObject( $path, 'Cubes' ) ) == null ) {

			eGlooLogger::writeLog( eGlooLogger::DEBUG, "CubeContentProcessor::loadXML -  $path, has not been loaded yet, reading the xml" );

  			/**
			 *  It's not in the cache... read in the file, asXML makes it cacheable
			 *  TODO error checking
			 */	
			$requestXMLObject = simplexml_load_file( $path )->asXML(); 	
			
			$cacheGateway->storeObject( $path, $requestXMLObject, 'Cubes' );
			
        } else {
			eGlooLogger::writeLog( eGlooLogger::DEBUG, "CubeContentProcessor::loadXML - $path Grabbed from cache!" );
        }	
    	
    	return simplexml_load_string( $requestXMLObject );
    	
    } 
    
}
 
?>
