<?php
/**
 * ClearCacheGatewayCacheCoreeGlooRequestProcessor Class File
 *
 * Contains the class definition for the ClearCacheGatewayCacheCoreeGlooRequestProcessor, a
 * subclass of the RequestProcessor abstract class.
 * 
 * Copyright 2011 eGloo, LLC
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
 * @package RequestProcessing
 * @version 1.0
 */

/**
 * Clear Cache Gateway Cache Core eGloo Request Processor
 * 
 * Handles client requests to retrieve the external main page (the domain root;
 * e.g. www.egloo.com).
 * 
 * @package RequestProcessing
 * @subpackage RequestProcessors
 */
class ClearCacheGatewayCacheCoreeGlooRequestProcessor extends TemplatePatternRequestProcessor {

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
		eGlooLogger::writeLog( eGlooLogger::DEBUG, "ClearCacheGatewayCacheCoreeGlooRequestProcessor: Entered processRequest()" );

		$cacheGateway = CacheGateway::getCacheGateway();
		$cacheGateway->flushAllCache();

		$systemInfoBean = SystemInfoBean::getInstance();
		$systemActions = $systemInfoBean->getValue('SystemActions');
		
		$this->setTemplateVariable('systemActions', $systemActions);

		eGlooLogger::writeLog( eGlooLogger::DEBUG, "ClearCacheGatewayCacheCoreeGlooRequestProcessor: Exiting processRequest()" );
	}

}


