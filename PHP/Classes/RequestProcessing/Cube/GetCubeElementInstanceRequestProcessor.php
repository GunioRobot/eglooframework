<?php
/**
 * GetCubeElementInstanceRequestProcessor Class File
 *
 * Needs to be commented
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
 * @copyright 2008 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package RequestProcessing
 * @version 1.0
 */

/**
 * GetCubeElementInstanceRequestProcessor
 * 
 * Needs to be commented
 * 
 * @package RequestProcessing
 * @subpackage RequestProcessors
 */
class GetCubeElementInstanceRequestProcessor extends RequestProcessor {

    private static $CUBE_ID_ATTRIBUTE = 'cubeID';
    
    public function processRequest() {
        // TODO refactor
        $cacheGateway = CacheGateway::getCacheGateway();

        // BUG Will break if not previously cached

        $cubeInstanceID = $this->requestInfoBean->getGET( self::$CUBE_ID_ATTRIBUTE );

        $profileID = $_SESSION['MAIN_PROFILE_ID'];

//        //check cache first
//        if( ($cubeDTO = $cacheGateway->getObject( $cubeID, '<type>' ) ) == null ) {            
//            eGlooLogger::writeLog( eGlooLogger::DEBUG, "building new cube "  . $cubeID );
//            /**
//             * 1) get dto from database
//             * 2) check permissions against userBean
//             *      - if invalid permissions throw exception
//             * 3) create object
//             * 4) cache it
//             * 5) return it
//             */
             
			$cubeDTO = new CubeDTO(); 
			$cubeDTO->getCubeInstance( $cubeInstanceID, $profileID );
            
	        $templateEngine = new CubeTemplateEngine( 'dev', 'us' );
	        $templateEngine->assign( 'cubeElementInstanceID', $cubeDTO->getElementInstanceID());
	        $templateEngine->assign( 'cubeElementTypeID', $cubeDTO->getElementTypeID() );
	        $templateEngine->assign( 'cubeElementName', $cubeDTO->getCubeName() );
	        
        // TODO check permissions

        header("Content-type: application/xml; charset=UTF-8");
       	echo  $templateEngine->fetch(  $cubeDTO->getCubeElementXMLHTTPRequest() );

    }
}

?>
