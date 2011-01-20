<?php
/**
 * CacheManagementUIBaseCoreeGlooRequestProcessor Class File
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
 * CacheManagementUIBaseCoreeGlooRequestProcessor
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class CacheManagementUIBaseCoreeGlooRequestProcessor extends TemplatePatternRequestProcessor {

	/**
	 * Concrete implementation of the abstract RequestProcessor method
	 * processRequest().
	 * 
	 * This method handles processing of the incoming client request.  Its
	 * primary function is to establish the deployment environment (dev, test,
	 * production) and the current localization, and to then parse the correct
	 * template(s) in order to construct and output the appropriate external
	 * main page (the domain root; e.g. www.egloo.com).
	 * 
	 * @access public
	 */
	public function populateTemplateVariables() {
		eGlooLogger::writeLog( eGlooLogger::DEBUG, "CacheManagementUIBaseCoreeGlooRequestProcessor: Entered populateTemplateVariables()" );


		// $systemInfoBean = SystemInfoBean::getInstance();
		// $systemActions = $systemInfoBean->getValue('SystemActions');
		$cacheData = CacheManagementDirector::getAllCacheEntries();
		$cacheRegionLabels = CacheManagementDirector::getAllCacheRegionLabels();

		foreach( $cacheData as $cacheRegionKey => $cacheRegion ) {
			foreach( $cacheRegion as $cacheKey => $cacheEntry ) {
				$cacheData[$cacheRegionKey][$cacheKey]['key'] = substr(print_r($cacheEntry['key'], true), 0, 30) . '...';

				if ( is_array($cacheEntry['value']) || is_object($cacheEntry['value']) ) {
					$cacheData[$cacheRegionKey][$cacheKey]['value'] = substr(print_r($cacheEntry['value'], true), 0, 30) . '...';
				}
			}
		}

		if ( $this->requestInfoBean->issetGET('action') ) {
			$action = $this->requestInfoBean->getGET('action');
		} else {
			$action = null;
		}

		if ( $this->requestInfoBean->issetGET('region') ) {
			$region = $this->requestInfoBean->getGET('region');
		} else {
			$region = null;
		}

		if ( $this->requestInfoBean->issetGET('cacheKey') ) {
			$cacheKey = $this->requestInfoBean->getGET('cacheKey');
		} else {
			$cacheKey = null;
		}

		$highlighted_entry = array();

		if ( $action && $region && $cacheKey ) {
			$highlighted_entry['key'] = $cacheKey;
			$highlighted_entry['value'] = $cacheData[$region][$cacheKey]['value'];
			$highlighted_entry['ttl'] = $cacheData[$region][$cacheKey]['ttl'];
			$highlighted_entry['lastUpdated'] = $cacheData[$region][$cacheKey]['lastUpdated'];

			switch($action) {
				case 'view' :
					break;
				case 'edit' :
					break;
				case 'delete' :
					break;
				default :
					break;
			}
		}

		$this->setTemplateVariable('errors', array());
		$this->setTemplateVariable('highlighted_entry', $highlighted_entry);
		$this->setTemplateVariable('cacheData', $cacheData);
		$this->setTemplateVariable('cacheRegionLabels', $cacheRegionLabels);
		$this->setTemplateVariable('systemActions', array());
		$this->setTemplateVariable('requestURI', $this->requestInfoBean->getFullyQualifiedRequestString(array('action','region','cacheKey')));

		eGlooLogger::writeLog( eGlooLogger::DEBUG, "CacheManagementUIBaseCoreeGlooRequestProcessor: Exiting populateTemplateVariables()" );
	}

	public function populateErrorTemplateVariables() {
		eGlooLogger::writeLog( eGlooLogger::DEBUG, "CacheManagementUIBaseCoreeGlooRequestProcessor: Entered populateErrorTemplateVariables()" );
		
		$unsetArguments = $this->requestInfoBean->getUnsetGETArray();
		$errors = array();

		foreach( $unsetArguments as $argument ) {
			$errors[] = 'Required parameter \'' . $argument . '\' not provided';
		}

		// echo_r($this->requestInfoBean->getUnsetGETArray());
		// echo_r($this->requestInfoBean->getUnsetPOSTArray());
		// Test

		$this->setTemplateVariable('errors', $errors);
		$this->setTemplateVariable('highlighted_entry', array());
		$this->setTemplateVariable('cacheData', array());
		$this->setTemplateVariable('cacheRegionLabels', array());
		$this->setTemplateVariable('systemActions', array());
		$this->setTemplateVariable('requestURI', $this->requestInfoBean->getFullyQualifiedRequestString(array('action','region','cacheKey')));

		eGlooLogger::writeLog( eGlooLogger::DEBUG, "CacheManagementUIBaseCoreeGlooRequestProcessor: Exiting populateErrorTemplateVariables()" );
	}
}


