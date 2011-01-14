<?php
/**
 * DynamicCubeContentRequestProcessor Class File
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
 * @package RequestProcessing
 * @version 1.0
 */

/**
 * DynamicCubeContentRequestProcessor
 * 
 * Needs to be commented
 * 
 * @package RequestProcessing
 * @subpackage RequestProcessors
 */
class DynamicCubeContentRequestProcessor extends RequestProcessor {

	private $cubeDirectory = '../Cubes/';
    private static $CUBE_ID_ATTRIBUTE = 'cubeID';


    public function processRequest() {
    	
		/**
		 * what type? css, js, xhtml
		 * which cube?
		 */    	
        $cacheGateway = CacheGateway::getCacheGateway();
        $cubeInstanceID = $this->requestInfoBean->getGET( self::$CUBE_ID_ATTRIBUTE );
        $type = $this->requestInfoBean->getGET("contentType");
        
        $profileID = $_SESSION['MAIN_PROFILE_ID'];

        $cubeDTO = new CubeDTO(); 
        $cubeDTO->getCubeInstance( $cubeInstanceID, $profileID );
        
        $templateEngine = new CubeDefaultTemplateEngine( 'dev', 'us' );

        $templateEngine->assign( 'cubeElementInstanceID', $cubeDTO->getElementInstanceID());
        $templateEngine->assign( 'cubeElementTypeID', $cubeDTO->getElementTypeID() );
        $templateEngine->assign( 'cubeElementName', $cubeDTO->getCubeName() );
        
        if( $cubeDTO->getProfileElementRank() != null ){
        	$templateEngine->assign( 'profileRanked', true );
        	$templateEngine->assign( 'profileRank', ($cubeDTO->getProfileElementRank() + 1 )  * 20  );
        	
        } else {
        	$templateEngine->assign( 'elementRank', ($cubeDTO->getElementRank() + 1 )  * 20 );
        	$templateEngine->assign( 'profileRanked', false );
        }
        
        
        $output = null;
    
    	if ($type === "css") {
            header("Content-type: text/css; charset=UTF-8");
    		$output = $templateEngine->fetch( $cubeDTO->getCubeElementContentViewCSS() );
    	} else if ( $type === "js") {
    		header("Content-type: script/javascript; charset=UTF-8");
    		$this->setCacheHeaders();
            switch ( $this->requestInfoBean->getGET( 'contentID' ) ) {
                case 'InitFunc' :
                    $output = $templateEngine->fetch( $cubeDTO->getCubeElementContentViewInitFunc() );
                    break;
                case 'InitPrefsFunc' :
                    $output = $templateEngine->fetch( $cubeDTO->getCubeElementPreferencesViewInitFunc() );
                    break;
                case 'AllFunc' :
                    $output = $templateEngine->fetch( $cubeDTO->getCubeElementAllFunctions() );
                    break;
                default :
                    break;
            }
    		
    	} else if ( $type === "xhtml") {
    		header("Content-type: text/html; charset=UTF-8");
            switch ( $this->requestInfoBean->getGET( 'contentID' ) ) {
        	    case 'WholeCube' :
        	    	$templateEngine->assign( 'contentViewCSS', $cubeDTO->getCubeElementContentViewCSS() );
        	    	$templateEngine->assign( 'contentViewFrameBeforeContent', $cubeDTO->getCubeElementContentViewXHTMLFrameBeforeContent() );
        	    	$templateEngine->assign( 'contentViewContent', $cubeDTO->getCubeElementContentViewXHTMLContent() );
        	    	$templateEngine->assign( 'contentViewFrameAfterContent', $cubeDTO->getCubeElementContentViewXHTMLFrameAfterContent() );
        	    	
        	    	$cubeContentProcessor = new CubeContentProcessor($cubeDTO, 'ContentViewContent');
                	$cubeContentProcessor->setTemplateEngine( $templateEngine );
                	$cubeContentProcessor->prepareContent();
        	    		
                    $output = $templateEngine->fetch( "../Templates/Frameworks/Common/XHTML/Cube/WholeCube.tpl" );
                    break;
                case 'ContentViewContent' :
                
                	$cubeContentProcessor = new CubeContentProcessor($cubeDTO, 'ContentViewContent');
                	$cubeContentProcessor->setTemplateEngine( $templateEngine );
                	$cubeContentProcessor->prepareContent();
                
                    $output = $templateEngine->fetch( $cubeDTO->getCubeElementContentViewXHTMLContent() );
                    break;
                case 'PreferencesViewContent' :
                    
                    $cubeContentProcessor = new CubeContentProcessor($cubeDTO, 'PreferencesViewContent');
                	$cubeContentProcessor->setTemplateEngine( $templateEngine );
                	$cubeContentProcessor->prepareContent();
                    
                    $output = $templateEngine->fetch( $cubeDTO->getCubeElementPreferencesViewXHTMLContent() );
                    break;
                default :
                    break;    
            }	
    	}


        
        echo $output;
	}
	
	private function setCacheHeaders(){
        Header("Cache-Control: must-revalidate");
		Header("Pragma: cache");
		$offset = 604800;
		Header( "Expires: " . gmdate("D, d M Y H:i:s", time() + $offset) . " GMT" );
	}
 
}

?>
