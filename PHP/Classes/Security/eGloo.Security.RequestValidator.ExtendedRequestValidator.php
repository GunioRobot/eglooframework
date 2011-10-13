<?php
namespace eGloo\Security\RequestValidator;

use eGloo\Configuration as Configuration;
use eGloo\Logger as Logger;

use eGloo\Performance\Caching\Gateway as CacheGateway;

use \DOMDocument as DOMDocument;
use \ErrorException as ErrorException;
use \Exception as Exception;
use \SimpleXMLElement as SimpleXMLElement;

/**
 * eGloo\Security\RequestValidator\ExtendedRequestValidator Class File
 *
 * Contains the class definition for the eGloo\Security\RequestValidator\ExtendedRequestValidator
 * 
 * Copyright 2011 eGloo LLC
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
 * @copyright 2011 eGloo LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package $package
 * @subpackage $subpackage
 * @version 1.0
 */

/**
 * eGloo\Security\RequestValidator\ExtendedRequestValidator
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class ExtendedRequestValidator extends RequestValidator {

	/**
	 * Returns the singleton of this class
	 */
    public static function getInstance( $webapp = "Default", $uibundle = "Default" ) {
		if ( !isset(self::$singleton) ) {
			self::$singleton = new self( $webapp, $uibundle );
		}

		return self::$singleton;
    }

	public function getParsedDefinitionsArrayFromXML( $requests_xml_location = './XML/Requests.xml' ) {
		$retVal = null;

		if ( file_exists($requests_xml_location) && is_file($requests_xml_location) && is_readable($requests_xml_location) ) {
			$retVal = self::$requestDefinitionParser->loadRequestNodes( false, $requests_xml_location );
		} else {
			throw new Exception( 'ExtendedRequestValidator: No Requests.xml file found at "' . $requests_xml_location . '"' );
		}

		return $retVal;
	}

	public function writeDefinitionsXMLFromArray( $request_definitions, $overwrite = true, $requests_xml_output_location = './XML/Requests.generated.xml' ) {
		$retVal = false;

		$full_parent_directory_path = realpath(preg_replace('~^([a-zA-Z0-9. ]+/)*?([a-zA-Z0-9.]*)$~', '$1', $requests_xml_output_location));
		$requests_xml_output_filename = preg_replace('~^([a-zA-Z0-9. ]+/)*?([a-zA-Z0-9.]*)$~', '$2', $requests_xml_output_location);

		$qualified_path = $full_parent_directory_path . '/' . $requests_xml_output_filename;

		if ( file_exists($qualified_path) && !$overwrite) {
			throw new ErrorException('Requests XML exists - will not overwrite: "' . $requests_xml_output_location . '"');
		} else {
			if (!is_writable($full_parent_directory_path)) {
				trigger_error('Destination for generated Requests XML is not writable');
			}

			$requests_skeleton_xml_path = Configuration::getFrameworkXMLPath() . '/Requests.skeleton.xml';

			// if ( file_exists($requests_skeleton_xml_path) && is_file($requests_skeleton_xml_path) && is_readable($requests_skeleton_xml_path) ) {
			// 	$skeletonRequestsXMLObj = simplexml_load_file( $requests_skeleton_xml_path );
			// }

			$requests_skeleton_xml = file_get_contents( $requests_skeleton_xml_path );

			$xmlData = '';
			$xmlData .= '<tns:Requests xmlns:tns="com.egloo.www/eGlooRequests" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"' . "\n";
			$xmlData .= "\t" . 'xsi:schemaLocation="com.egloo.www/eGlooRequests ../XML/Schemas/eGlooRequests.xsd">';
			$xmlData .= '</tns:Requests>';

			$skeletonRequestsXMLObj = new SimpleXMLElement( $xmlData );

			$xmlObject = $skeletonRequestsXMLObj;

			ksort($request_definitions['requestClasses']);

			Logger::writeLog( Logger::INFO, 'Beginning rebuild process...' );

			foreach( $request_definitions['requestClasses'] as $request_class_id => $request_class ) {
				Logger::writeLog( Logger::DEBUG, 'Rebuilding RequestClass "' . $request_class_id . '"...' );

				$requestClassObj = $xmlObject->addChild( 'RequestClass', null, '' );
				$requestClassObj->addAttribute( 'id', $request_class_id );

				foreach( $request_class['requests'] as $request_id => $request ) {
					$requestObj = $requestClassObj->addChild( 'Request' );

					$requestObj->addAttribute( 'id', $request_id );
					$requestObj->addAttribute( 'processorID', $request['processorID'] );

					if ( isset($request['errorProcessorID']) ) {
						$requestObj->addAttribute( 'errorProcessorID', $request['errorProcessorID'] );
					}

					foreach( $request['boolArguments'] as $boolArgument ) {
						$boolArgumentXMLObject = $requestObj->addChild('BoolArgument');

						$boolArgumentXMLObject->addAttribute('id', $boolArgument['id']);
						$boolArgumentXMLObject->addAttribute('type', $boolArgument['type']);
						$boolArgumentXMLObject->addAttribute('required', $boolArgument['required']);

						if ( isset($boolArgument['default']) ) {
							$boolArgumentXMLObject->addAttribute('default', $boolArgument['default']);
						}
					}

					foreach( $request['selectArguments'] as $selectArgument ) {
						$selectArgumentXMLObject = $requestObj->addChild('SelectArgument');

						$selectArgumentXMLObject->addAttribute('id', $selectArgument['id']);
						$selectArgumentXMLObject->addAttribute('type', $selectArgument['type']);
						
						if ( isset($selectArgument['scalarType']) ) {
							$selectArgumentXMLObject->addAttribute('scalarType', $selectArgument['scalarType']);
						}

						$selectArgumentXMLObject->addAttribute('required', $selectArgument['required']);

						if ( isset($selectArgument['default']) ) {
							$selectArgumentXMLObject->addAttribute('default', $selectArgument['default']);
						}

						foreach( $selectArgument['values'] as $selectArgumentValue ) {
							$selectArgumentValueXMLObject = $selectArgumentXMLObject->addChild( 'value', $selectArgumentValue );
						}
					}

					foreach( $request['variableArguments'] as $variableArgument ) {
						$variableArgumentXMLObject = $requestObj->addChild('VariableArgument');

						$variableArgumentXMLObject->addAttribute('id', $variableArgument['id']);
						$variableArgumentXMLObject->addAttribute('type', $variableArgument['type']);
						$variableArgumentXMLObject->addAttribute('regex', $variableArgument['regex']);

						if ( isset($variableArgument['scalarType']) ) {
							$variableArgumentXMLObject->addAttribute('scalarType', $variableArgument['scalarType']);
						}
						
						$variableArgumentXMLObject->addAttribute('required', $variableArgument['required']);

						if ( isset($variableArgument['default']) ) {
							$variableArgumentXMLObject->addAttribute('default', $variableArgument['default']);
						}
					}

					foreach( $request['complexArguments'] as $complexArgument ) {
						$complexArgumentXMLObject = $requestObj->addChild('ComplexArgument');

						$complexArgumentXMLObject->addAttribute('id', $complexArgument['id']);
						$complexArgumentXMLObject->addAttribute('type', $complexArgument['type']);
						$complexArgumentXMLObject->addAttribute('validator', $complexArgument['validator']);

						if ( isset($complexArgument['scalarType']) ) {
							$complexArgumentXMLObject->addAttribute('scalarType', $complexArgument['scalarType']);
						}

						if ( isset($complexArgument['complexType']) ) {
							$complexArgumentXMLObject->addAttribute('complexType', $complexArgument['complexType']);
						}

						$complexArgumentXMLObject->addAttribute('required', $complexArgument['required']);

						if ( isset($complexArgument['default']) ) {
							$complexArgumentXMLObject->addAttribute('default', $complexArgument['default']);
						}
					}

					foreach( $request['formArguments'] as $formArgument ) {
						$formArgumentXMLObject = $requestObj->addChild('FormArgument');

						$formArgumentXMLObject->addAttribute('id', $formArgument['id']);
						$formArgumentXMLObject->addAttribute('type', $formArgument['type']);
						$formArgumentXMLObject->addAttribute('required', $formArgument['required']);
						$formArgumentXMLObject->addAttribute('formID', $formArgument['formID']);
					}

					foreach( $request['decorators'] as $decorator ) {
						$decoratorXMLObject = $requestObj->addChild('Decorator');

						$decoratorXMLObject->addAttribute('order', $decorator['order']);
						$decoratorXMLObject->addAttribute('decoratorID', $decorator['decoratorID']);
					}

					foreach( $request['depends'] as $depend ) {
						$dependXMLObject = $requestObj->addChild('Depend');

						$dependXMLObject->addAttribute('id', $depend['id']);
						$dependXMLObject->addAttribute('type', $depend['type']);

						foreach( $depend['children'] as $dependChild ) {
							$dependChildXMLObject = $dependXMLObject->addChild('Child');

							$dependChildXMLObject->addAttribute('id', $dependChild['id']);
							$dependChildXMLObject->addAttribute('type', $dependChild['type']);
						}
					}

					foreach( $request['initRoutines'] as $initRoutine ) {
						$initRoutineXMLObject = $requestObj->addChild('InitRoutine');

						$initRoutineXMLObject->addAttribute('order', $initRoutine['order']);
						$initRoutineXMLObject->addAttribute('initRoutineID', $initRoutine['initRoutineID']);
					}

					foreach( $request['requestAttributeSetIncludes'] as $requestAttributeSetInclude ) {
						$requestAttributeSetIncludeXMLObject = $requestObj->addChild('RequestAttributeSetInclude');

						$requestAttributeSetIncludeXMLObject->addAttribute('requestAttributeSetID', $requestAttributeSetInclude['requestAttributeSetID']);
						$requestAttributeSetIncludeXMLObject->addAttribute('priority', $requestAttributeSetInclude['priority']);
					}
				}
			}

			ksort($request_definitions['requestAttributeSets']);

			foreach ( $request_definitions['requestAttributeSets'] as $request_attribute_set_id => $request_attribute_set ) {
				Logger::writeLog( Logger::DEBUG, 'Rebuilding RequestAttributeSet "' . $request_attribute_set_id . '"...' );

				$requestObj = $xmlObject->addChild( 'RequestAttributeSet', null, '' );
				$requestObj->addAttribute( 'id', $request_attribute_set_id );

				foreach( $request_attribute_set['attributes']['boolArguments'] as $boolArgument ) {
					$boolArgumentXMLObject = $requestObj->addChild('BoolArgument');

					$boolArgumentXMLObject->addAttribute('id', $boolArgument['id']);
					$boolArgumentXMLObject->addAttribute('type', $boolArgument['type']);
					$boolArgumentXMLObject->addAttribute('required', $boolArgument['required']);

					if ( isset($boolArgument['default']) ) {
						$boolArgumentXMLObject->addAttribute('default', $boolArgument['default']);
					}
				}

				foreach( $request_attribute_set['attributes']['selectArguments'] as $selectArgument ) {
					$selectArgumentXMLObject = $requestObj->addChild('SelectArgument');

					$selectArgumentXMLObject->addAttribute('id', $selectArgument['id']);
					$selectArgumentXMLObject->addAttribute('type', $selectArgument['type']);
					
					if ( isset($selectArgument['scalarType']) ) {
						$selectArgumentXMLObject->addAttribute('scalarType', $selectArgument['scalarType']);
					}

					$selectArgumentXMLObject->addAttribute('required', $selectArgument['required']);

					if ( isset($selectArgument['default']) ) {
						$selectArgumentXMLObject->addAttribute('default', $selectArgument['default']);
					}

					foreach( $selectArgument['values'] as $selectArgumentValue ) {
						$selectArgumentValueXMLObject = $selectArgumentXMLObject->addChild( 'value', $selectArgumentValue );
					}
				}

				foreach( $request_attribute_set['attributes']['variableArguments'] as $variableArgument ) {
					$variableArgumentXMLObject = $requestObj->addChild('VariableArgument');

					$variableArgumentXMLObject->addAttribute('id', $variableArgument['id']);
					$variableArgumentXMLObject->addAttribute('type', $variableArgument['type']);
					$variableArgumentXMLObject->addAttribute('regex', $variableArgument['regex']);

					if ( isset($variableArgument['scalarType']) ) {
						$variableArgumentXMLObject->addAttribute('scalarType', $variableArgument['scalarType']);
					}
					
					$variableArgumentXMLObject->addAttribute('required', $variableArgument['required']);

					if ( isset($variableArgument['default']) ) {
						$variableArgumentXMLObject->addAttribute('default', $variableArgument['default']);
					}
				}

				foreach( $request_attribute_set['attributes']['complexArguments'] as $complexArgument ) {
					$complexArgumentXMLObject = $requestObj->addChild('ComplexArgument');

					$complexArgumentXMLObject->addAttribute('id', $complexArgument['id']);
					$complexArgumentXMLObject->addAttribute('type', $complexArgument['type']);
					$complexArgumentXMLObject->addAttribute('validator', $complexArgument['validator']);

					if ( isset($complexArgument['scalarType']) ) {
						$complexArgumentXMLObject->addAttribute('scalarType', $complexArgument['scalarType']);
					}

					if ( isset($complexArgument['complexType']) ) {
						$complexArgumentXMLObject->addAttribute('complexType', $complexArgument['complexType']);
					}

					$complexArgumentXMLObject->addAttribute('required', $complexArgument['required']);

					if ( isset($complexArgument['default']) ) {
						$complexArgumentXMLObject->addAttribute('default', $complexArgument['default']);
					}
				}

				foreach( $request_attribute_set['attributes']['formArguments'] as $formArgument ) {
					$formArgumentXMLObject = $requestObj->addChild('FormArgument');

					$formArgumentXMLObject->addAttribute('id', $formArgument['id']);
					$formArgumentXMLObject->addAttribute('type', $formArgument['type']);
					$formArgumentXMLObject->addAttribute('required', $formArgument['required']);
					$formArgumentXMLObject->addAttribute('formID', $formArgument['formID']);
				}

				foreach( $request_attribute_set['attributes']['decorators'] as $decorator ) {
					$decoratorXMLObject = $requestObj->addChild('Decorator');

					$decoratorXMLObject->addAttribute('order', $decorator['order']);
					$decoratorXMLObject->addAttribute('decoratorID', $decorator['decoratorID']);
				}

				foreach( $request_attribute_set['attributes']['depends'] as $depend ) {
					$dependXMLObject = $requestObj->addChild('Depend');

					$dependXMLObject->addAttribute('id', $depend['id']);
					$dependXMLObject->addAttribute('type', $depend['type']);

					foreach( $depend['children'] as $dependChild ) {
						$dependChildXMLObject = $dependXMLObject->addChild('Child');

						$dependChildXMLObject->addAttribute('id', $dependChild['id']);
						$dependChildXMLObject->addAttribute('type', $dependChild['type']);
					}
				}

				foreach( $request_attribute_set['attributes']['initRoutines'] as $initRoutine ) {
					$initRoutineXMLObject = $requestObj->addChild('InitRoutine');

					$initRoutineXMLObject->addAttribute('order', $initRoutine['order']);
					$initRoutineXMLObject->addAttribute('initRoutineID', $initRoutine['initRoutineID']);
				}
			}

			$domObject = new DOMDocument();

			$domObject->preserveWhiteSpace = false;
			$domObject->validateOnParse = true;
			$domObject->loadXML($xmlObject->asXML());

			$formatted_xml = $requests_skeleton_xml . \eGlooXML::formatXMLString( $domObject->saveXML( $domObject->documentElement, LIBXML_NSCLEAN ) );

			// $domObject->save( $qualified_path );

			file_put_contents( $qualified_path, $formatted_xml );

			return $formatted_xml;
		}

		return $retVal;
	}

}

