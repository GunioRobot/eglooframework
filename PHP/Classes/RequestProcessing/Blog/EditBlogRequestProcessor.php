<?php
/**
 * EditBlogRequestProcessor Class File
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
 * @author <UNKNOWN>
 * @copyright 2011 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package RequestProcessing
 * @subpackage Blog
 * @version 1.0
 */

/**
 * EditBlogRequestProcessor
 *
 * Needs to be commented
 * 
 * @package RequestProcessing
 * @subpackage Blog
 */
class EditBlogRequestProcessor extends RequestProcessor {

    
    public function processRequest() {

        
		$daoFunction = 'editBlog';
		$inputValues = array();
    	$inputValues[ 'inputBlogId' ] = $this->requestInfoBean->getPOST( 'blogID' ); 
    	$inputValues[ 'inputBlogTitle' ] = $this->requestInfoBean->getPOST( 'newBlogTitle' );
    	$inputValues[ 'inputBlogContent' ] = $this->requestInfoBean->getPOST( 'newBlogContent' );
    	 	    	
    	$daoFactory = AbstractDAOFactory::getInstance();
		$genericPLFunctionDAO = $daoFactory->getGenericPLFunctionDAO();
		$gqDTO = $genericPLFunctionDAO->selectGenericData( $daoFunction,  $inputValues );
        
        //TODO: check output successful... do something?
        $gqDTO->get_output_successful();
        
        //forward the request to view Blog Request Processor
		header( 'Location: /blog/viewBlog/&blogID=' . $this->requestInfoBean->getPOST( 'blogID' ) );
        
    }
    
}

?>