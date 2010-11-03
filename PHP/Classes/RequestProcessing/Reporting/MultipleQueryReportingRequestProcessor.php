<?php
/**
 * MultipleQueryReportingRequestProcessor Class File
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
 * MultipleQueryReportingRequestProcessor
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
abstract class MultipleQueryReportingRequestProcessor extends BaseReportingRequestProcessor {
	// $queryTransformRoutine = QueryTransformationManager::getQueryResultTransformRoutine( $queryTransaction );

	/* Protected Data Members */
	protected $_executedQueries = array();
	protected $_populatedQueries = array();
	protected $_preparedQueries = array();
	protected $_preparedQueryStrings = array();
	protected $_queryExecutionSteps = array();
	protected $_queryParameters = array();
	protected $_queryResultResources = array();
	protected $_rawDataReports = array();

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

				$this->processSubQuery($preparedQueryName);
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
		$queryResultTransformRoutine = QueryTransformationManager::getQueryResponseTransactionTransformRoutine($queryResponseTransaction);

		// I'd like to support multiple column feeders in the future.  For now, this works.
		$output = $queryResultTransformRoutine->getSingleColumnFeederStringFormat($queryResponseTransaction);

		$feederColumnKey = $output['column_key'];

		$feederValue = implode(', ', $output[$feederColumnKey]);
		$this->_executedQueries[$subQuery] = array($output['column_key'] => $feederValue);
	}

	protected function prepareQuery( $preparedQueryString = null ) {
		$preparedQuery = new QueryTransaction($preparedQueryString);
		$preparedQuery->setQueryDialect($this->_reportingConnection->getConnectionDialect());
		return $preparedQuery;
	}

	public function prepareSubQueryParameters( $queryName, $parametersNeeded ) {
		$queryParameters = array();
		
		if (isset($parametersNeeded['get'])) {
			foreach( $parametersNeeded['get'] as $getParameter ) {
				$queryParameters[] = array('type' => 'integer', 'value' => $this->requestInfoBean->getGET($getParameter));
			}
		}
		
		if (isset($parametersNeeded['other'])) {
			foreach( $parametersNeeded['other'] as $subQuery => $parameters ) {
				foreach( $parameters as $parameter_name ) {
					$queryParameters[] = array('type' => 'string', 'value' => $this->_executedQueries[$subQuery][$parameter_name]);
				}
			}
		}

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

