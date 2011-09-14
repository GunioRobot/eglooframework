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
			$dpCacheRegionHandler = CacheManagementDirector::getCacheRegionHandler('DataProcessing');

			$allNodesCached = $dpCacheRegionHandler->getObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' .
				'XML2ArrayDPDefinitionParser::DynamicObjectNodesCached', 'DataProcessing', true );

			if ( !$allNodesCached ) {
				$this->loadDataProcessingNodes();
			} else {
				$this->_dataProcessingStatements = $dpCacheRegionHandler->getObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' .
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
			$dpCacheRegionHandler = CacheManagementDirector::getCacheRegionHandler('DataProcessing');

			$allNodesCached = $dpCacheRegionHandler->getObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' .
				'XML2ArrayDPDefinitionParser::StatementNodesCached', 'DataProcessing', true );

			if ( !$allNodesCached ) {
				$this->loadDataProcessingNodes();
			} else {
				$this->_dataProcessingStatements = $dpCacheRegionHandler->getObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' .
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
		$dpXMLObject = simplexml_load_file( $dp_xml_location );

		// If reading the DataProcessing.xml file failed, log the error
		// TODO determine if we should throw an exception here...
		if ( !$dpXMLObject ) {
			eGlooLogger::writeLog( eGlooLogger::EMERGENCY,
				'XML2ArrayDPDefinitionParser: simplexml_load_file( "' . $dp_xml_location . '" ): ' . libxml_get_errors() );
		}

		$eglooXMLObj = new eGlooXML( $dpXMLObject );

		// Setup an array to hold all of our processed DPStatement definitions
		$dynamicObjects = $this->loadDataProcessingDynamicObjects( $eglooXMLObj, $overwrite );

		// Setup an array to hold all of our processed DPProcedure definitions
		$dataProcessingProcedures = array();

		// Setup an array to hold all of our processed DPSequence definitions
		$dataProcessingSequences = $this->loadDataProcessingSequences( $eglooXMLObj, $overwrite );

		// Setup an array to hold all of our processed DPStatement definitions
		$dataProcessingStatements = $this->loadDataProcessingStatements( $eglooXMLObj, $overwrite );

		$retVal = array(
			'dataProcessingDynamicObjects' => $dynamicObjects,
			'dataProcessingProcedures' => $dataProcessingProcedures,
			'dataProcessingSequences' => $dataProcessingSequences,
			'dataProcessingStatements' => $dataProcessingStatements
		);

		// Mark successful completion of this method so that when debugging we can more accurately trace control flow
		eGlooLogger::writeLog( eGlooLogger::DEBUG, "XML2ArrayDPDefinitionParser: DataProcessing.xml successfully processed", 'DataProcessing' );

		return $retVal;
	}

	protected function loadDataProcessingDynamicObjects( $dpXMLObject, $overwrite = true ) {
		$retVal = null;

		// Grab the cache handler specifically for this cache region.  We do this so that when we write to the cache for DataProcessing
		// we can also write some information to the caching system to better keep track of what is cached for the DataProcessing system
		// and do more granulated inspection and cache clearing
		$dpCacheRegionHandler = CacheManagementDirector::getCacheRegionHandler('DataProcessing');

		$dynamicObjects = array();

// getSimpleXMLObject
		// Iterate over each DPStatementClass
		foreach( $dpXMLObject->xpath( '/tns:DataProcessing/DPDynamicObjects/DPDynamicObject' ) as $dynamicObject ) {
			// Grab the ID for this particular DPDynamicObject
			$objectID = isset($dynamicObject->id) ? (string) $dynamicObject->id : null;

			// If no ID is set for this DPDynamicObject, this is not a valid DataProcessing.xml and we should get out of here
			if ( !$objectID || trim($objectID) === '' ) {
				throw new ErrorException("No ID specified in DPDynamicObject. Please review your DataProcessing.xml");
			}

			$staticMethods = array();

			// Iterate over each DPDynamicObjectStaticMethod in this DPDynamicObject
			foreach( $dynamicObject->xpath( 'child::DPDynamicObjectStaticMethod' ) as $staticMethod ) {
				// Grab the ID for this particular DPDynamicObjectStaticMethod
				$staticMethodID = isset($staticMethod->id) ? (string) $staticMethod->id : null;

				// If no ID is set for this DPDynamicObjectStaticMethod, this is not a valid DataProcessing.xml and we should get out of here
				if ( !$staticMethodID || trim($staticMethodID) === '' ) {
					throw new ErrorException("No ID specified in DPDynamicObjectStaticMethod. Please review your DataProcessing.xml");
				}

				$staticMethodArguments = array();

				foreach( $staticMethod->xpath('child::DPDynamicObjectStaticMethodArguments/DPDynamicObjectStaticMethodArgument')
					as $staticMethodArgument ) {

					// If no ID is set for this DPDynamicObjectStaticMethodArgument, this is not a valid DataProcessing.xml and we should get out of here
					if ( !isset($staticMethodArgument->id) || trim((string) $staticMethodArgument->id) === '' ) {
						throw new ErrorException("No ID specified in DPDynamicObjectStaticMethodArgument. Please review your DataProcessing.xml");
					}

					$staticMethodArguments[(string) $staticMethodArgument->id] = array( 'id' => (string) $staticMethodArgument->id );
				}

				$staticMethodExecutionStatements = array();

				foreach( $staticMethod->xpath('child::DPDynamicObjectStaticMethodExecution/DPDynamicObjectStaticMethodExecutionStatement')
					as $staticMethodExecutionStatement ) {

					// Grab the order for this particular DPDynamicObjectStaticMethodExecutionStatement
					$staticMethodExecutionStatementOrder = isset($staticMethodExecutionStatement->order) ? (string) $staticMethodExecutionStatement->order : null;

					// If no order is set for this DPDynamicObjectStaticMethodExecutionStatement, this is not a valid DataProcessing.xml and we should get out of here
					if ( !$staticMethodExecutionStatementOrder || trim($staticMethodExecutionStatementOrder) === '' ) {
						throw new ErrorException("No order specified in DPDynamicObjectStaticMethodExecutionStatement. Please review your DataProcessing.xml");
					}

					$executionStatementDPStatements = array();

					foreach( $staticMethodExecutionStatement->xpath('child::DPStatement') as $dpStatement ) {
						$dpStatementClass = isset($dpStatement->class) ? (string) $dpStatement->class : null;

						if ( !$dpStatementClass || trim($dpStatementClass) === '' ) {
							throw new ErrorException('No class specified in DPStatement component of ' .
								'DPDynamicObjectStaticMethodExecutionStatement. Please review your DataProcessing.xml');
						}

						$dpStatementID = isset($dpStatement->statementID) ? (string) $dpStatement->statementID : null;

						if ( !$dpStatementID || trim($dpStatementID) === '' ) {
							throw new ErrorException('No statement ID specified in DPStatement component of ' .
								'DPDynamicObjectStaticMethodExecutionStatement. Please review your DataProcessing.xml');
						}

						$dpStatementType = isset($dpStatement->type) ? (string) $dpStatement->type : null;

						if ( !$dpStatementType || trim($dpStatementType) === '' ) {
							throw new ErrorException('No type specified in DPStatement component of ' .
								'DPDynamicObjectStaticMethodExecutionStatement. Please review your DataProcessing.xml');
						}

						$argumentMaps = array();

						foreach( $dpStatement->xpath('child::DPDynamicObjectMethodArgumentMap') as $argumentMap ) {
							$mapFrom = isset($argumentMap->from) ? (string) $argumentMap->from : null;

							if ( !$mapFrom || trim($mapFrom) === '' ) {
								throw new ErrorException('No "map from" specified in DPDynamicObjectMethodArgumentMap. Please review your DataProcessing.xml');
							}

							$mapTo = isset($argumentMap->to) ? (string) $argumentMap->to : null;

							if ( !$mapTo || trim($mapTo) === '' ) {
								throw new ErrorException('No "map to" specified in DPDynamicObjectMethodArgumentMap. Please review your DataProcessing.xml');
							}

							$argumentMaps[$mapTo] = array( 'from' => $mapFrom, 'to' => $mapTo );
						}

						$returnMaps = array();

						foreach( $dpStatement->xpath('child::DPDynamicObjectMethodReturnMap') as $returnMap ) {
							$mapFrom = isset($returnMap->from) ? (string) $returnMap->from : null;

							if ( !$mapFrom || trim($mapFrom) === '' ) {
								throw new ErrorException('No "map from" specified in DPDynamicObjectMethodReturnMap. Please review your DataProcessing.xml');
							}

							$mapTo = isset($returnMap->to) ? (string) $returnMap->to : null;

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

					$staticMethodExecutionStatements[$staticMethodExecutionStatementOrder] = array(
						'order' => $staticMethodExecutionStatementOrder,
						'dpStatements' => $executionStatementDPStatements,
					);
				}

				$staticMethods[$staticMethodID] =
					array(
						'id' => $staticMethodID,
						'arguments' => $staticMethodArguments,
						'executionStatements' => $staticMethodExecutionStatements,
					);
			}

			$objectMethods = array();

			// Iterate over each DPDynamicObjectMethod in this DPDynamicObject
			foreach( $dynamicObject->xpath( 'child::DPDynamicObjectMethod' ) as $objectMethod ) {
				// Grab the ID for this particular DPDynamicObjectMethod
				$objectMethodID = isset($objectMethod->id) ? (string) $objectMethod->id : null;

				// If no ID is set for this DPDynamicObjectMethod, this is not a valid DataProcessing.xml and we should get out of here
				if ( !$objectMethodID || trim($objectMethodID) === '' ) {
					throw new ErrorException("No ID specified in DPDynamicObjectMethod. Please review your DataProcessing.xml");
				}

				$objectMethodArguments = array();

				foreach( $objectMethod->xpath('child::DPDynamicObjectMethodArguments/DPDynamicObjectMethodArgument') as $objectMethodArgument ) {
					// Grab the ID for this particular DPDynamicObjectMethodArgument
					$objectMethodArgumentID = isset($objectMethodArgument->id) ? (string) $objectMethodArgument->id : null;

					// If no ID is set for this DPDynamicObjectMethodArgument, this is not a valid DataProcessing.xml and we should get out of here
					if ( !$objectMethodArgumentID || trim($objectMethodArgumentID) === '' ) {
						throw new ErrorException("No ID specified in DPDynamicObjectMethodArgument. Please review your DataProcessing.xml");
					}

					$objectMethodArguments[$objectMethodArgumentID] = array( 'id' => $objectMethodArgumentID );
				}

				$objectMethods[$objectMethodID] = array( 'id' => $objectMethodID, 'arguments' => $objectMethodArguments );
			}

			$objectConstants = array();

			// Iterate over each DPDynamicObjectConstant in this DPDynamicObject
			foreach( $dynamicObject->xpath( 'child::DPDynamicObjectConstant' ) as $constant ) {
				// Grab the ID for this particular DPDynamicObjectConstant
				$constantID = isset($constant->id) ? (string) $constant->id : null;

				// If no ID is set for this DPDynamicObjectConstant, this is not a valid DataProcessing.xml and we should get out of here
				if ( !$constantID || trim($constantID) === '' ) {
					throw new ErrorException("No ID specified in DPDynamicObjectConstant. Please review your DataProcessing.xml");
				}

				// Grab the type for this particular DPDynamicObjectConstant
				$constantType = isset($constant->type) ? (string) $constant->type : null;

				// Grab the default value for this particular DPDynamicObjectConstant
				$constantDefaultValue = isset($constant->defaultValue) ? (string) $constant->defaultValue : null;

				// Grab the default value type for this particular DPDynamicObjectConstant
				$constantDefaultValueType = isset($constant->defaultValueType) ? (string) $constant->defaultValueType : null;

				$objectConstants[$constantID] =
					array(
						'id' => $objectMethodID,
						'type' => $constantType,
						'defaultValue' => $constantDefaultValue,
						'defaultValueType' => $constantDefaultValueType,
					);
			}

			$objectStaticMembers = array();

			// Iterate over each DPDynamicObjectStaticMember in this DPDynamicObject
			foreach( $dynamicObject->xpath( 'child::DPDynamicObjectStaticMember' ) as $staticMember ) {
				// Grab the ID for this particular DPDynamicObjectStaticMember
				$staticMemberID = isset($staticMember->id) ? (string) $staticMember->id : null;

				// If no ID is set for this DPDynamicObjectStaticMember, this is not a valid DataProcessing.xml and we should get out of here
				if ( !$staticMemberID || trim($staticMemberID) === '' ) {
					throw new ErrorException("No ID specified in DPDynamicObjectStaticMember. Please review your DataProcessing.xml");
				}

				// Grab the type for this particular DPDynamicObjectStaticMember
				$staticMemberType = isset($staticMember->type) ? (string) $staticMember->type : null;

				// Grab the default value for this particular DPDynamicObjectStaticMember
				$staticMemberDefaultValue = isset($staticMember->defaultValue) ? (string) $staticMember->defaultValue : null;

				// Grab the default value type for this particular DPDynamicObjectStaticMember
				$staticMemberDefaultValueType = isset($staticMember->defaultValueType) ? (string) $staticMember->defaultValueType : null;

				// Grab the scope for this particular DPDynamicObjectStaticMember
				$staticMemberScope = isset($staticMember->scope) ? (string) $staticMember->scope : null;

				$objectStaticMembers[$staticMemberID] =
					array(
						'id' => $staticMemberID,
						'type' => $staticMemberType,
						'defaultValue' => $staticMemberDefaultValue,
						'defaultValueType' => $staticMemberDefaultValueType,
						'scope' => $staticMemberScope,
					);
			}

			$objectMembers = array();

			// Iterate over each DPDynamicObjectMember in this DPDynamicObject
			foreach( $dynamicObject->xpath( 'child::DPDynamicObjectMember' ) as $member ) {
				// Grab the ID for this particular DPDynamicObjectStaticMember
				$memberID = isset($member->id) ? (string) $member->id : null;

				// If no ID is set for this DPDynamicObjectMember, this is not a valid DataProcessing.xml and we should get out of here
				if ( !$memberID || trim($memberID) === '' ) {
					throw new ErrorException("No ID specified in DPDynamicObjectMember. Please review your DataProcessing.xml");
				}

				// Grab the type for this particular DPDynamicObjectMember
				$memberType = isset($member->type) ? (string) $member->type : null;

				// Grab the default value for this particular DPDynamicObjectMember
				$memberDefaultValue = isset($member->defaultValue) ? (string) $member->defaultValue : null;

				// Grab the default value type for this particular DPDynamicObjectMember
				$memberDefaultValueType = isset($member->defaultValueType) ? (string) $member->defaultValueType : null;

				// Grab the scope for this particular DPDynamicObjectMember
				$memberScope = isset($member->scope) ? (string) $member->scope : null;

				// Grab the scope for this particular DPDynamicObjectMember
				$memberManaged = isset($member->managed) ? (string) $member->managed : null;

				if ( strtolower($memberManaged) === 'true' ) {
					$memberManaged = true;
				} else {
					$memberManaged = false;
				}

				$objectMembers[$memberID] =
					array(
						'id' => $memberID,
						'type' => $memberType,
						'defaultValue' => $memberDefaultValue,
						'defaultValueType' => $memberDefaultValueType,
						'scope' => $memberScope,
						'managed' => $memberManaged,
					);
			}

			// Assign an array to hold this DPStatementClass node definition.  Associative key is the DPStatementClass ID
			$dynamicObjects[$objectID] =
				array(	'dynamicObjectID' => $objectID,
						'staticMethods' => $staticMethods,
						'methods' => $objectMethods,
						'constants' => $objectConstants,
						'staticMembers' => $objectStaticMembers,
						'members' => $objectMembers,
				);

			if ( $overwrite ) {
				$dpCacheRegionHandler->storeObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'XML2ArrayDPDefinitionParserDynamicObjectNodes::' .
					$objectID, $dynamicObjects[$objectID], 'DataProcessing', 0, true );
			}
		}

		if ( $overwrite ) {
			$this->_dataProcessingDynamicObjects = array( 'objects' => $dynamicObjects );

			$dpCacheRegionHandler->storeObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'XML2ArrayDPDefinitionParserDynamicObjectNodes',
				$this->_dataProcessingDynamicObjects, 'DataProcessing', 0, true );

			$dpCacheRegionHandler->storeObject( 
				eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'XML2ArrayDPDefinitionParser::DynamicObjectNodesCached', true, 'DataProcessing', 0, true );
		}

		$retVal = $dynamicObjects;

		return $retVal;
	}

	protected function loadDataProcessingSequences( $dpXMLObject, $overwrite = true ) {
		$retVal = null;

		$dataProcessingSequences = array();

		// Iterate over the DPSequence nodes so that we can parse each DPSequence definition
		foreach( $dpXMLObject->xpath( '/tns:DataProcessing/DPSequence' ) as $dpSequence ) {
			// Grab the ID for this particular DPSequence
			$dpSequenceID = isset($dpSequence->id) ? (string) $dpSequence->id : null;

			// If no ID is set for this DPSequence, this is not a valid DataProcessing.xml and we should get out of here
			if ( !$dpSequenceID || trim($dpSequenceID) === '' ) {
				throw new ErrorException("No ID specified in DPSequence. Please review your DataProcessing.xml");
			}

			// Assign an array to hold this DPSequence node definition.  Associative key is the DPSequence ID
			$dataProcessingSequences[$dpSequenceID] = array('dataProcessingSequence' => $dpSequenceID, 'attributes' => array());

			// TODO

			if ( $overwrite ) {
				$uniqueKey = ((string) $dpSequence->id);
				$this->_dataProcessingSequences[ $uniqueKey ] = $dataProcessingSequences[$dpSequenceID];

				// Grab the cache handler specifically for this cache region.  We do this so that when we write to the cache for DataProcessing
				// we can also write some information to the caching system to better keep track of what is cached for the DataProcessing system
				// and do more granulated inspection and cache clearing
				$dpCacheRegionHandler = CacheManagementDirector::getCacheRegionHandler('DataProcessing');

				$dpCacheRegionHandler->storeObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'XML2ArrayDPDefinitionParserAttributeNodes::' .
					$uniqueKey, $dataProcessingSequences[$dpSequenceID], 'DataProcessing', 0, true );
			}
		}

		if ( $overwrite ) {
			// Grab the cache handler specifically for this cache region.  We do this so that when we write to the cache for DataProcessing
			// we can also write some information to the caching system to better keep track of what is cached for the DataProcessing system
			// and do more granulated inspection and cache clearing
			$dpCacheRegionHandler = CacheManagementDirector::getCacheRegionHandler('DataProcessing');

			// We're done processing our DPSequences, so let's store the structured array in cache for faster lookup
			// For cache properties, the ttl is forever (0) and we can keep the cache piping hot by storing a local copy (true)
			$dpCacheRegionHandler->storeObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'XML2ArrayDPDefinitionParserAttributeSets',
				$this->_dataProcessingSequences, 'DataProcessing', 0, true );
		}

		$retVal = $dataProcessingSequences;

		return $retVal;
	}

	protected function loadDataProcessingStatements( $dpXMLObject, $overwrite = true ) {
		$retVal = null;

		// Grab the cache handler specifically for this cache region.  We do this so that when we write to the cache for DataProcessing
		// we can also write some information to the caching system to better keep track of what is cached for the DataProcessing system
		// and do more granulated inspection and cache clearing
		$dpCacheRegionHandler = CacheManagementDirector::getCacheRegionHandler('DataProcessing');

		$dataProcessingStatements = array();

		// Iterate over each DPStatementClass
		foreach( $dpXMLObject->xpath( '/tns:DataProcessing/DPStatements/DPStatementClass' ) as $statementClass ) {
			// Grab the ID for this particular DPStatementClass
			$statementClassID = isset($statementClass->id) ? (string) $statementClass->id : null;

			// If no ID is set for this DPStatementClass, this is not a valid DataProcessing.xml and we should get out of here
			if ( !$statementClassID || trim($statementClassID) === '' ) {
				throw new ErrorException("No ID specified in DPStatementClass. Please review your DataProcessing.xml");
			}

			$classStatements = array();

			// Iterate over each DPStatement in this DPStatementClass
			foreach( $statementClass->xpath( 'child::DPStatement' ) as $dataProcessingStatement ) {
				// Grab the ID for this particular DPStatement
				$statementID = isset($dataProcessingStatement->id) ? (string) $dataProcessingStatement->id : null;

				// If no ID is set for this DPStatement, this is not a valid DataProcessing.xml and we should get out of here
				if ( !$statementID || trim($statementID) === '' ) {
					throw new ErrorException("No ID specified in DPStatement. Please review your DataProcessing.xml");
				}

				// Grab the type for this particular DPStatement
				$dataProcessingStatementType = isset($dataProcessingStatement->type) ? (string) $dataProcessingStatement->type : null;

				// If no type is set for this DPStatement, this is not a valid DataProcessing.xml and we should get out of here
				if ( !$dataProcessingStatementType || trim($dataProcessingStatementType) === '' ) {
					throw new ErrorException("No type specified in DPStatement. Please review your DataProcessing.xml");
				}

				$classStatements[$statementID] = array( 'id' => $statementID, 'type' => $dataProcessingStatementType );

				$argumentLists = array();

				if ( $dataProcessingStatement->xpath( 'child::DPStatementArgumentLists/DPStatementArgumentList' ) ) {
					foreach( $dataProcessingStatement->xpath( 'child::DPStatementArgumentLists/DPStatementArgumentList' ) as $dpStatementArgumentList ) {
						$argumentListID = isset($dpStatementArgumentList->argumentListID) ? (string) $dpStatementArgumentList->argumentListID : null;
						$parameterPreparation = isset($dpStatementArgumentList->parameterPreparation) ? (string) $dpStatementArgumentList->parameterPreparation : null;

						$argumentLists[$argumentListID] = array( 'argumentListID' => $argumentListID, 'parameterPreparation' => $parameterPreparation );

						$arguments = array();

						if ( $dpStatementArgumentList->xpath( 'child::DPStatementArgument') ) {
							foreach( $dpStatementArgumentList->xpath( 'child::DPStatementArgument') as $dpStatementArgument ) {
								$argumentID = isset($dpStatementArgument->id) ? (string) $dpStatementArgument->id : null;
								$argumentType = isset($dpStatementArgument->type) ? (string) $dpStatementArgument->type : null;

								$arguments[$argumentID] = array( 'argumentID' => $argumentID, 'argumentType' => $argumentType );

								if ( $argumentType === 'integer' ) {
									$arguments[$argumentID]['min'] = isset($dpStatementArgument->min) ? (string) $dpStatementArgument->min : null;
									$arguments[$argumentID]['max'] = isset($dpStatementArgument->max) ? (string) $dpStatementArgument->max : null;
								} else if ( $argumentType === 'string' ) {
									$arguments[$argumentID]['pattern'] = isset($dpStatementArgument->pattern) ? (string) $dpStatementArgument->pattern : null;
								}
							}
						}

						$argumentLists[$argumentListID]['arguments'] = $arguments;
					}
				}

				$classStatements[$statementID]['argumentLists'] = $argumentLists;

				$statementReturn = array();

				if ( $dataProcessingStatement->xpath( 'child::DPStatementReturn' ) ) {
					foreach( $dataProcessingStatement->xpath( 'child::DPStatementReturn' ) as $dpStatementReturn ) {
						$dpStatementReturnType = isset($dpStatementReturn->type) ? (string) $dpStatementReturn->type : null;

						$statementReturn['type'] = $dpStatementReturnType;

						$statementReturnColumnSets = array();

						if ( $dpStatementReturn->xpath( 'child::DPStatementReturnColumnSet' ) ) {
							foreach( $dpStatementReturn->xpath( 'child::DPStatementReturnColumnSet' ) as $returnColumnSet ) {
								$returnColumnSetTable = isset($returnColumnSet->table) ? (string) $returnColumnSet->table : null;
								$returnColumnSetPattern = isset($returnColumnSet->pattern) ? (string) $returnColumnSet->pattern : null;
								$returnColumnSetType = isset($returnColumnSet->type) ? (string) $returnColumnSet->type : null;

								$statementReturnColumnSets[$returnColumnSetTable] =
									array( 'table' => $returnColumnSetTable, 'pattern' => $returnColumnSetPattern, 'type' => $returnColumnSetType );
							}
						}

						$statementReturn['statementReturnColumnSets'] = $statementReturnColumnSets;

						$statementReturnColumns = array();

						if ( $dpStatementReturn->xpath( 'child::DPStatementReturnColumn' ) ) {
							foreach( $dpStatementReturn->xpath( 'child::DPStatementReturnColumn' ) as $returnColumn ) {
								$returnColumnID = isset($returnColumn->id) ? (string) $returnColumn->id : null;
								$returnColumnType = isset($returnColumn->type) ? (string) $returnColumn->type : null;

								$statementReturnColumns[$returnColumnID] = array( 'id' => $returnColumnID, 'type' => $returnColumnType );

								if ( $returnColumnType === 'integer' ) {
									$statementReturnColumns[$returnColumnID]['min'] = isset($returnColumn->min) ? (string) $returnColumn->min : null;
									$statementReturnColumns[$returnColumnID]['max'] = isset($returnColumn->max) ? (string) $returnColumn->max : null;
								} else if ( $returnColumnType === 'string' ) {
									$statementReturnColumns[$returnColumnID]['pattern'] = isset($returnColumn->pattern) ? (string) $returnColumn->pattern : null;
								}
							}
						}

						$statementReturn['statementReturnColumns'] = $statementReturnColumns;
					}
				}

				$classStatements[$statementID]['statementReturn'] = $statementReturn;

				$statementVariants = array();

				if ( $dataProcessingStatement->xpath( 'child::DPStatementVariants/DPStatementVariant' ) ) {
					foreach( $dataProcessingStatement->xpath( 'child::DPStatementVariants/DPStatementVariant' ) as $dpStatementVariant ) {
						$variantConnection = isset($dpStatementVariant->connection) ? (string) $dpStatementVariant->connection : null;

						$statementVariant = array( 'connection' => $variantConnection );

						$variantEngineModes = array();

						if ( $dpStatementVariant->xpath( 'child::DPStatementVariantEngineMode' ) ) {
							foreach( $dpStatementVariant->xpath( 'child::DPStatementVariantEngineMode' ) as $variantEngineMode ) {
								$variantEngineModeMode = isset($variantEngineMode->mode) ?
									eGlooConfiguration::getEngineModeFromString( (string) $variantEngineMode->mode ) : null;
								$variantEngineModeName = isset($variantEngineMode->mode) ? (string) $variantEngineMode->mode : null;

								$variantEngineModes[$variantEngineModeMode] =
									array( 'mode' => $variantEngineModeMode, 'modeName' => $variantEngineModeName );

								$includePaths = array();

								if ( $variantEngineMode->xpath( 'child::DPStatementIncludePath' ) ) {
									foreach( $variantEngineMode->xpath( 'child::DPStatementIncludePath' ) as $includePath ) {
										$includePathArgumentList = isset($includePath->argumentList) ? (string) $includePath->argumentList : null;

										$includePathValue = trim( (string) $includePath );

										$includePaths[] = array( 'argumentList' => $includePathArgumentList, 'includePath' => $includePathValue );
									}
								}

								$variantEngineModes[$variantEngineModeMode]['includePaths'] = $includePaths;
							}
						}

						$statementVariant['engineModes'] = $variantEngineModes;
						$statementVariants[$variantConnection] = $statementVariant;
					}
				}

				$classStatements[$statementID]['statementVariants'] = $statementVariants;

				$statementConstraints = array();

				if ( $dataProcessingStatement->xpath( 'child::DPStatementConstraints/DPStatementConstraint' ) ) {
					foreach( $dataProcessingStatement->xpath( 'child::DPStatementConstraints/DPStatementConstraint' ) as $constraint ) {
						$constraintType = isset($constraint->type) ? (string) $constraint->type : null;
						$constraintMin = isset($constraint->min) ? (string) $constraint->min : null;
						$constraintMax = isset($constraint->max) ? (string) $constraint->max : null;
						$constraintGranularity = isset($constraint->granularity) ? (string) $constraint->granularity : null;
						$constraintLevel = isset($constraint->level) ? (string) $constraint->level : null;

						$statementConstraints[] = array(	'type' => $constraintType,
															'min' => $constraintMin,
															'max' => $constraintMax,
															'granularity' => $constraintGranularity,
															'level', $constraintLevel,
						);
					}
				}

				$classStatements[$statementID]['statementConstraints'] = $statementConstraints;
			}

			// Assign an array to hold this DPStatementClass node definition.  Associative key is the DPStatementClass ID
			$dataProcessingStatements[$statementClassID] = array( 'statementClass' => $statementClassID, 'statements' => $classStatements );

			if ( $overwrite ) {
				$dpCacheRegionHandler->storeObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'XML2ArrayDPDefinitionParserStatementNodes::' .
					$statementClassID, $dataProcessingStatements[$statementClassID], 'DataProcessing', 0, true );
			}
		}

		if ( $overwrite ) {
			$this->_dataProcessingStatements = $dataProcessingStatements;

			$dpCacheRegionHandler->storeObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'XML2ArrayDPDefinitionParserStatementNodes',
				$this->_dataProcessingStatements, 'DataProcessing', 0, true );

			$dpCacheRegionHandler->storeObject( 
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
