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
 * Validates requests against specification from requests definition file (DataProcessing.xml)
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

	public function getDPProcedureDefinition( $procedure_class, $procedure_id ) {
		$retVal = null;

		if ( !isset( $this->dataProcessingProcedures ) ) {
			$this->loadDataProcessingNodes();
		}

		if ( isset( $this->dataProcessingProcedures[$procedure_class]['procedures'][$procedure_id] ) ) {
			$retVal = $this->dataProcessingProcedures[$procedure_class]['procedures'][$procedure_id];
		}

		return $retVal;
	}

	public function getDPSequenceDefinition( $sequence_class, $sequence_id ) {
		$retVal = null;

		if ( !isset( $this->dataProcessingSequences ) ) {
			$this->loadDataProcessingNodes();
		}

		if ( isset( $this->dataProcessingSequences[$sequence_class]['sequences'][$sequence_id] ) ) {
			$retVal = $this->dataProcessingSequences[$sequence_class]['sequences'][$sequence_id];
		}

		return $retVal;
	}

	public function getDPStatementDefinition( $statement_class, $statement_id ) {
		$retVal = null;

		if ( !isset( $this->dataProcessingStatements ) ) {
			$this->loadDataProcessingNodes();
		}

		if ( isset( $this->dataProcessingStatements[$statement_class]['statements'][$statement_id] ) ) {
			$retVal = $this->dataProcessingStatements[$statement_class]['statements'][$statement_id];
		}

		return $retVal;
	}

	/**
	 * Method to load data processing nodes from DataProcessing.xml definitions file
	 * 
	 * @throws ErrorException	if definition file cannot be read, has syntax errors, is missing
	 *							required values or provides invalid values
	 */
	protected function loadDataProcessingNodes() {
		// Mark entrance into this method so that when debugging we can more accurately trace control flow
		eGlooLogger::writeLog( eGlooLogger::DEBUG, "XML2ArrayDPDefinitionParser: Entered loadRequestNodes()", 'DataProcessing' );

		// Grab the absolute file system path to the DataProcessing.xml we're concerned with.  $this->webapp is set
		// during construction of this XML2ArrayDPDefinitionParser singleton.  See eGlooDPDefinitionParser
		// for details.
		$dp_xml_path = eGlooConfiguration::getApplicationsPath() . '/' . eGlooConfiguration::getApplicationPath() . "/XML/DataProcessing.xml";

		// Mark that we are now attempting to load the specified DataProcessing.xml
		eGlooLogger::writeLog( eGlooLogger::DEBUG, "XML2ArrayDPDefinitionParser: Loading " . $dp_xml_path, 'DataProcessing' );

		// Attempt to load the specified DataProcessing.xml file
		$dataProcessingXMLObject = simplexml_load_file( $dp_xml_path );

		// If reading the DataProcessing.xml file failed, log the error
		// TODO determine if we should throw an exception here...
		if ( !$dataProcessingXMLObject ) {
			eGlooLogger::writeLog( eGlooLogger::EMERGENCY,
				'XML2ArrayDPDefinitionParser: simplexml_load_file( "' . $dp_xml_path . '" ): ' . libxml_get_errors() );
		}

		// Setup an array to hold all of our processed DPSequence definitions
		$dataProcessingSequences = $this->loadDataProcessingSequences( $dataProcessingXMLObject );

		// Setup an array to hold all of our processed DPStatement definitions
		$dataProcessingStatements = $this->loadDataProcessingStatements( $dataProcessingXMLObject );

		// Mark successful completion of this method so that when debugging we can more accurately trace control flow
		eGlooLogger::writeLog( eGlooLogger::DEBUG, "XML2ArrayDPDefinitionParser: DataProcessing.xml successfully processed", 'DataProcessing' );
	}

	protected function loadDataProcessingSequences( $dataProcessingXMLObject ) {
		$retVal = null;

		$dataProcessingSequences = array();

		// Iterate over the DPSequence nodes so that we can parse each DPSequence definition
		foreach( $dataProcessingXMLObject->xpath( '/tns:DataProcessing/DPSequence' ) as $dpSequence ) {
			// Grab the ID for this particular DPSequence
			$dpSequenceID = isset($dpSequence['id']) ? (string) $dpSequence['id'] : NULL;

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

			$uniqueKey = ((string) $dpSequence['id']);
			$this->dataProcessingSequences[ $uniqueKey ] = $dataProcessingSequences[$dpSequenceID];

			// Grab the cache handler specifically for this cache region.  We do this so that when we write to the cache for DataProcessing
			// we can also write some information to the caching system to better keep track of what is cached for the DataProcessing system
			// and do more granulated inspection and cache clearing
			$dataProcessingCacheRegionHandler = CacheManagementDirector::getCacheRegionHandler('DataProcessing');

			$dataProcessingCacheRegionHandler->storeObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'XML2ArrayDPDefinitionParserAttributeNodes::' .
				$uniqueKey, $dataProcessingSequences[$dpSequenceID], 'DataProcessing', 0, true );
		}

		// Grab the cache handler specifically for this cache region.  We do this so that when we write to the cache for DataProcessing
		// we can also write some information to the caching system to better keep track of what is cached for the DataProcessing system
		// and do more granulated inspection and cache clearing
		$dataProcessingCacheRegionHandler = CacheManagementDirector::getCacheRegionHandler('DataProcessing');

		// We're done processing our DPSequences, so let's store the structured array in cache for faster lookup
		// For cache properties, the ttl is forever (0) and we can keep the cache piping hot by storing a local copy (true)
		$dataProcessingCacheRegionHandler->storeObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'XML2ArrayDPDefinitionParserAttributeSets',
			$this->dataProcessingSequences, 'DataProcessing', 0, true );

		$retVal = $dataProcessingSequences;

		return $retVal;
	}

	protected function loadDataProcessingStatements( $dataProcessingXMLObject ) {
		$retVal = null;

		$dataProcessingStatements = array();

		// Iterate over each DPStatementClass
		foreach( $dataProcessingXMLObject->xpath( '/tns:DataProcessing/DPStatements/DPStatementClass' ) as $dataProcessingStatementClass ) {
			// Grab the ID for this particular DPStatementClass
			$dataProcessingStatementClassID = isset($dataProcessingStatementClass['id']) ? (string) $dataProcessingStatementClass['id'] : NULL;

			// If no ID is set for this DPStatementClass, this is not a valid DataProcessing.xml and we should get out of here
			if ( !$dataProcessingStatementClassID || trim($dataProcessingStatementClassID) === '' ) {
				throw new ErrorException("No ID specified in DPStatementClass. Please review your DataProcessing.xml");
			}

			$dpStatementClassStatements = array();

			// Iterate over each DPStatement in this DPStatementClass
			foreach( $dataProcessingStatementClass->xpath( 'child::DPStatement' ) as $dataProcessingStatement ) {
				// Grab the ID for this particular DPStatement
				$dataProcessingStatementID = isset($dataProcessingStatement['id']) ? (string) $dataProcessingStatement['id'] : NULL;

				// If no ID is set for this DPStatement, this is not a valid DataProcessing.xml and we should get out of here
				if ( !$dataProcessingStatementID || trim($dataProcessingStatementID) === '' ) {
					throw new ErrorException("No ID specified in DPStatement. Please review your DataProcessing.xml");
				}

				// Grab the type for this particular DPStatement
				$dataProcessingStatementType = isset($dataProcessingStatement['type']) ? (string) $dataProcessingStatement['type'] : NULL;

				// If no type is set for this DPStatement, this is not a valid DataProcessing.xml and we should get out of here
				if ( !$dataProcessingStatementType || trim($dataProcessingStatementType) === '' ) {
					throw new ErrorException("No type specified in DPStatement. Please review your DataProcessing.xml");
				}

				$dpStatementClassStatements[$dataProcessingStatementID] = array( 'id' => $dataProcessingStatementID, 'type' => $dataProcessingStatementType );

				$argumentLists = array();

				foreach( $dataProcessingStatement->xpath( 'child::DPStatementArgumentLists/DPStatementArgumentList' ) as $dpStatementArgumentList ) {
					$argumentListID = isset($dpStatementArgumentList['argumentListID']) ? (string) $dpStatementArgumentList['argumentListID'] : NULL;
					$parameterPreparation = isset($dpStatementArgumentList['parameterPreparation']) ? (string) $dpStatementArgumentList['parameterPreparation'] : NULL;

					$argumentLists[$argumentListID] = array( 'argumentListID' => $argumentListID, 'parameterPreparation' => $parameterPreparation );

					$arguments = array();

					foreach( $dpStatementArgumentList->xpath( 'child::DPStatementArgument') as $dpStatementArgument ) {
						$argumentID = isset($dpStatementArgument['id']) ? (string) $dpStatementArgument['id'] : NULL;
						$argumentType = isset($dpStatementArgument['type']) ? (string) $dpStatementArgument['type'] : NULL;

						$arguments[$argumentID] = array( 'argumentID' => $argumentID, 'argumentType' => $argumentType );

						if ( $argumentType === 'integer' ) {
							$arguments[$argumentID]['min'] = isset($dpStatementArgument['min']) ? (string) $dpStatementArgument['min'] : NULL;
							$arguments[$argumentID]['max'] = isset($dpStatementArgument['max']) ? (string) $dpStatementArgument['max'] : NULL;
						} else if ( $argumentType === 'string' ) {
							$arguments[$argumentID]['pattern'] = isset($dpStatementArgument['pattern']) ? (string) $dpStatementArgument['pattern'] : NULL;
						}
					}

					$argumentLists[$argumentListID]['arguments'] = $arguments;
				}

				$dpStatementClassStatements[$dataProcessingStatementID]['argumentLists'] = $argumentLists;

				$statementReturn = array();

				foreach( $dataProcessingStatement->xpath( 'child::DPStatementReturn' ) as $dpStatementReturn ) {
					$dpStatementReturnType = isset($dpStatementReturn['type']) ? (string) $dpStatementReturn['type'] : NULL;

					$statementReturn['type'] = $dpStatementReturnType;

					$statementReturnColumnSets = array();

					foreach( $dpStatementReturn->xpath( 'child::DPStatementReturnColumnSet' ) as $dpStatementReturnColumnSet ) {
						$dpStatementReturnColumnSetTable = isset($dpStatementReturnColumnSet['table']) ? (string) $dpStatementReturnColumnSet['table'] : NULL;
						$dpStatementReturnColumnSetPattern = isset($dpStatementReturnColumnSet['pattern']) ? (string) $dpStatementReturnColumnSet['pattern'] : NULL;
						$dpStatementReturnColumnSetType = isset($dpStatementReturnColumnSet['type']) ? (string) $dpStatementReturnColumnSet['type'] : NULL;
						
						$statementReturnColumnSets[$dpStatementReturnColumnSetTable] =
							array( 'table' => $dpStatementReturnColumnSetTable, 'pattern' => $dpStatementReturnColumnSetPattern, 'type' => $dpStatementReturnColumnSetType );
					}

					$statementReturn['statementReturnColumnSets'] = $statementReturnColumnSets;

					$statementReturnColumns = array();

					foreach( $dpStatementReturn->xpath( 'child::DPStatementReturnColumn' ) as $dpStatementReturnColumn ) {
						$dpStatementReturnColumnID = isset($dpStatementReturnColumn['id']) ? (string) $dpStatementReturnColumn['id'] : NULL;
						$dpStatementReturnColumnType = isset($dpStatementReturnColumn['type']) ? (string) $dpStatementReturnColumn['type'] : NULL;
						
						$statementReturnColumns[$dpStatementReturnColumnID] = array( 'id' => $dpStatementReturnColumnID, 'type' => $dpStatementReturnColumnType );

						if ( $dpStatementReturnColumnType === 'integer' ) {
							$statementReturnColumns[$dpStatementReturnColumnID]['min'] =
								isset($dpStatementReturnColumn['min']) ? (string) $dpStatementReturnColumn['min'] : NULL;
							$statementReturnColumns[$dpStatementReturnColumnID]['max'] =
								isset($dpStatementReturnColumn['max']) ? (string) $dpStatementReturnColumn['max'] : NULL;
						} else if ( $dpStatementReturnColumnType === 'string' ) {
							$statementReturnColumns[$dpStatementReturnColumnID]['pattern'] =
								isset($dpStatementReturnColumn['pattern']) ? (string) $dpStatementReturnColumn['pattern'] : NULL;
						}
					}

					$statementReturn['statementReturnColumns'] = $statementReturnColumns;
				}

				$dpStatementClassStatements[$dataProcessingStatementID]['statementReturn'] = $statementReturn;

				$statementVariants = array();

				foreach( $dataProcessingStatement->xpath( 'child::DPStatementVariants/DPStatementVariant' ) as $dpStatementVariant ) {
					$dpStatementVariantConnection = isset($dpStatementVariant['connection']) ? (string) $dpStatementVariant['connection'] : NULL;

					$statementVariant = array( 'connection' => $dpStatementVariantConnection );

					$statementVariantEngineModes = array();

					foreach( $dpStatementVariant->xpath( 'child::DPStatementVariantEngineMode' ) as $dpStatementVariantEngineMode ) {
						$dpStatementVariantEngineModeMode = isset($dpStatementVariantEngineMode['mode']) ? (string) $dpStatementVariantEngineMode['mode'] : NULL;

						$statementVariantEngineModes[$dpStatementVariantEngineModeMode] = array( 'mode' => $dpStatementVariantEngineModeMode );

						$includePaths = array();

						foreach( $dpStatementVariantEngineMode->xpath( 'child::DPStatementIncludePath' ) as $dpStatementIncludePath ) {
							$dpStatementIncludePathArgumentList = isset($dpStatementIncludePath['argumentList']) ? (string) $dpStatementIncludePath['argumentList'] : NULL;

							$dpStatementIncludePathValue = (string) $dpStatementIncludePath;

							$includePaths[] = array( 'argumentList' => $dpStatementIncludePathArgumentList, 'includePath' => $dpStatementIncludePathValue );
						}

						$statementVariantEngineModes[$dpStatementVariantEngineModeMode]['includePaths'] = $includePaths;
					}

					$statementVariant['engineModes'] = $statementVariantEngineModes;

					$statementVariants[$dpStatementVariantConnection] = $statementVariant;
				}

				$dpStatementClassStatements[$dataProcessingStatementID]['statementVariants'] = $statementVariants;

				$statementConstraints = array();

				foreach( $dataProcessingStatement->xpath( 'child::DPStatementConstraints/DPStatementConstraint' ) as $dpStatementConstraint ) {
					$dpStatementConstraintType = isset($dpStatementConstraint['type']) ? (string) $dpStatementConstraint['type'] : NULL;
					$dpStatementConstraintMin = isset($dpStatementConstraint['min']) ? (string) $dpStatementConstraint['min'] : NULL;
					$dpStatementConstraintMax = isset($dpStatementConstraint['max']) ? (string) $dpStatementConstraint['max'] : NULL;
					$dpStatementConstraintGranularity = isset($dpStatementConstraint['granularity']) ? (string) $dpStatementConstraint['granularity'] : NULL;
					$dpStatementConstraintLevel = isset($dpStatementConstraint['level']) ? (string) $dpStatementConstraint['level'] : NULL;

					$statementConstraints[] = array(	'type' => $dpStatementConstraintType,
														'min' => $dpStatementConstraintMin,
														'max' => $dpStatementConstraintMax,
														'granularity' => $dpStatementConstraintGranularity,
														'level', $dpStatementConstraintLevel,
					);
				}

				$dpStatementClassStatements[$dataProcessingStatementID]['statementConstraints'] = $statementConstraints;
			}

			// Assign an array to hold this DPStatementClass node definition.  Associative key is the DPStatementClass ID
			$dataProcessingStatements[$dataProcessingStatementClassID] = array( 'statementClass' => $dataProcessingStatementClassID, 'statements' => $dpStatementClassStatements );
		}

		$this->dataProcessingStatements = $dataProcessingStatements;
// die_r($this->dataProcessingStatements);
		return $this->dataProcessingStatements;
// echo_r($dataProcessingStatements);
// die;

		// Grab the cache handler specifically for this cache region.  We do this so that when we write to the cache for DataProcessing
		// we can also write some information to the caching system to better keep track of what is cached for the DataProcessing system
		// and do more granulated inspection and cache clearing
		$dataProcessingCacheRegionHandler = CacheManagementDirector::getCacheRegionHandler('DataProcessing');

		$dataProcessingCacheRegionHandler->storeObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'XML2ArrayDPDefinitionParserNodes',
			$this->dataProcessingStatements, 'DataProcessing', 0, true );

		$dataProcessingCacheRegionHandler->storeObject( 
			eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'XML2ArrayDPDefinitionParser::NodesCached', true, 'DataProcessing', 0, true );

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
