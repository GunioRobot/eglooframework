<?php
/**
 * BaseReportingRequestProcessor Class File
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
	protected $_connectionName = 'egPrimary';
	protected $_engineMode = null;
	protected $_populatedQuery = null;
	protected $_preparedQuery = null;
	protected $_queryParameters = array();
	protected $_queryResultResource = null;
	protected $_rawDataReport = null;
	protected $_reportingConnection = null;
	protected $_structuredDataReport = null;

	protected function populateTemplateVariables() {
		$this->prepareConnection();
		$this->prepareQuery();
		$this->prepareQueryParameters();
		$this->populateQuery();
		$this->executeQuery();
		$this->prepareRawDataReport();
		$this->structureRawDataReport();
		$this->setTemplateVariablesByMerge($this->getStructuredDataReport());
	}

	// This is "for now".  It shouldn't be tied to MySQL.  Overload this if you need to
	public function executeQuery() {
		$connection = $this->getConnection();
		$this->_queryResultResource = mysql_query($this->getPopulatedQuery(), $connection);

		if ( !isset($this->_queryResultResource) || !$this->_queryResultResource ) {
			$error_message = mysql_error($connection);

			if ( $error_message !== '' ) {
				throw new Exception( 'Query failed with message: ' . $error_message);
			} else {
				throw new Exception( 'Query resource unset or was returned null.  No error provided');
			}
		}
	}

	protected function getConnection() {
		return $this->_reportingConnection;
	}

	protected function prepareConnection() {
		$this->_reportingConnection = DBConnectionManager::getConnection($this->_connectionName, $this->_engineMode);
	}

	// TODO uh... fix this with some query parameters
	protected function populateQuery() {
		QueryPopulationManager::populateQueryTransaction($this->_preparedQuery, $this->_queryParameters);
		// $this->_populatedQuery = $this->_preparedQuery;
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

	// Should set $this->_preparedQuery to something useful
	abstract protected function prepareQuery();

	// Should set $this->_queryParameters to something useful
	abstract protected function prepareQueryParameters();

	// Should set $this->_queryParameters to something useful
	abstract protected function prepareRawDataReport();

	// Should structure the raw data result $this->_structuredDataReport into something useful for templates
	abstract function structureRawDataReport();

}

