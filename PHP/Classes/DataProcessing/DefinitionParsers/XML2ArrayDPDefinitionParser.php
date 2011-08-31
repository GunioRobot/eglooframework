<?php
/**
 * XML2ArrayDPDefinitionParser Class File
 *
 * Contains the class definition for the XML2ArrayDPDefinitionParser
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
 * XML2ArrayDPDefinitionParser
 * 
 * Parses and acts as a lookup for data processing definition file (DataProcessing.xml)
 * This is a specific subclass implementation of the eGlooDPDefinitionParser.
 *
 * @package DataProcessing
 * @subpackage Security
 */
final class XML2ArrayDPDefinitionParser extends eGlooDPDefinitionParser {

	/**
	 * Static Data Members
	 */

	// Singleton data member to enforce the singleton pattern for eGlooDPDefinitionParser subclasses
	protected static $singleton;

	public function getDPDynamicObjectDefinition( $object_id ) {
		$retVal = null;

		if ( !isset( $this->_dataProcessingDynamicObjects ) ) {
			// Grab the cache handler specifically for this cache region.  We do this so that when we write to the cache for DataProcessing
			// we can also write some information to the caching system to better keep track of what is cached for the DataProcessing system
			// and do more granulated inspection and cache clearing
			$dataProcessingCacheRegionHandler = CacheManagementDirector::getCacheRegionHandler('DataProcessing');

			$allNodesCached = $dataProcessingCacheRegionHandler->getObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' .
				'XML2ArrayDPDefinitionParser::DynamicObjectNodesCached', 'DataProcessing', true );

			if ( !$allNodesCached ) {
				$this->loadDataProcessingNodes();
			} else {
				$this->_dataProcessingStatements = $dataProcessingCacheRegionHandler->getObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' .
					'XML2ArrayDPDefinitionParserDynamicObjectNodes', 'DataProcessing', true );
			}
		}

		if ( isset( $this->_dataProcessingDynamicObjects['objects'][$object_id] ) ) {
			$retVal = $this->_dataProcessingDynamicObjects['objects'][$object_id];
		} else {
			throw new Exception( 'DynamicObject "' . $object_id . '" does not exist. Please review your DataProcessing.xml' );
		}

		return $retVal;
	}

	public function getDPProcedureDefinition( $procedure_class, $procedure_id ) {
		$retVal = null;

		if ( !isset( $this->_dataProcessingProcedures ) ) {
			$this->loadDataProcessingNodes();
		}

		if ( isset( $this->_dataProcessingProcedures[$procedure_class]['procedures'][$procedure_id] ) ) {
			$retVal = $this->_dataProcessingProcedures[$procedure_class]['procedures'][$procedure_id];
		}

		return $retVal;
	}

	public function getDPSequenceDefinition( $sequence_class, $sequence_id ) {
		$retVal = null;

		if ( !isset( $this->_dataProcessingSequences ) ) {
			$this->loadDataProcessingNodes();
		}

		if ( isset( $this->_dataProcessingSequences[$sequence_class]['sequences'][$sequence_id] ) ) {
			$retVal = $this->_dataProcessingSequences[$sequence_class]['sequences'][$sequence_id];
		}

		return $retVal;
	}

	public function getDPStatementDefinition( $statement_class, $statement_id ) {
		$retVal = null;

		if ( !isset( $this->_dataProcessingStatements ) ) {
			// Grab the cache handler specifically for this cache region.  We do this so that when we write to the cache for DataProcessing
			// we can also write some information to the caching system to better keep track of what is cached for the DataProcessing system
			// and do more granulated inspection and cache clearing
			$dataProcessingCacheRegionHandler = CacheManagementDirector::getCacheRegionHandler('DataProcessing');

			$allNodesCached = $dataProcessingCacheRegionHandler->getObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' .
				'XML2ArrayDPDefinitionParser::StatementNodesCached', 'DataProcessing', true );

			if ( !$allNodesCached ) {
				$this->loadDataProcessingNodes();
			} else {
				$this->_dataProcessingStatements = $dataProcessingCacheRegionHandler->getObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' .
					'XML2ArrayDPDefinitionParserStatementNodes', 'DataProcessing', true );
			}
		}

		if ( isset( $this->_dataProcessingStatements[$statement_class]['statements'][$statement_id] ) ) {
			$retVal = $this->_dataProcessingStatements[$statement_class]['statements'][$statement_id];
		} else {
			throw new Exception( 'DPStatement class "' . $statement_class . '" and ID "' . $statement_id . '" pair does not exist. Please review your DataProcessing.xml' );
		}

		return $retVal;
	}

	/**
	 * Method to load data processing nodes from DataProcessing.xml definitions file
	 * 
	 * @throws ErrorException	if definition file cannot be read, has syntax errors, is missing
	 *							required values or provides invalid values
	 */
	public function loadDataProcessingNodes( $overwrite = true, $dp_xml_location = null ) {
		// Mark entrance into this method so that when debugging we can more accurately trace control flow
		eGlooLogger::writeLog( eGlooLogger::DEBUG, "XML2ArrayDPDefinitionParser: Entered loadRequestNodes()", 'DataProcessing' );

		$retVal = null;

		if ( !$dp_xml_location ) {
			// Grab the absolute file system path to the DataProcessing.xml we're concerned with.  $this->webapp is set
			// during construction of this XML2ArrayDPDefinitionParser singleton.  See eGlooDPDefinitionParser
			// for details.
			$dp_xml_location = eGlooConfiguration::getApplicationsPath() . '/' . eGlooConfiguration::getApplicationPath() . "/XML/DataProcessing.xml";
		}

		// Mark that we are now attempting to load the specified DataProcessing.xml
		eGlooLogger::writeLog( eGlooLogger::DEBUG, "XML2ArrayDPDefinitionParser: Loading " . $dp_xml_location, 'DataProcessing' );

		// Attempt to load the specified DataProcessing.xml file
		$dataProcessingXMLObject = simplexml_load_file( $dp_xml_location );

		// If reading the DataProcessing.xml file failed, log the error
		// TODO determine if we should throw an exception here...
		if ( !$dataProcessingXMLObject ) {
			eGlooLogger::writeLog( eGlooLogger::EMERGENCY,
				'XML2ArrayDPDefinitionParser: simplexml_load_file( "' . $dp_xml_location . '" ): ' . libxml_get_errors() );
		}

		$eglooXMLObj = new eGlooXML( $dp_xml_location );

		$hydrated_array = array();

		foreach( $eglooXMLObj->xpath( '/tns:DataProcessing/DPDynamicObjects/DPDynamicObject' ) as $dpDynamicObject ) {
			$hydrated_array[ $dpDynamicObject->getNodeID() ] = $dpDynamicObject->getHydratedArray();
		}

		die_r($hydrated_array);

		die_r($eglooXMLObj->xpath( '/tns:DataProcessing/DPDynamicObjects/DPDynamicObject' ));
		die;

		// Setup an array to hold all of our processed DPStatement definitions
		$dataProcessingDynamicObjects = $this->loadDataProcessingDynamicObjects( $dataProcessingXMLObject, $overwrite );

		// Setup an array to hold all of our processed DPProcedure definitions
		$dataProcessingProcedures = array();

		// Setup an array to hold all of our processed DPSequence definitions
		$dataProcessingSequences = $this->loadDataProcessingSequences( $dataProcessingXMLObject, $overwrite );

		// Setup an array to hold all of our processed DPStatement definitions
		$dataProcessingStatements = $this->loadDataProcessingStatements( $dataProcessingXMLObject, $overwrite );

		$retVal = array(
			'dataProcessingDynamicObjects' => $dataProcessingDynamicObjects,
			'dataProcessingProcedures' => $dataProcessingProcedures,
			'dataProcessingSequences' => $dataProcessingSequences,
			'dataProcessingStatements' => $dataProcessingStatements
		);

		// Mark successful completion of this method so that when debugging we can more accurately trace control flow
		eGlooLogger::writeLog( eGlooLogger::DEBUG, "XML2ArrayDPDefinitionParser: DataProcessing.xml successfully processed", 'DataProcessing' );

		return $retVal;
	}

	protected function loadDataProcessingDynamicObjects( $dataProcessingXMLObject, $overwrite = true ) {
		$retVal = null;

		// Grab the cache handler specifically for this cache region.  We do this so that when we write to the cache for DataProcessing
		// we can also write some information to the caching system to better keep track of what is cached for the DataProcessing system
		// and do more granulated inspection and cache clearing
		$dataProcessingCacheRegionHandler = CacheManagementDirector::getCacheRegionHandler('DataProcessing');

		$dataProcessingDynamicObjects = array();

		// Iterate over each DPStatementClass
		foreach( $dataProcessingXMLObject->xpath( '/tns:DataProcessing/DPDynamicObjects/DPDynamicObject' ) as $dataProcessingDynamicObject ) {
			// Grab the ID for this particular DPDynamicObject
			$dataProcessingDynamicObjectID = isset($dataProcessingDynamicObject['id']) ? (string) $dataProcessingDynamicObject['id'] : null;

			// If no ID is set for this DPDynamicObject, this is not a valid DataProcessing.xml and we should get out of here
			if ( !$dataProcessingDynamicObjectID || trim($dataProcessingDynamicObjectID) === '' ) {
				throw new ErrorException("No ID specified in DPDynamicObject. Please review your DataProcessing.xml");
			}

			$dpDynamicObjectStaticMethods = array();

			// Iterate over each DPDynamicObjectStaticMethod in this DPDynamicObject
			foreach( $dataProcessingDynamicObject->xpath( 'child::DPDynamicObjectStaticMethod' ) as $dataProcessingDynamicObjectStaticMethod ) {
				// Grab the ID for this particular DPDynamicObjectStaticMethod
				$dataProcessingDynamicObjectStaticMethodID = isset($dataProcessingDynamicObjectStaticMethod['id']) ? (string) $dataProcessingDynamicObjectStaticMethod['id'] : null;

				// If no ID is set for this DPDynamicObjectStaticMethod, this is not a valid DataProcessing.xml and we should get out of here
				if ( !$dataProcessingDynamicObjectStaticMethodID || trim($dataProcessingDynamicObjectStaticMethodID) === '' ) {
					throw new ErrorException("No ID specified in DPDynamicObjectStaticMethod. Please review your DataProcessing.xml");
				}

				$dpDynamicObjectStaticMethodArguments = array();

				foreach( $dataProcessingDynamicObjectStaticMethod->xpath('child::DPDynamicObjectStaticMethodArguments/DPDynamicObjectStaticMethodArgument')
					as $dataProcessingDynamicObjectStaticMethodArgument ) {

					// Grab the ID for this particular DPDynamicObjectStaticMethodArgument
					$dataProcessingDynamicObjectStaticMethodArgumentID =
						isset($dataProcessingDynamicObjectStaticMethodArgument['id']) ? (string) $dataProcessingDynamicObjectStaticMethodArgument['id'] : null;

					// If no ID is set for this DPDynamicObjectStaticMethodArgument, this is not a valid DataProcessing.xml and we should get out of here
					if ( !$dataProcessingDynamicObjectStaticMethodArgumentID || trim($dataProcessingDynamicObjectStaticMethodArgumentID) === '' ) {
						throw new ErrorException("No ID specified in DPDynamicObjectStaticMethodArgument. Please review your DataProcessing.xml");
					}

					$dpDynamicObjectStaticMethodArguments[$dataProcessingDynamicObjectStaticMethodArgumentID] =
						array( 'id' => $dataProcessingDynamicObjectStaticMethodArgumentID );
				}

				$dpDynamicObjectStaticMethodExecutionStatements = array();

				foreach( $dataProcessingDynamicObjectStaticMethod->xpath('child::DPDynamicObjectStaticMethodExecution/DPDynamicObjectStaticMethodExecutionStatement')
					as $dataProcessingDynamicObjectStaticMethodExecutionStatement ) {

					// Grab the order for this particular DPDynamicObjectStaticMethodExecutionStatement
					$dataProcessingDynamicObjectStaticMethodExecutionStatementOrder =
						isset($dataProcessingDynamicObjectStaticMethodExecutionStatement['order']) ?
							(string) $dataProcessingDynamicObjectStaticMethodExecutionStatement['order'] : null;

					// If no order is set for this DPDynamicObjectStaticMethodExecutionStatement, this is not a valid DataProcessing.xml and we should get out of here
					if ( !$dataProcessingDynamicObjectStaticMethodExecutionStatementOrder ||
							trim($dataProcessingDynamicObjectStaticMethodExecutionStatementOrder) === '' ) {
						throw new ErrorException("No order specified in DPDynamicObjectStaticMethodExecutionStatement. Please review your DataProcessing.xml");
					}

					$executionStatementDPStatements = array();

					foreach( $dataProcessingDynamicObjectStaticMethodExecutionStatement->xpath('child::DPStatement')
						as $dpStatement ) {

						$dpStatementClass = isset($dpStatement['class']) ? (string) $dpStatement['class'] : null;

						if ( !$dpStatementClass || trim($dpStatementClass) === '' ) {
							throw new ErrorException('No class specified in DPStatement component of ' .
								'DPDynamicObjectStaticMethodExecutionStatement. Please review your DataProcessing.xml');
						}

						$dpStatementID = isset($dpStatement['statementID']) ? (string) $dpStatement['statementID'] : null;

						if ( !$dpStatementID || trim($dpStatementID) === '' ) {
							throw new ErrorException('No statement ID specified in DPStatement component of ' .
								'DPDynamicObjectStaticMethodExecutionStatement. Please review your DataProcessing.xml');
						}

						$dpStatementType = isset($dpStatement['type']) ? (string) $dpStatement['type'] : null;

						if ( !$dpStatementType || trim($dpStatementType) === '' ) {
							throw new ErrorException('No type specified in DPStatement component of ' .
								'DPDynamicObjectStaticMethodExecutionStatement. Please review your DataProcessing.xml');
						}

						$argumentMaps = array();

						foreach( $dpStatement->xpath('child::DPDynamicObjectMethodArgumentMap') as $argumentMap ) {
							$mapFrom = isset($argumentMap['from']) ? (string) $argumentMap['from'] : null;

							if ( !$mapFrom || trim($mapFrom) === '' ) {
								throw new ErrorException('No "map from" specified in DPDynamicObjectMethodArgumentMap. Please review your DataProcessing.xml');
							}

							$mapTo = isset($argumentMap['to']) ? (string) $argumentMap['to'] : null;

							if ( !$mapTo || trim($mapTo) === '' ) {
								throw new ErrorException('No "map to" specified in DPDynamicObjectMethodArgumentMap. Please review your DataProcessing.xml');
							}

							$argumentMaps[$mapTo] = array( 'from' => $mapFrom, 'to' => $mapTo );
						}

						$returnMaps = array();

						foreach( $dpStatement->xpath('child::DPDynamicObjectMethodReturnMap') as $returnMap ) {
							$mapFrom = isset($returnMap['from']) ? (string) $returnMap['from'] : null;

							if ( !$mapFrom || trim($mapFrom) === '' ) {
								throw new ErrorException('No "map from" specified in DPDynamicObjectMethodReturnMap. Please review your DataProcessing.xml');
							}

							$mapTo = isset($returnMap['to']) ? (string) $returnMap['to'] : null;

							if ( !$mapTo || trim($mapTo) === '' ) {
								throw new ErrorException('No "map to" specified in DPDynamicObjectMethodReturnMap. Please review your DataProcessing.xml');
							}

							$returnMaps[$mapTo] = array( 'from' => $mapFrom, 'to' => $mapTo );
						}

						$executionStatementDPStatements[] = array(
							'class' => $dpStatementClass,
							'statementID' => $dpStatementID,
							'type' => $dpStatementType,
							'argumentMaps' => $argumentMaps,
							'returnMaps' => $returnMaps,
						);

					}

					$dpDynamicObjectStaticMethodExecutionStatements[$dataProcessingDynamicObjectStaticMethodExecutionStatementOrder] = array(
						'order' => $dataProcessingDynamicObjectStaticMethodExecutionStatementOrder,
						'dpStatements' => $executionStatementDPStatements,
					);
				}

				$dpDynamicObjectStaticMethods[$dataProcessingDynamicObjectStaticMethodID] =
					array(
						'id' => $dataProcessingDynamicObjectStaticMethodID,
						'arguments' => $dpDynamicObjectStaticMethodArguments,
						'executionStatements' => $dpDynamicObjectStaticMethodExecutionStatements,
					);
			}

			$dpDynamicObjectMethods = array();

			// Iterate over each DPDynamicObjectMethod in this DPDynamicObject
			foreach( $dataProcessingDynamicObject->xpath( 'child::DPDynamicObjectMethod' ) as $dataProcessingDynamicObjectMethod ) {
				// Grab the ID for this particular DPDynamicObjectMethod
				$dataProcessingDynamicObjectMethodID = isset($dataProcessingDynamicObjectMethod['id']) ? (string) $dataProcessingDynamicObjectMethod['id'] : null;

				// If no ID is set for this DPDynamicObjectMethod, this is not a valid DataProcessing.xml and we should get out of here
				if ( !$dataProcessingDynamicObjectMethodID || trim($dataProcessingDynamicObjectMethodID) === '' ) {
					throw new ErrorException("No ID specified in DPDynamicObjectMethod. Please review your DataProcessing.xml");
				}

				$dpDynamicObjectMethodArguments = array();

				foreach( $dataProcessingDynamicObjectMethod->xpath('child::DPDynamicObjectMethodArguments/DPDynamicObjectMethodArgument')
					as $dataProcessingDynamicObjectMethodArgument ) {

					// Grab the ID for this particular DPDynamicObjectMethodArgument
					$dataProcessingDynamicObjectMethodArgumentID =
						isset($dataProcessingDynamicObjectMethodArgument['id']) ? (string) $dataProcessingDynamicObjectMethodArgument['id'] : null;

					// If no ID is set for this DPDynamicObjectMethodArgument, this is not a valid DataProcessing.xml and we should get out of here
					if ( !$dataProcessingDynamicObjectMethodArgumentID || trim($dataProcessingDynamicObjectMethodArgumentID) === '' ) {
						throw new ErrorException("No ID specified in DPDynamicObjectMethodArgument. Please review your DataProcessing.xml");
					}

					$dpDynamicObjectMethodArguments[$dataProcessingDynamicObjectMethodArgumentID] =
						array( 'id' => $dataProcessingDynamicObjectMethodArgumentID );
				}

				$dpDynamicObjectMethods[$dataProcessingDynamicObjectMethodID] =
					array(
						'id' => $dataProcessingDynamicObjectMethodID,
						'arguments' => $dpDynamicObjectMethodArguments
					);
			}

			$dpDynamicObjectConstants = array();

			// Iterate over each DPDynamicObjectConstant in this DPDynamicObject
			foreach( $dataProcessingDynamicObject->xpath( 'child::DPDynamicObjectConstant' ) as $dataProcessingDynamicObjectConstant ) {
				// Grab the ID for this particular DPDynamicObjectConstant
				$dataProcessingDynamicObjectConstantID = isset($dataProcessingDynamicObjectConstant['id']) ? (string) $dataProcessingDynamicObjectConstant['id'] : null;

				// If no ID is set for this DPDynamicObjectConstant, this is not a valid DataProcessing.xml and we should get out of here
				if ( !$dataProcessingDynamicObjectConstantID || trim($dataProcessingDynamicObjectConstantID) === '' ) {
					throw new ErrorException("No ID specified in DPDynamicObjectConstant. Please review your DataProcessing.xml");
				}

				// Grab the type for this particular DPDynamicObjectConstant
				$dataProcessingDynamicObjectConstantType =
					isset($dataProcessingDynamicObjectConstant['type']) ? (string) $dataProcessingDynamicObjectConstant['type'] : null;

				// Grab the default value for this particular DPDynamicObjectConstant
				$dataProcessingDynamicObjectConstantDefaultValue =
					isset($dataProcessingDynamicObjectConstant['defaultValue']) ? (string) $dataProcessingDynamicObjectConstant['defaultValue'] : null;

				// Grab the default value type for this particular DPDynamicObjectConstant
				$dataProcessingDynamicObjectConstantDefaultValueType =
					isset($dataProcessingDynamicObjectConstant['defaultValueType']) ? (string) $dataProcessingDynamicObjectConstant['defaultValueType'] : null;

				$dpDynamicObjectConstants[$dataProcessingDynamicObjectConstantID] =
					array(
						'id' => $dataProcessingDynamicObjectMethodID,
						'type' => $dataProcessingDynamicObjectConstantType,
						'defaultValue' => $dataProcessingDynamicObjectConstantDefaultValue,
						'defaultValueType' => $dataProcessingDynamicObjectConstantDefaultValueType,
					);
			}

			$dpDynamicObjectStaticMembers = array();

			// Iterate over each DPDynamicObjectStaticMember in this DPDynamicObject
			foreach( $dataProcessingDynamicObject->xpath( 'child::DPDynamicObjectStaticMember' ) as $dataProcessingDynamicObjectStaticMember ) {
				// Grab the ID for this particular DPDynamicObjectStaticMember
				$dataProcessingDynamicObjectStaticMemberID =
					isset($dataProcessingDynamicObjectStaticMember['id']) ? (string) $dataProcessingDynamicObjectStaticMember['id'] : null;

				// If no ID is set for this DPDynamicObjectStaticMember, this is not a valid DataProcessing.xml and we should get out of here
				if ( !$dataProcessingDynamicObjectStaticMemberID || trim($dataProcessingDynamicObjectStaticMemberID) === '' ) {
					throw new ErrorException("No ID specified in DPDynamicObjectStaticMember. Please review your DataProcessing.xml");
				}

				// Grab the type for this particular DPDynamicObjectStaticMember
				$dataProcessingDynamicObjectStaticMemberType =
					isset($dataProcessingDynamicObjectStaticMember['type']) ? (string) $dataProcessingDynamicObjectStaticMember['type'] : null;

				// Grab the default value for this particular DPDynamicObjectStaticMember
				$dataProcessingDynamicObjectStaticMemberDefaultValue =
					isset($dataProcessingDynamicObjectStaticMember['defaultValue']) ? (string) $dataProcessingDynamicObjectStaticMember['defaultValue'] : null;

				// Grab the default value type for this particular DPDynamicObjectStaticMember
				$dataProcessingDynamicObjectStaticMemberDefaultValueType =
					isset($dataProcessingDynamicObjectStaticMember['defaultValueType']) ? (string) $dataProcessingDynamicObjectStaticMember['defaultValueType'] : null;

				// Grab the scope for this particular DPDynamicObjectStaticMember
				$dataProcessingDynamicObjectStaticMemberScope =
					isset($dataProcessingDynamicObjectStaticMember['scope']) ? (string) $dataProcessingDynamicObjectStaticMember['scope'] : null;

				$dpDynamicObjectStaticMembers[$dataProcessingDynamicObjectStaticMemberID] =
					array(
						'id' => $dataProcessingDynamicObjectStaticMemberID,
						'type' => $dataProcessingDynamicObjectStaticMemberType,
						'defaultValue' => $dataProcessingDynamicObjectStaticMemberDefaultValue,
						'defaultValueType' => $dataProcessingDynamicObjectStaticMemberDefaultValueType,
						'scope' => $dataProcessingDynamicObjectStaticMemberScope,
					);
			}

			$dpDynamicObjectMembers = array();

			// Iterate over each DPDynamicObjectMember in this DPDynamicObject
			foreach( $dataProcessingDynamicObject->xpath( 'child::DPDynamicObjectMember' ) as $dataProcessingDynamicObjectMember ) {
				// Grab the ID for this particular DPDynamicObjectStaticMember
				$dataProcessingDynamicObjectMemberID =
					isset($dataProcessingDynamicObjectMember['id']) ? (string) $dataProcessingDynamicObjectMember['id'] : null;

				// If no ID is set for this DPDynamicObjectMember, this is not a valid DataProcessing.xml and we should get out of here
				if ( !$dataProcessingDynamicObjectMemberID || trim($dataProcessingDynamicObjectMemberID) === '' ) {
					throw new ErrorException("No ID specified in DPDynamicObjectMember. Please review your DataProcessing.xml");
				}

				// Grab the type for this particular DPDynamicObjectMember
				$dataProcessingDynamicObjectMemberType =
					isset($dataProcessingDynamicObjectMember['type']) ? (string) $dataProcessingDynamicObjectMember['type'] : null;

				// Grab the default value for this particular DPDynamicObjectMember
				$dataProcessingDynamicObjectMemberDefaultValue =
					isset($dataProcessingDynamicObjectMember['defaultValue']) ? (string) $dataProcessingDynamicObjectMember['defaultValue'] : null;

				// Grab the default value type for this particular DPDynamicObjectMember
				$dataProcessingDynamicObjectMemberDefaultValueType =
					isset($dataProcessingDynamicObjectMember['defaultValueType']) ? (string) $dataProcessingDynamicObjectMember['defaultValueType'] : null;

				// Grab the scope for this particular DPDynamicObjectMember
				$dataProcessingDynamicObjectMemberScope =
					isset($dataProcessingDynamicObjectMember['scope']) ? (string) $dataProcessingDynamicObjectMember['scope'] : null;

				// Grab the scope for this particular DPDynamicObjectMember
				$dataProcessingDynamicObjectMemberManaged =
					isset($dataProcessingDynamicObjectMember['managed']) ? (string) $dataProcessingDynamicObjectMember['managed'] : null;

				if ( strtolower($dataProcessingDynamicObjectMemberManaged) === 'true' ) {
					$dataProcessingDynamicObjectMemberManaged = true;
				} else {
					$dataProcessingDynamicObjectMemberManaged = false;
				}

				$dpDynamicObjectMembers[$dataProcessingDynamicObjectMemberID] =
					array(
						'id' => $dataProcessingDynamicObjectMemberID,
						'type' => $dataProcessingDynamicObjectMemberType,
						'defaultValue' => $dataProcessingDynamicObjectMemberDefaultValue,
						'defaultValueType' => $dataProcessingDynamicObjectMemberDefaultValueType,
						'scope' => $dataProcessingDynamicObjectMemberScope,
						'managed' => $dataProcessingDynamicObjectMemberManaged,
					);
			}

			// Assign an array to hold this DPStatementClass node definition.  Associative key is the DPStatementClass ID
			$dataProcessingDynamicObjects[$dataProcessingDynamicObjectID] =
				array(	'dynamicObjectID' => $dataProcessingDynamicObjectID,
						'staticMethods' => $dpDynamicObjectStaticMethods,
						'methods' => $dpDynamicObjectMethods,
						'constants' => $dpDynamicObjectConstants,
						'staticMembers' => $dpDynamicObjectStaticMembers,
						'members' => $dpDynamicObjectMembers,
				);

			if ( $overwrite ) {
				$dataProcessingCacheRegionHandler->storeObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'XML2ArrayDPDefinitionParserDynamicObjectNodes::' .
					$dataProcessingDynamicObjectID, $dataProcessingDynamicObjects[$dataProcessingDynamicObjectID], 'DataProcessing', 0, true );
			}
		}

		if ( $overwrite ) {
			$this->_dataProcessingDynamicObjects = array( 'objects' => $dataProcessingDynamicObjects );

			$dataProcessingCacheRegionHandler->storeObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'XML2ArrayDPDefinitionParserDynamicObjectNodes',
				$this->_dataProcessingDynamicObjects, 'DataProcessing', 0, true );

			$dataProcessingCacheRegionHandler->storeObject( 
				eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'XML2ArrayDPDefinitionParser::DynamicObjectNodesCached', true, 'DataProcessing', 0, true );
		}

		$retVal = $dataProcessingDynamicObjects;

		return $retVal;
	}

	protected function loadDataProcessingSequences( $dataProcessingXMLObject, $overwrite = true ) {
		$retVal = null;

		$dataProcessingSequences = array();

		// Iterate over the DPSequence nodes so that we can parse each DPSequence definition
		foreach( $dataProcessingXMLObject->xpath( '/tns:DataProcessing/DPSequence' ) as $dpSequence ) {
			// Grab the ID for this particular DPSequence
			$dpSequenceID = isset($dpSequence['id']) ? (string) $dpSequence['id'] : null;

			// If no ID is set for this DPSequence, this is not a valid DataProcessing.xml and we should get out of here
			if ( !$dpSequenceID || trim($dpSequenceID) === '' ) {
				throw new ErrorException("No ID specified in DPSequence. Please review your DataProcessing.xml");
			}

			// Assign an array to hold this DPSequence node definition.  Associative key is the DPSequence ID
			$dataProcessingSequences[$dpSequenceID] = array('dataProcessingSequence' => $dpSequenceID, 'attributes' => array());

			$dataProcessingSequences[$dpSequenceID]['attributes']['variableArguments'] = array();

			foreach( $dpSequence->xpath( 'child::VariableArgument' ) as $variableArgument ) {
				$newVariableArgument = array();

				$newVariableArgument['id'] = (string) $variableArgument['id'];
				$newVariableArgument['type'] = strtolower( (string) $variableArgument['type'] );
				$newVariableArgument['required'] = strtolower( (string) $variableArgument['required'] );
				$newVariableArgument['regex'] = (string) $variableArgument['regex'];

				if ( isset($variableArgument['scalarType']) ) {
					$newVariableArgument['scalarType'] =  (string) $variableArgument['scalarType'];
				}

				if ($newVariableArgument['required'] === 'false' && isset($variableArgument['default']) && $newVariableArgument['type'] !== 'postarray') {
					$defaultVariableValue = (string) $variableArgument['default'];

					if (preg_match( $newVariableArgument['regex'], $defaultVariableValue )) {
						$newVariableArgument['default'] = $defaultVariableValue;
					}
				}

				$dataProcessingSequences[$dpSequenceID]['attributes']['variableArguments'][$newVariableArgument['id']] = $newVariableArgument;
			}

			$dataProcessingSequences[$dpSequenceID]['attributes']['formArguments'] = array();

			foreach( $dpSequence->xpath( 'child::FormArgument' ) as $formArgument ) {
				$newFormArgument = array();

				$newFormArgument['id'] = (string) $formArgument['id'];
				$newFormArgument['type'] = strtolower( (string) $formArgument['type'] );
				$newFormArgument['required'] = strtolower( (string) $formArgument['required'] );
				$newFormArgument['formID'] = (string) $formArgument['formID'];

				$dataProcessingSequences[$dpSequenceID]['attributes']['formArguments'][$newFormArgument['id']] = $newFormArgument;
			}

			if ( $overwrite ) {
				$uniqueKey = ((string) $dpSequence['id']);
				$this->_dataProcessingSequences[ $uniqueKey ] = $dataProcessingSequences[$dpSequenceID];

				// Grab the cache handler specifically for this cache region.  We do this so that when we write to the cache for DataProcessing
				// we can also write some information to the caching system to better keep track of what is cached for the DataProcessing system
				// and do more granulated inspection and cache clearing
				$dataProcessingCacheRegionHandler = CacheManagementDirector::getCacheRegionHandler('DataProcessing');

				$dataProcessingCacheRegionHandler->storeObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'XML2ArrayDPDefinitionParserAttributeNodes::' .
					$uniqueKey, $dataProcessingSequences[$dpSequenceID], 'DataProcessing', 0, true );
			}
		}

		if ( $overwrite ) {
			// Grab the cache handler specifically for this cache region.  We do this so that when we write to the cache for DataProcessing
			// we can also write some information to the caching system to better keep track of what is cached for the DataProcessing system
			// and do more granulated inspection and cache clearing
			$dataProcessingCacheRegionHandler = CacheManagementDirector::getCacheRegionHandler('DataProcessing');

			// We're done processing our DPSequences, so let's store the structured array in cache for faster lookup
			// For cache properties, the ttl is forever (0) and we can keep the cache piping hot by storing a local copy (true)
			$dataProcessingCacheRegionHandler->storeObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'XML2ArrayDPDefinitionParserAttributeSets',
				$this->_dataProcessingSequences, 'DataProcessing', 0, true );
		}

		$retVal = $dataProcessingSequences;

		return $retVal;
	}

	protected function loadDataProcessingStatements( $dataProcessingXMLObject, $overwrite = true ) {
		$retVal = null;

		// Grab the cache handler specifically for this cache region.  We do this so that when we write to the cache for DataProcessing
		// we can also write some information to the caching system to better keep track of what is cached for the DataProcessing system
		// and do more granulated inspection and cache clearing
		$dataProcessingCacheRegionHandler = CacheManagementDirector::getCacheRegionHandler('DataProcessing');

		$dataProcessingStatements = array();

		// Iterate over each DPStatementClass
		foreach( $dataProcessingXMLObject->xpath( '/tns:DataProcessing/DPStatements/DPStatementClass' ) as $dataProcessingStatementClass ) {
			// Grab the ID for this particular DPStatementClass
			$dataProcessingStatementClassID = isset($dataProcessingStatementClass['id']) ? (string) $dataProcessingStatementClass['id'] : null;

			// If no ID is set for this DPStatementClass, this is not a valid DataProcessing.xml and we should get out of here
			if ( !$dataProcessingStatementClassID || trim($dataProcessingStatementClassID) === '' ) {
				throw new ErrorException("No ID specified in DPStatementClass. Please review your DataProcessing.xml");
			}

			$dpStatementClassStatements = array();

			// Iterate over each DPStatement in this DPStatementClass
			foreach( $dataProcessingStatementClass->xpath( 'child::DPStatement' ) as $dataProcessingStatement ) {
				// Grab the ID for this particular DPStatement
				$dataProcessingStatementID = isset($dataProcessingStatement['id']) ? (string) $dataProcessingStatement['id'] : null;

				// If no ID is set for this DPStatement, this is not a valid DataProcessing.xml and we should get out of here
				if ( !$dataProcessingStatementID || trim($dataProcessingStatementID) === '' ) {
					throw new ErrorException("No ID specified in DPStatement. Please review your DataProcessing.xml");
				}

				// Grab the type for this particular DPStatement
				$dataProcessingStatementType = isset($dataProcessingStatement['type']) ? (string) $dataProcessingStatement['type'] : null;

				// If no type is set for this DPStatement, this is not a valid DataProcessing.xml and we should get out of here
				if ( !$dataProcessingStatementType || trim($dataProcessingStatementType) === '' ) {
					throw new ErrorException("No type specified in DPStatement. Please review your DataProcessing.xml");
				}

				$dpStatementClassStatements[$dataProcessingStatementID] = array( 'id' => $dataProcessingStatementID, 'type' => $dataProcessingStatementType );

				$argumentLists = array();

				if ( $dataProcessingStatement->xpath( 'child::DPStatementArgumentLists/DPStatementArgumentList' ) ) {
					foreach( $dataProcessingStatement->xpath( 'child::DPStatementArgumentLists/DPStatementArgumentList' ) as $dpStatementArgumentList ) {
						$argumentListID = isset($dpStatementArgumentList['argumentListID']) ? (string) $dpStatementArgumentList['argumentListID'] : null;
						$parameterPreparation = isset($dpStatementArgumentList['parameterPreparation']) ? (string) $dpStatementArgumentList['parameterPreparation'] : null;

						$argumentLists[$argumentListID] = array( 'argumentListID' => $argumentListID, 'parameterPreparation' => $parameterPreparation );

						$arguments = array();

						if ( $dpStatementArgumentList->xpath( 'child::DPStatementArgument') ) {
							foreach( $dpStatementArgumentList->xpath( 'child::DPStatementArgument') as $dpStatementArgument ) {
								$argumentID = isset($dpStatementArgument['id']) ? (string) $dpStatementArgument['id'] : null;
								$argumentType = isset($dpStatementArgument['type']) ? (string) $dpStatementArgument['type'] : null;

								$arguments[$argumentID] = array( 'argumentID' => $argumentID, 'argumentType' => $argumentType );

								if ( $argumentType === 'integer' ) {
									$arguments[$argumentID]['min'] = isset($dpStatementArgument['min']) ? (string) $dpStatementArgument['min'] : null;
									$arguments[$argumentID]['max'] = isset($dpStatementArgument['max']) ? (string) $dpStatementArgument['max'] : null;
								} else if ( $argumentType === 'string' ) {
									$arguments[$argumentID]['pattern'] = isset($dpStatementArgument['pattern']) ? (string) $dpStatementArgument['pattern'] : null;
								}
							}
						}

						$argumentLists[$argumentListID]['arguments'] = $arguments;
					}
				}

				$dpStatementClassStatements[$dataProcessingStatementID]['argumentLists'] = $argumentLists;

				$statementReturn = array();

				if ( $dataProcessingStatement->xpath( 'child::DPStatementReturn' ) ) {
					foreach( $dataProcessingStatement->xpath( 'child::DPStatementReturn' ) as $dpStatementReturn ) {
						$dpStatementReturnType = isset($dpStatementReturn['type']) ? (string) $dpStatementReturn['type'] : null;

						$statementReturn['type'] = $dpStatementReturnType;

						$statementReturnColumnSets = array();

						if ( $dpStatementReturn->xpath( 'child::DPStatementReturnColumnSet' ) ) {
							foreach( $dpStatementReturn->xpath( 'child::DPStatementReturnColumnSet' ) as $dpStatementReturnColumnSet ) {
								$dpStatementReturnColumnSetTable = isset($dpStatementReturnColumnSet['table']) ? (string) $dpStatementReturnColumnSet['table'] : null;
								$dpStatementReturnColumnSetPattern = isset($dpStatementReturnColumnSet['pattern']) ? (string) $dpStatementReturnColumnSet['pattern'] : null;
								$dpStatementReturnColumnSetType = isset($dpStatementReturnColumnSet['type']) ? (string) $dpStatementReturnColumnSet['type'] : null;

								$statementReturnColumnSets[$dpStatementReturnColumnSetTable] =
									array( 'table' => $dpStatementReturnColumnSetTable, 'pattern' => $dpStatementReturnColumnSetPattern, 'type' => $dpStatementReturnColumnSetType );
							}
						}

						$statementReturn['statementReturnColumnSets'] = $statementReturnColumnSets;

						$statementReturnColumns = array();

						if ( $dpStatementReturn->xpath( 'child::DPStatementReturnColumn' ) ) {
							foreach( $dpStatementReturn->xpath( 'child::DPStatementReturnColumn' ) as $dpStatementReturnColumn ) {
								$dpStatementReturnColumnID = isset($dpStatementReturnColumn['id']) ? (string) $dpStatementReturnColumn['id'] : null;
								$dpStatementReturnColumnType = isset($dpStatementReturnColumn['type']) ? (string) $dpStatementReturnColumn['type'] : null;

								$statementReturnColumns[$dpStatementReturnColumnID] = array( 'id' => $dpStatementReturnColumnID, 'type' => $dpStatementReturnColumnType );

								if ( $dpStatementReturnColumnType === 'integer' ) {
									$statementReturnColumns[$dpStatementReturnColumnID]['min'] =
										isset($dpStatementReturnColumn['min']) ? (string) $dpStatementReturnColumn['min'] : null;
									$statementReturnColumns[$dpStatementReturnColumnID]['max'] =
										isset($dpStatementReturnColumn['max']) ? (string) $dpStatementReturnColumn['max'] : null;
								} else if ( $dpStatementReturnColumnType === 'string' ) {
									$statementReturnColumns[$dpStatementReturnColumnID]['pattern'] =
										isset($dpStatementReturnColumn['pattern']) ? (string) $dpStatementReturnColumn['pattern'] : null;
								}
							}
						}

						$statementReturn['statementReturnColumns'] = $statementReturnColumns;
					}
				}

				$dpStatementClassStatements[$dataProcessingStatementID]['statementReturn'] = $statementReturn;

				$statementVariants = array();

				if ( $dataProcessingStatement->xpath( 'child::DPStatementVariants/DPStatementVariant' ) ) {
					foreach( $dataProcessingStatement->xpath( 'child::DPStatementVariants/DPStatementVariant' ) as $dpStatementVariant ) {
						$dpStatementVariantConnection = isset($dpStatementVariant['connection']) ? (string) $dpStatementVariant['connection'] : null;

						$statementVariant = array( 'connection' => $dpStatementVariantConnection );

						$statementVariantEngineModes = array();

						if ( $dpStatementVariant->xpath( 'child::DPStatementVariantEngineMode' ) ) {
							foreach( $dpStatementVariant->xpath( 'child::DPStatementVariantEngineMode' ) as $dpStatementVariantEngineMode ) {
								$dpStatementVariantEngineModeMode = isset($dpStatementVariantEngineMode['mode']) ?
									eGlooConfiguration::getEngineModeFromString( (string) $dpStatementVariantEngineMode['mode'] ) : null;
								$dpStatementVariantEngineModeName = isset($dpStatementVariantEngineMode['mode']) ? (string) $dpStatementVariantEngineMode['mode'] : null;

								$statementVariantEngineModes[$dpStatementVariantEngineModeMode] =
									array( 'mode' => $dpStatementVariantEngineModeMode, 'modeName' => $dpStatementVariantEngineModeName );

								$includePaths = array();

								if ( $dpStatementVariantEngineMode->xpath( 'child::DPStatementIncludePath' ) ) {
									foreach( $dpStatementVariantEngineMode->xpath( 'child::DPStatementIncludePath' ) as $dpStatementIncludePath ) {
										$dpStatementIncludePathArgumentList = isset($dpStatementIncludePath['argumentList']) ? (string) $dpStatementIncludePath['argumentList'] : null;

										$dpStatementIncludePathValue = trim( (string) $dpStatementIncludePath );

										$includePaths[] = array( 'argumentList' => $dpStatementIncludePathArgumentList, 'includePath' => $dpStatementIncludePathValue );
									}
								}

								$statementVariantEngineModes[$dpStatementVariantEngineModeMode]['includePaths'] = $includePaths;
							}
						}

						$statementVariant['engineModes'] = $statementVariantEngineModes;

						$statementVariants[$dpStatementVariantConnection] = $statementVariant;
					}
				}

				$dpStatementClassStatements[$dataProcessingStatementID]['statementVariants'] = $statementVariants;

				$statementConstraints = array();

				if ( $dataProcessingStatement->xpath( 'child::DPStatementConstraints/DPStatementConstraint' ) ) {
					foreach( $dataProcessingStatement->xpath( 'child::DPStatementConstraints/DPStatementConstraint' ) as $dpStatementConstraint ) {
						$dpStatementConstraintType = isset($dpStatementConstraint['type']) ? (string) $dpStatementConstraint['type'] : null;
						$dpStatementConstraintMin = isset($dpStatementConstraint['min']) ? (string) $dpStatementConstraint['min'] : null;
						$dpStatementConstraintMax = isset($dpStatementConstraint['max']) ? (string) $dpStatementConstraint['max'] : null;
						$dpStatementConstraintGranularity = isset($dpStatementConstraint['granularity']) ? (string) $dpStatementConstraint['granularity'] : null;
						$dpStatementConstraintLevel = isset($dpStatementConstraint['level']) ? (string) $dpStatementConstraint['level'] : null;

						$statementConstraints[] = array(	'type' => $dpStatementConstraintType,
															'min' => $dpStatementConstraintMin,
															'max' => $dpStatementConstraintMax,
															'granularity' => $dpStatementConstraintGranularity,
															'level', $dpStatementConstraintLevel,
						);
					}
				}

				$dpStatementClassStatements[$dataProcessingStatementID]['statementConstraints'] = $statementConstraints;
			}

			// Assign an array to hold this DPStatementClass node definition.  Associative key is the DPStatementClass ID
			$dataProcessingStatements[$dataProcessingStatementClassID] = array( 'statementClass' => $dataProcessingStatementClassID, 'statements' => $dpStatementClassStatements );

			if ( $overwrite ) {
				$dataProcessingCacheRegionHandler->storeObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'XML2ArrayDPDefinitionParserStatementNodes::' .
					$dataProcessingStatementClassID, $dataProcessingStatements[$dataProcessingStatementClassID], 'DataProcessing', 0, true );
			}
		}

		if ( $overwrite ) {
			$this->_dataProcessingStatements = $dataProcessingStatements;

			$dataProcessingCacheRegionHandler->storeObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'XML2ArrayDPDefinitionParserStatementNodes',
				$this->_dataProcessingStatements, 'DataProcessing', 0, true );

			$dataProcessingCacheRegionHandler->storeObject( 
				eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'XML2ArrayDPDefinitionParser::StatementNodesCached', true, 'DataProcessing', 0, true );
		}

		$retVal = $dataProcessingStatements;

		return $retVal;
	}

	/**
	 * Empty init method invoked in constructor
	 *
	 * The eGlooDPDefinitionParser parent class requires us to implement this method in case we
	 * want to do something useful during construction of our singleton instance.  For now, do nothing.
	 */
	protected function init() {}

}
