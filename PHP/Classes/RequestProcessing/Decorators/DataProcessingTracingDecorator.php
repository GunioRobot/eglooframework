<?php
/**
 * DataProcessingTracingDecorator Class File
 *
 * Contains the class definition for the DataProcessingTracingDecorator
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
 * DataProcessingTracingDecorator
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class DataProcessingTracingDecorator extends RequestProcessorDecorator {

	protected $_namespace = 'ManagedOutput';
	protected $_managerName = 'DataProcessingTracingDecorator';

  /**
   * do any pre processing here
   */
	protected function requestPreProcessing() {
		eGlooLogger::writeLog( eGlooLogger::DEBUG, "DataProcessingTracingDecorator: Entering requestPreProcessing()", 'Decorators' );
		eGlooLogger::writeLog( eGlooLogger::DEBUG, "DataProcessingTracingDecorator: Exiting requestPreProcessing()", 'Decorators' );
		return true;
   }

  /**
   * do any post processing here
   */
	protected function requestPostProcessing() {
		eGlooLogger::writeLog( eGlooLogger::DEBUG, "DataProcessingTracingDecorator: Entering requestPostProcessing()", 'Decorators' );

		if ( $this->decoratorInfoBean->issetValue('Manager', $this->_namespace) &&
			$this->decoratorInfoBean->getValue('Active', $this->_namespace) &&
			$this->decoratorInfoBean->issetValue('Output', $this->_namespace) ) {

			$format = $this->decoratorInfoBean->getValue('Format', $this->_namespace);
			$output = $this->decoratorInfoBean->getValue('Output', $this->_namespace);

			if ($format === 'xhtml' || $format === 'html') {
				$output .= '<br /><br /><h1>*** Begin Data Processing Trace ***</h1><br /><br />';

				$output .= '<h2>** Begin History Of Queries Populated **</h2><br /><br />';

				$queryCount = 1;

				foreach( QueryPopulationRoutine::getHistoryOfQueriesPopulated() as $queryPopulated ) {
					$output .= '<h3>Begin Populated Query #' . $queryCount . '</h3><br /><br />';

					$output .= '<h4>Begin Unprepared Populated Query #' . $queryCount . '</h4><br />';
					$output .= '<pre>' . print_r( $queryPopulated['unpreparedQueryString'], true) . '</pre><br />';
					$output .= '<h4>End Unprepared Populated Query #' . $queryCount . '</h4><br />';

					$output .= '<h4>Begin Parameters for Populated Query #' . $queryCount . '</h4><br />';
					$output .= '<pre>' . print_r( $queryPopulated['parameters'], true) . '</pre><br />';
					$output .= '<h4>End Parameters for Populated Query #' . $queryCount . '</h4><br />';

					$output .= '<h4>Begin Prepared Populated Query #' . $queryCount . '</h4><br />';
					$output .= '<pre>' . print_r( $queryPopulated['preparedQueryString'], true) . '</pre><br />';
					$output .= '<h4>End Prepared Populated Query #' . $queryCount . '</h4><br /><br />';

					$output .= '<h3>End Populated Query #' . $queryCount . '</h3><br /><br />';

					$queryCount++;
				}

				$output .= '<h2>** End History Of Queries Populated **</h2><br /><br />';

				$output .= '<h2>** Begin History Of Queries Executed **</h2><br /><br />';

				$queryCount = 1;

				foreach( QueryExecutionRoutine::getHistoryOfQueriesExecuted() as $queryExecuted ) {
					$output .= '<h3>Begin Executed Query #' . $queryCount . '</h3><br /><br />';

					$output .= '<h4>Begin Executed Query #' . $queryCount . '</h4><br />';
					$output .= '<pre>' . print_r( $queryExecuted['executedQuery'], true) . '</pre><br />';
					$output .= '<h4>End Executed Query #' . $queryCount . '</h4><br />';

					// $output .= '<h4>Begin Result for Executed Query #' . $queryCount . '</h4><br />';
					// $output .= '<pre>' . print_r( $queryExecuted['executedQueryResult'], true) . '</pre><br />';
					// $output .= '<h4>End Result for Executed Query #' . $queryCount . '</h4><br />';

					$output .= '<h3>End Populated Query #' . $queryCount . '</h3><br /><br />';

					$queryCount++;
				}

				$output .= '<h2>** End History Of Queries Executed **</h2><br /><br />';


				// $output .= '<pre>Transactions that went in flight: ' . eGlooResponseTransaction::getNumberOfResponseTransactionsInFlight() . '</pre>';
				// $output .= '<pre>Response Transactions that went in flight: ' . eGlooTransaction::getNumberOfTransactionsInFlight() . '</pre>';
				// $output .= '<pre>Total number of queries executed: ' . QueryExecutionRoutine::getNumberOfQueriesExecuted() . '</pre>';
				// $output .= '<pre>Total number of queries populated: ' . QueryPopulationRoutine::getNumberOfQueriesPopulated() . '</pre>';
				// $output .= '<pre>Total number of transformations performed: ' . QueryResponseTransactionTransformRoutine::getNumberOfTransformations() . '</pre>';
				$output .= '<br /><br /><h1>*** End Data Processing Trace ***</h1><br /><br />';

				$output = $this->decoratorInfoBean->setValue('Output', $output, $this->_namespace);
			}
		} else {
			echo_r('Transactions that went in flight: ' . eGlooResponseTransaction::getNumberOfResponseTransactionsInFlight());
			echo_r('Response Transactions that went in flight: ' . eGlooTransaction::getNumberOfTransactionsInFlight());
			echo_r('Total number of queries executed: ' . QueryExecutionRoutine::getNumberOfQueriesExecuted());
			echo_r('Total number of queries populated: ' . QueryPopulationRoutine::getNumberOfQueriesPopulated());
			echo_r('Total number of transformations performed: ' . QueryResponseTransactionTransformRoutine::getNumberOfTransformations());
		}

		eGlooLogger::writeLog( eGlooLogger::DEBUG, "DataProcessingTracingDecorator: Exiting requestPostProcessing()", 'Decorators' );
	}

}

