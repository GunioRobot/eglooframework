<?php
/**
 * eGlooRequestLibrary Class File
 *
 * $file_block_description
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
 * @copyright 2010 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package $package
 * @subpackage $subpackage
 * @version 1.0
 */

/**
 * eGlooRequestLibrary
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class eGlooRequestLibrary {

	public static function getRequestClass( $requestClass ) {
		
	}

	public static function getRequestID( $requestID ) {
		
	}

	public static function getRequest( $requestClass, $requestID ) {
		
	}

	public static function getRequestURLArrayByRequestProcessorName( $requestProcessorName ) {
		
	}

	public static function getRequestURLArrayByRequestClassName( $requestClassName ) {
		
	}

	public static function getRequestURLArrayByRequestIDName( $requestIDName ) {
		
	}

	public static function getRequestURLArray( $absolute = false, $includeRewriteBase = true ) {
		$retVal = array();

		$cacheGateway = CacheGateway::getCacheGateway();

		$requestNodes = $cacheGateway->getObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'XML2ArrayRequestDefinitionParserNodes', 'RequestValidation' );

		if ( $requestNodes == null ) {
			
		}

		foreach($requestNodes as $requestNode) {
			$url = '';

			if ( $absolute ) {
				// one day this will be a great feature
				$url .= '';
			}
			
			if ( $includeRewriteBase ) {
				$url .= eGlooConfiguration::getRewriteBase();
			}

			$retVal[] = $url . $requestNode['requestClass'] . '/' . $requestNode['requestID'];
		}

		sort($retVal);

		return $retVal;
	}

	public static function getRequestProcessorNameByURL( $url ) {
		
	}

	public static function getRequestProcessorInstanceByUrl( $url ) {
		
	}

}

