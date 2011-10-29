<?php
/**
 * BaseReportingRequestProcessor Class File
 *
 * $file_block_description
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
 * @author George Cooper
 * @copyright 2011 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package $package
 * @subpackage $subpackage
 * @version 1.0
 */

/**
 * BaseReportingRequestProcessor
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
abstract class BaseReportingRequestProcessor extends TemplatePatternRequestProcessor {

	/* Protected Data Members */
	protected $_cache = true;
	protected $_cache_ttl = 30;
	protected $_connectionName = 'egPrimary';
	protected $_engineMode = null;
	protected $_executedQueryResponseTransaction = null;
	protected $_populatedQuery = null;
	protected $_preparedQuery = null;
	protected $_preparedQueryString = null;
	protected $_queryParameters = array();
	protected $_queryResultResource = null;
	protected $_rawDataReport = null;
	protected $_reportingConnection = null;
	protected $_structuredDataReport = null;

	protected function populateTemplateVariables() {
		$cacheGateway = CacheGateway::getCacheGateway();

		$this->prepareConnection();
		$this->setPreparedQueryString();
		$this->prepareQuery();
		$this->prepareQueryParameters();
		$this->populateQuery();

		$dataPackage = $this->getPopulatedQuery()->getDataPackage();

		if (is_array($dataPackage) && isset($dataPackage['preparedQueryString'])) {
			$dataPackageString = $dataPackage['preparedQueryString'];
		} else if (is_string($dataPackage)) {
			$dataPackageString = $dataPackage;
		} else {
			$dataPackageString = null;
		}

		$dataProcessingCacheRegionHandler = CacheManagementDirector::getCacheRegionHandler('DataProcessing');

		if ($this->_cache && $dataPackageString &&
			($cachedResponse = $dataProcessingCacheRegionHandler->getObject( md5($dataPackageString), 'Reporting' )) != null) {
			$this->setExecutedQueryResponseTransaction( $cachedResponse );
		} else {
			$this->executeQuery();

			if ($this->_cache && $dataPackageString) {
				$dataProcessingCacheRegionHandler->storeObject( md5($dataPackageString), $this->getExecutedQueryResponseTransaction(), 'Reporting', $this->_cache_ttl );
			}
		}


		$this->prepareRawDataReport();
		$this->structureRawDataReport();
		$this->setTemplateVariablesByMerge($this->getStructuredDataReport());
	}

	// This is "for now".  It shouldn't be tied to MySQL or a specific connection.  Overload this if you need to
	public function executeQuery( $queryToExecute = null, $connection = null ) {
		$retVal = null;
		$connection = isset($connection) ? $connection : $this->getConnection();

		if (isset($queryToExecute)) {
			$queryExecutionRoutine = QueryExecutionRoutineManager::getQueryExecutionRoutine($queryToExecute);
			$retVal = $queryExecutionRoutine->executeTransactionWithConnection($queryToExecute, $connection);
		} else {
			$queryExecutionRoutine = QueryExecutionRoutineManager::getQueryExecutionRoutine($this->getPopulatedQuery());
			$this->_executedQueryResponseTransaction =
				$queryExecutionRoutine->executeTransactionWithConnection($this->getPopulatedQuery(), $connection);

			$retVal = $this->_executedQueryResponseTransaction;
		}

		return $retVal;
	}

	protected function getConnection() {
		return $this->_reportingConnection;
	}

	// This should eventually be sourced out of a specific
	protected function prepareConnection() {
		$this->_reportingConnection = DBConnectionManager::getConnection($this->_connectionName, $this->_engineMode);
	}

	protected function prepareQuery( $preparedQueryString = null ) {
		$constructorArgument = isset($preparedQueryString) ? $preparedQueryString : $this->_preparedQueryString;

		$this->_preparedQuery = new QueryTransaction($constructorArgument);
		$this->_preparedQuery->setQueryDialect(DBConnectionManager::getConnection()->getConnectionDialect());
	}

	protected function prepareRawDataReport() {
		$this->_rawDataReport = $this->_executedQueryResponseTransaction->getDataPackage();
	}

	protected function populateQuery( $queryToPopulate = null, $queryParameters = null, $populateByValue = false ) {
		$retVal = null;

		$parameters = isset($queryParameters) ? $queryParameters : $this->_queryParameters;

		if ( $queryToPopulate ) {
			if ( $populateByValue ) {
				$clonedQuery = clone $queryToPopulate;
				QueryPopulationManager::populateQueryTransaction($clonedQuery, $parameters);
				$retVal = $clonedQuery;
			} else {
				QueryPopulationManager::populateQueryTransaction($queryToPopulate, $parameters);
				$retVal = $queryToPopulate;
			}
		} else {
			$this->_populatedQuery = clone $this->_preparedQuery;
			QueryPopulationManager::populateQueryTransaction($this->_populatedQuery, $parameters);
			$retVal = $this->_populatedQuery;
		}

		return $retVal;
	}

	protected function getPopulatedQuery() {
		return $this->_populatedQuery;
	}

	protected function setPopulatedQuery( $populatedQuery ) {
		$this->_populatedQuery = $populatedQuery;
	}

	protected function getPreparedQuery() {
		return $this->_preparedQuery;
	}

	protected function setPreparedQuery( $preparedQuery ) {
		$this->_preparedQuery = $preparedQuery;
	}

	protected function getPreparedQueryString() {
		return $this->_preparedQueryString;
	}

	protected function setPreparedQueryStringFromDispatch() {
		// TODO
	}

	protected function setPreparedQueryStringFromPath( $preparedQueryStringPath ) {
		if ( file_exists($preparedQueryStringPath) && is_file($preparedQueryStringPath) && is_readable($preparedQueryStringPath) ) {
			$this->_preparedQueryString = file_get_contents($preparedQueryStringPath);
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

	protected function getExecutedQueryResponseTransaction() {
		return $this->_executedQueryResponseTransaction;
	}

	protected function setExecutedQueryResponseTransaction( $executedQueryResponseTransaction ) {
		$this->_executedQueryResponseTransaction = $executedQueryResponseTransaction;
	}

	protected function getRawDataReport() {
		return $this->_rawDataReport;
	}

	protected function setRawDataReport( $rawDataReport ) {
		$this->_rawDataReport = $rawDataReport;
	}

	protected function getStructuredDataReport() {
		return $this->_structuredDataReport;
	}

	protected function setStructuredDataReport( $structuredDataReport ) {
		$this->_structuredDataReport = $structuredDataReport;
	}

	// Should set $this->_queryParameters to something useful
	abstract protected function prepareQueryParameters();

	// Should populate $this->_preparedQueryString with something useful
	abstract protected function setPreparedQueryString( $preparedQueryName = null );

	// Should structure the raw data result $this->_structuredDataReport into something useful for templates
	abstract function structureRawDataReport();

}

