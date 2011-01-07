<?php
/**
 * GetNewCubeElementInstanceRequestProcessor Class File
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
 * @copyright 2011 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package RequestProcessing
 * @version 1.0
 */

/**
 * GetNewCubeElementInstanceRequestProcessor
 * 
 * Needs to be commented
 * 
 * @package RequestProcessing
 * @subpackage RequestProcessors
 */
class GetNewCubeElementInstanceRequestProcessor extends RequestProcessor {

    private static $CUBE_ID_ATTRIBUTE = 'cubeID';
    
    public function processRequest() {

		//get the cube id
        $cubeTypeID = $this->requestInfoBean->getGET( self::$CUBE_ID_ATTRIBUTE );

        $profileID = $_SESSION['MAIN_PROFILE_ID'];

       	$elementInstanceID = null;
       	$cubeDTO = new CubeDTO(); 

        $cubeDTO->createNewInstance( $profileID,  $cubeTypeID );
            
		$templateEngine = new CubeDefaultTemplateEngine( 'dev', 'us' );
        $templateEngine->assign( 'cubeElementInstanceID', $cubeDTO->getElementInstanceID());
        $templateEngine->assign( 'cubeElementTypeID', $cubeDTO->getElementTypeID() );
        $templateEngine->assign( 'cubeElementName', $cubeDTO->getCubeName() );

        header("Content-type: application/xml; charset=UTF-8");
        
		echo $templateEngine->fetch( $cubeDTO->getCubeElementXMLHTTPRequest() );

    }
}

?>
