<?php
/**
 * CubeTemplateEngine Class File
 *
 * Contains the class definition for the CubeTemplateEngine, a subclass of 
 * the TemplateEngine class.
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
 * @package TemplateProcessing
 * @subpackage TemplateEngines
 * @version 1.0
 */

/**
 * CubeTemplateEngine
 * 
 * Provides a class definition for a Cube template engine subclass of
 * the TemplateEngine class.
 *
 * @package TemplateProcessing
 * @subpackage TemplateEngines
 */
class CubeTemplateEngine extends TemplateEngine {

    public function __construct( $interfacebundle, $local = 'US', $language = 'en' ) {
		parent::__construct( $interfacebundle, $local = 'US', $language = 'en' );

        $this->left_delimiter = '<!--{'; 
        $this->right_delimiter = '}-->'; 

        $this->template_dir = 'Cubes/';
        $this->config_dir   = 'Configuration/Smarty';

		$this->compile_dir	= eGlooConfiguration::getCachePath() . '/CompiledTemplates/' . $local . '/' . $language;
		$this->cache_dir	= eGlooConfiguration::getCachePath() . '/SmartyCache/' . $local . '/' . $language;

		// Because neither Windows nor Smarty is as dumb as both
		$this->compile_dir = str_replace('/', DIRECTORY_SEPARATOR, $this->compile_dir);
		$this->cache_dir = str_replace('/', DIRECTORY_SEPARATOR, $this->cache_dir);

		$this->compile_check = false;
		$this->force_compile = false;

		if (eGlooConfiguration::getDeploymentType() == eGlooConfiguration::PRODUCTION) {
			$this->compile_check = false;
			$this->force_compile = false;
			$this->caching = true;
		} else if (eGlooConfiguration::getDeploymentType() == eGlooConfiguration::STAGING) {
			$this->compile_check = true;
			$this->force_compile = false;
			$this->caching = true;
		} else if (eGlooConfiguration::getDeploymentType() == eGlooConfiguration::DEVELOPMENT) {
			$this->compile_check = true;
			$this->force_compile = true;
			$this->caching = false;
		} else {
			throw new CubeTemplateEngineException('Unknown Deployment Type Specified');
		}

    }

}