<?php
/**
 * RequestProcessorFactory Final Class File
 *
 * Contains the RequestProcessorFactory class definition
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
 * @author Keith Buel
 * @copyright 2008 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package RequestProcessing
 * @version 1.0
 */

/**
 * RequestProcessorFactory
 *
 * This class creates concrete request processors to process each type of request
 * 
 * @package RequestProcessing
 */
final class RequestProcessorFactory {

    public static function getRequestProcessor( $requestInfoBean ) {

        $requestProcessorID = $requestInfoBean->getRequestProcessorID();
        $requestProcessor = null;

        $cacheGateway = CacheGateway::getCacheGateway();
		$requestProcessorID = (string) $requestProcessorID;

        if ( ( $requestProcessor = $cacheGateway->getObject( $requestProcessorID, '<type>' ) ) == null ) {
            if ( $requestProcessorID !== null ) {
				//first make the concrete request processor
				// $requestProcessor = eval( "return new $requestProcessorID();" );
				$requestProcessor = new $requestProcessorID;

				//now add the decorators
				
				foreach( $requestInfoBean->getDecoratorArray() as $decoratorID ){
					// $requestDecorator = eval( "return new $decoratorID();" );
					$requestDecorator = new $decoratorID;

					$requestDecorator->setChildRequestProcessor( $requestProcessor );

					//now set this decorator as the request processor
					$requestProcessor = $requestDecorator;
				}
            } else {
                // TODO This shouldn't blindly return the main page because of our use of Ajax
                // We'll have to do extensive checking on the referral.  If it's a failure
                // on single component in the system, then we don't necessarily want to reload 
                // the entire interface.  Just present a simple error message to the user, but
                // don't reload their whole page.  
                            	
            	//$evalString = "return new ExternalMainPageBaseRequestProcessor();";
                $requestProcessorID = "ExternalMainPageBaseRequestProcessor";
            	$requestProcessor = new ExternalMainPageBaseRequestProcessor();
            }

            $cacheGateway->storeObject( (string) $requestProcessorID, $requestProcessor, '<type>' );
        }

        $requestProcessor->setRequestInfoBean( $requestInfoBean );
        return $requestProcessor;
    }

}
 
?>
