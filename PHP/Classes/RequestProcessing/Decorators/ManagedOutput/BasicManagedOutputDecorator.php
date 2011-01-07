<?php
/**
 * BasicManagedOutputDecorator Class File
 *
 * $file_block_description
 * 
 * Copyright 2010 eGloo, LLC
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *		  http://www.apache.org/licenses/LICENSE-2.0
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
 * BasicManagedOutputDecorator
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class BasicManagedOutputDecorator extends RequestProcessorDecorator {

	protected $_namespace = 'ManagedOutput';
	protected $_managerName = 'BasicManagedOutputDecorator';

  /**
   * do any pre processing here
   */
	protected function requestPreProcessing() {
		eGlooLogger::writeLog( eGlooLogger::DEBUG, "BasicManagedOutputDecorator: Entering requestPreProcessing()", 'Decorators' );

		if ( !$this->decoratorInfoBean->issetNamespace($this->_namespace) ) {
			$this->decoratorInfoBean->createNamespace($this->_namespace);

			if (!$this->decoratorInfoBean->issetValue('Manager', $this->_namespace)) {
				$this->decoratorInfoBean->setValue('Manager', $this->_managerName, $this->_namespace);
				$this->decoratorInfoBean->setValue('Format', $this->getOutputFormat(), $this->_namespace);
				$this->decoratorInfoBean->setValue('Active', true, $this->_namespace);
			}
		}

		eGlooLogger::writeLog( eGlooLogger::DEBUG, "BasicManagedOutputDecorator: Exiting requestPreProcessing()", 'Decorators' );

		return true;
   }

  /**
   * do any post processing here
   */
	protected function requestPostProcessing() {
		eGlooLogger::writeLog( eGlooLogger::DEBUG, "BasicManagedOutputDecorator: Entering requestPostProcessing()", 'Decorators' );

		if ( $this->decoratorInfoBean->issetValue('Manager', $this->_namespace) &&
			$this->decoratorInfoBean->getValue('Active', $this->_namespace) &&
			$this->decoratorInfoBean->getValue('Manager', $this->_namespace) === $this->_managerName &&
			$this->decoratorInfoBean->issetValue('Output', $this->_namespace) ) {

			$format = $this->decoratorInfoBean->getValue('Format', $this->_namespace);
			$output = $this->decoratorInfoBean->getValue('Output', $this->_namespace);

			if ($this->decoratorInfoBean->issetValue('Filename', $this->_namespace)) {
				$filename = $this->decoratorInfoBean->getValue('Filename', $this->_namespace);
			} else {
				$filename = $this->requestInfoBean->getRequestID() . '.' . $format;
			}

			switch( $format ) {
				case 'csv' :
					header('Content-type: application/octet-stream');
					header('Content-Disposition: attachment; filename="' . $filename . '"');
					break;
				case 'html' :
					header('Content-type: text/html; charset=UTF-8');
					break;
				case 'json' :
					header('Content-type: text/javascript');
					break;
				case 'svg' :
					break;
				case 'xhtml' :
					header('Content-type: text/html; charset=UTF-8');
					break;
				case 'xml' :
					header('Content-type: text/xml');
					break;
				default :
					break;
			}

			echo $output;
		}

		eGlooLogger::writeLog( eGlooLogger::DEBUG, "BasicManagedOutputDecorator: Exiting requestPostProcessing()", 'Decorators' );
	}

	protected function getOutputFormat() {
		$retVal = 'xhtml';

		if ($this->decoratorInfoBean->issetValue('Format', $this->_namespace)) {
			$retVal = $this->decoratorInfoBean->getValue('Format', $this->_namespace);;
		}

		return $retVal;
	}

}