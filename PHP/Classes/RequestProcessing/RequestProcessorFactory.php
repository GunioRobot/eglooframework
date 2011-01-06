<?php
/**
 * RequestProcessorFactory Final Class File
 *
 * Contains the RequestProcessorFactory class definition
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
 * @author George Cooper
 * @copyright 2011 eGloo, LLC
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

		$requestProcessorID = (string) $requestProcessorID;

		if ( $requestProcessorID !== null ) {
			$requestProcessor = new $requestProcessorID;

			//now add the decorators
			foreach( $requestInfoBean->getDecoratorArray() as $decoratorID ){
				$requestDecorator = new $decoratorID;

				$requestDecorator->setChildRequestProcessor( $requestProcessor );

				//now set this decorator as the request processor
				$requestProcessor = $requestDecorator;
			}
		}

        $requestProcessor->setRequestInfoBean( $requestInfoBean );

        return $requestProcessor;
    }

    public static function getErrorRequestProcessor( $requestInfoBean ) {
        $errorRequestProcessorID = $requestInfoBean->getErrorRequestProcessorID();
        $errorRequestProcessor = null;

		if ( $errorRequestProcessorID !== null ) {
			$errorRequestProcessor = new $errorRequestProcessorID;

			//now add the decorators
			foreach( $requestInfoBean->getDecoratorArray() as $decoratorID ){
				$requestDecorator = new $decoratorID;

				$requestDecorator->setChildRequestProcessor( $errorRequestProcessor );

				//now set this decorator as the request processor
				$errorRequestProcessor = $requestDecorator;
			}

			$errorRequestProcessor->setRequestInfoBean( $requestInfoBean );
		}

        return $errorRequestProcessor;
    }

}
 
?>
