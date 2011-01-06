<?php
/**
 * RankElementInstanceRequestProcessor Class File
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
 * @author Keith Buel
 * @copyright 2011 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package RequestProcessing
 * @version 1.0
 */

/**
 * RankElementInstanceRequestProcessor
 * 
 * Needs to be commented
 * 
 * @package RequestProcessing
 * @subpackage RequestProcessors
 */
class RankElementInstanceRequestProcessor extends RequestProcessor {
 	
 	    public function processRequest() {
 	    	
 	    	//who am i?
 	    	$rankerProfileID = $_SESSION['MAIN_PROFILE_ID'];
 	    	
 	    	//the element instance i am ranking
 	    	$elementID = $this->requestInfoBean->getGET('elmInstance');
 	    	
 	    	//the rank I'm ranking it
 	    	$elementRank = $this->requestInfoBean->getGET('rank');
	    	
 	    	$daoFunction = 'rankElement';
 	    	
 	    	$inputValues = array();
 	    	$inputValues[ 'profileID' ] = $rankerProfileID;
 	    	$inputValues[ 'elementID' ] = $elementID;
 	    	$inputValues[ 'rank' ] = $elementRank;
 	    	 	    	
 	    	$daoFactory = AbstractDAOFactory::getInstance();
			$genericPLFunctionDAO = $daoFactory->getGenericPLFunctionDAO();
			$gqDTO = $genericPLFunctionDAO->selectGenericData( $daoFunction,  $inputValues );
			
			$success = $gqDTO->get_output_successful();
			if( $success ){
				eGlooLogger::writeLog( eGlooLogger::DEBUG, "SUCCESSFUL call to $daoFunction");
			} else {
				eGlooLogger::writeLog( eGlooLogger::DEBUG, "FAILURE call to $daoFunction");
			}
			
 	    }
 	    
 }
?>
