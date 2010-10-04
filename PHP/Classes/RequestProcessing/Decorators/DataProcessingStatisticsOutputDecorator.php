<?php
/**
 * DataProcessingStatisticsOutputDecorator Class File
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
 * DataProcessingStatisticsOutputDecorator
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class DataProcessingStatisticsOutputDecorator extends DataProcessingStatisticsDecorator {

	protected $_namespace = 'ManagedOutput';
	protected $_managerName = 'DataProcessingStatisticsOutputDecorator';

  /**
   * do any pre processing here
   */
	protected function requestPreProcessing() {
		eGlooLogger::writeLog( eGlooLogger::DEBUG, "DataProcessingStatisticsOutputDecorator: Entering requestPreProcessing()", 'Decorators' );
		eGlooLogger::writeLog( eGlooLogger::DEBUG, "DataProcessingStatisticsOutputDecorator: Exiting requestPreProcessing()", 'Decorators' );
		return true;
   }

  /**
   * do any post processing here
   */
	protected function requestPostProcessing() {
		eGlooLogger::writeLog( eGlooLogger::DEBUG, "DataProcessingStatisticsOutputDecorator: Entering requestPostProcessing()", 'Decorators' );

		if ( $this->decoratorInfoBean->issetValue('Manager', $this->_namespace) &&
			$this->decoratorInfoBean->getValue('Active', $this->_namespace) &&
			$this->decoratorInfoBean->issetValue('Output', $this->_namespace) ) {

			$format = $this->decoratorInfoBean->getValue('Format', $this->_namespace);
			$output = $this->decoratorInfoBean->getValue('Output', $this->_namespace);

			if ($format === 'xhtml' || $format === 'html') {
				$output .= '<pre>Transactions that went in flight: ' . eGlooResponseTransaction::getNumberOfResponseTransactionsInFlight() . '</pre>';
				$output .= '<pre>Response Transactions that went in flight: ' . eGlooTransaction::getNumberOfTransactionsInFlight() . '</pre>';
				$output .= '<pre>Total number of queries executed: ' . QueryExecutionRoutine::getNumberOfQueriesExecuted() . '</pre>';
				$output .= '<pre>Total number of queries populated: ' . QueryPopulationRoutine::getNumberOfQueriesPopulated() . '</pre>';
				$output .= '<pre>Total number of transformations performed: ' . QueryResponseTransactionTransformRoutine::getNumberOfTransformations() . '</pre>';

				$output = $this->decoratorInfoBean->setValue('Output', $output, $this->_namespace);
			}
		} else {
			echo_r('Transactions that went in flight: ' . eGlooResponseTransaction::getNumberOfResponseTransactionsInFlight());
			echo_r('Response Transactions that went in flight: ' . eGlooTransaction::getNumberOfTransactionsInFlight());
			echo_r('Total number of queries executed: ' . QueryExecutionRoutine::getNumberOfQueriesExecuted());
			echo_r('Total number of queries populated: ' . QueryPopulationRoutine::getNumberOfQueriesPopulated());
			echo_r('Total number of transformations performed: ' . QueryResponseTransactionTransformRoutine::getNumberOfTransformations());
		}

		eGlooLogger::writeLog( eGlooLogger::DEBUG, "DataProcessingStatisticsOutputDecorator: Exiting requestPostProcessing()", 'Decorators' );
	}

}

