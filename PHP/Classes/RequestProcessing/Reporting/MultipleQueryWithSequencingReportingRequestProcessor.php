<?php
/**
 * MultipleQueryWithSequencingReportingRequestProcessor Class File
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
 * MultipleQueryWithSequencingReportingRequestProcessor
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
abstract class MultipleQueryWithSequencingReportingRequestProcessor extends MultipleQueryReportingRequestProcessor {

	/* Protected Data Members */
	protected $_executedQueries = array();
	protected $_populatedQueries = array();
	protected $_preparedQueries = array();
	protected $_preparedQueryStrings = array();
	protected $_queryExecutionSteps = array();
	protected $_querySequences = array();
	protected $_queryParameters = array();
	protected $_queryResultResources = array();
	protected $_rawDataReports = array();
	protected $_executedQueriesInFeederForm = array();
	protected $_executedQuerySequences = array();

	protected function populateTemplateVariables() {
		$this->prepareConnection();
		$this->setReportFilePaths();

		foreach($this->_queryExecutionSteps as $preparedQueryName => $executionStep) {
			// array( 'preparedQueryName' => array('eatsFrom' => array(0 => 'preparedQueryName1', 1 => 'preparedQueryName2'), 'parameters' => array('get'=>array(), 'post'=>array())))
			if (!isset($this->_executedQueries[$preparedQueryName])) {
				if (!empty($executionStep['eatsFrom'])) {
					foreach( $executionStep['eatsFrom'] as $subQuery ) {
						$this->processSubQuery($subQuery);
					}
				}

				if (!empty($executionStep['loopsOn'])) {
					$this->processSubQuery($executionStep['loopsOn']);
				}

				$this->processQuery($preparedQueryName);
			}
		}

		$this->structureRawDataReport();
		$this->setTemplateVariablesByMerge($this->getStructuredDataReport());
	}

	protected function processSubQuery( $subQuery ) {
		$preparedQueryString = $this->getPreparedQueryStringFromPath( $this->_queryExecutionSteps[$subQuery]['path'] );
		$preparedQuery = $this->prepareQuery( $preparedQueryString );
		$subQueryParameters = $this->prepareSubQueryParameters( $subQuery, $this->_queryExecutionSteps[$subQuery]['parameters']);
		$this->populateQuery( $preparedQuery, $subQueryParameters );
		$queryResponseTransaction = $this->executeQuery( $preparedQuery );
		$this->prepareRawDataReportByQueryName( $subQuery, $queryResponseTransaction );
		$this->_executedQueries[$subQuery] = $queryResponseTransaction->getDataPackage();

		$queryResultTransformRoutine = QueryTransformationManager::getQueryResponseTransactionTransformRoutine($queryResponseTransaction);

		$feederValue = $this->getTransformedFeederQueryArray($queryResponseTransaction);

		$this->_executedQueriesInFeederForm[$subQuery] = $feederValue;
	}

	protected function getTransformedFeederQueryArray( $queryResponseTransaction ) {
		$retVal = null;

		$queryResultTransformRoutine = QueryTransformationManager::getQueryResponseTransactionTransformRoutine($queryResponseTransaction);

		// I'd like to support multiple column feeders in the future.  For now, this works.
		$retVal = $queryResultTransformRoutine->getSingleColumnFeederStringFormat($queryResponseTransaction);

		return $retVal;
	}

	protected function getTransformedFeederQueryString( $queryResponseTransaction ) {
		$retVal = null;

		$queryResultTransformRoutine = QueryTransformationManager::getQueryResponseTransactionTransformRoutine($queryResponseTransaction);

		// I'd like to support multiple column feeders in the future.  For now, this works.
		$output = $queryResultTransformRoutine->getSingleColumnFeederStringFormat($queryResponseTransaction);
		$feederColumnKey = $output['column_key'];

		$feederValue = implode(', ', $output[$feederColumnKey]);
		
		$retVal = array($output['column_key'] => $feederValue);

		return $retVal;
	}

	protected function processQuery( $subQuery ) {
		$preparedQueryString = $this->getPreparedQueryStringFromPath( $this->_queryExecutionSteps[$subQuery]['path'] );
		$preparedQuery = $this->prepareQuery( $preparedQueryString );
		$subQueryParameters = $this->prepareSubQueryParameters($subQuery, $this->_queryExecutionSteps[$subQuery]['parameters']);
		$this->populateQuery( $preparedQuery, $subQueryParameters );
		$queryResponseTransaction = $this->executeQuery( $preparedQuery );
		$this->prepareRawDataReportByQueryName( $subQuery, $queryResponseTransaction );
		$this->_executedQueries[$subQuery] = $queryResponseTransaction->getDataPackage();
	}

	protected function prepareQuery( $preparedQueryString = null ) {
		$preparedQuery = new QueryTransaction($preparedQueryString);
		$preparedQuery->setQueryDialect(DialectLibrary::MYSQL);
		return $preparedQuery;
	}

	public function prepareSubQueryParameters( $queryName, $parametersNeeded ) {
		$queryParameters = array();

		if ( isset($this->_queryExecutionSteps[$queryName]['loopsOn']) ) {
			$loopsOn = $this->_queryExecutionSteps[$queryName]['loopsOn'];
		} else {
			$loopsOn = null;
		}

		if ( isset($this->_queryExecutionSteps[$queryName]['loopIndex']) ) {
			$loopIndex = $this->_queryExecutionSteps[$queryName]['loopIndex'];
		} else {
			$loopIndex = null;
		}

		if ( isset($this->_queryExecutionSteps[$queryName]['loopColumn']) ) {
			$loopColumn = $this->_queryExecutionSteps[$queryName]['loopColumn'];
		} else {
			$loopColumn = null;
		}

		if ($this->_queryExecutionSteps[$queryName]['parameterOrderMatters']) {
			// We might sort in the future, but for now let's assume it's coming in ordered
			foreach( $parametersNeeded as $parameter ) {
				if ( $parameter['source'] === 'GET' ) {
					$queryParameters[] = array('type' => $parameter['type'], 'value' => $this->requestInfoBean->getGET($parameter['value']));
				} else if ( $parameter['source'] === 'POST' ) {
					$queryParameters[] = array('type' => $parameter['type'], 'value' => $this->requestInfoBean->getPOST($parameter['value']));
				} else if ( $parameter['source'] === $loopsOn ) {
					$source = $parameter['source'];
echo_r($this->_executedQueriesInFeederForm[$source]);
					if ( isset($this->_executedQueriesInFeederForm[$source]) && isset($this->_executedQueriesInFeederForm[$source][$loopColumn]) ) {
						if ( isset($this->_executedQueriesInFeederForm[$source][$loopColumn][$loopIndex]) ) {
							$loopValue = $this->_executedQueriesInFeederForm[$source][$loopColumn][$loopIndex];
							$queryParameters[] = array('type' => $parameter['type'], 'value' => $loopValue);
						}
					}
				} else if ( isset($this->_queryExecutionSteps[$parameter['source']]) ) {
					$queryParameters[] = array('type' => $parameter['type'], 'value' => $this->_executedQueries[$subQuery][$parameter['value']]);
				} else {
					throw new Exception('Invalid query parameter source specified');
				}
			}
		}

		if ( isset($this->_queryExecutionSteps[$queryName]['loopIndex']) ) {
			$this->_queryExecutionSteps[$queryName]['loopIndex'] += 1;
		}
echo_r($queryParameters);
		return $queryParameters;
	}

	protected function prepareRawDataReportByQueryName( $preparedQueryName, $queryResponseTransaction ) {
		$this->_rawDataReports[ $preparedQueryName ] = $queryResponseTransaction->getDataPackage();
	}

	protected function setPreparedQueryStringFromDispatch() {
		// TODO
	}

	protected function setPreparedQueryStringFromPath( $preparedQueryStringPath ) {
		if ( file_exists($preparedQueryStringPath) && is_file($preparedQueryStringPath) && is_readable($preparedQueryStringPath) ) {
			$this->_preparedQueryString = file_get_contents($preparedQueryStringPath);
		} else {
			throw new Exception('Requested query path does not exist: ' . $preparedQueryStringPath);
		}
	}

	protected function setPreparedQueryStringFromString( $preparedQueryString ) {
		if ( is_string($preparedQueryString) ) {
			$this->_preparedQueryString = $preparedQueryString;
		} else {
			throw new Exception('BaseReportingRequestProcessor: setPreparedQueryStringFromString expected ' .
				'argument to be type string, received ' . gettype($preparedQueryString));
		}
	}

	protected function getPreparedQueryStringFromPath( $preparedQueryStringPath ) {
		$retVal = null;

		if ( file_exists($preparedQueryStringPath) && is_file($preparedQueryStringPath) && is_readable($preparedQueryStringPath) ) {
			$retVal = file_get_contents($preparedQueryStringPath);
		} else {
			throw new Exception('Requested query path does not exist: ' . $preparedQueryStringPath);
		}

		return $retVal;
	}

	protected function getRawDataReports() {
		return $this->_rawDataReports;
	}

}
