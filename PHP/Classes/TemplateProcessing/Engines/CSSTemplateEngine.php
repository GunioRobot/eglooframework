<?php
/**
 * CSSTemplateEngine Class File
 *
 * Contains the class definition for the CSSTemplateEngine, a subclass of 
 * the TemplateEngine class.
 *
 * Copyright 2008 eGloo, LLC
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
 * @copyright 2008 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package Template
 * @version 1.0
 */

/**
 * CSSTemplateEngine
 * 
 * Provides a class definition for a CSS template engine subclass of
 * the TemplateEngine class.
 *
 * @package Template
 */
class CSSTemplateEngine extends TemplateEngine {

	protected $packagePrefix = 'CSS';

    public function __construct( $interfacebundle, $local = 'US', $language = 'en' ) {
		parent::__construct( $interfacebundle, $local = 'US', $language = 'en' );
		$this->left_delimiter = '/*<!--{';
		$this->right_delimiter = '}-->*/';

        // Get the template paths for the application and the framework
		$application_template_path = eGlooConfiguration::getApplicationsPath() . '/' . 
			eGlooConfiguration::getApplicationPath() . '/InterfaceBundles/' . eGlooConfiguration::getUIBundleName() . '/' . $this->packagePrefix . '/';

		$framework_template_path = 'Templates/Applications/eGloo/InterfaceBundles/' . eGlooConfiguration::getUIBundleName() . '/CSS/';

		// We look in all template directories
		// This does NOT guarantee priority (undefined which will be grabbed if name collision exists)
        $this->template_dir = array($application_template_path, $framework_template_path);

		// Set the configuration directory
        $this->config_dir   = eGlooConfiguration::getConfigurationPath() . '/Smarty';

		$this->compile_dir	= eGlooConfiguration::getCachePath() . '/' . eGlooConfiguration::getApplicationPath() . '/' .
			eGlooConfiguration::getUIBundleName() . '/CompiledTemplates/' . $local . '/' . $language;

		$this->cache_dir	= eGlooConfiguration::getCachePath() . '/' . eGlooConfiguration::getApplicationPath() . '/' .
			eGlooConfiguration::getUIBundleName() . '/SmartyCache/' . $local . '/' . $language;

		// Because neither Windows nor Smarty is as dumb as both
		$this->compile_dir = str_replace('/', DIRECTORY_SEPARATOR, $this->compile_dir);
		$this->cache_dir = str_replace('/', DIRECTORY_SEPARATOR, $this->cache_dir);

		// $this->cache_handler_func = 'smarty_cache_memcache';

		if (eGlooConfiguration::getDeploymentType() == eGlooConfiguration::PRODUCTION) {
			$this->compile_check = false;
			$this->force_compile = false;
			// $this->caching = true;
			// $this->caching = 2;
			$this->caching = false;
		} else if (eGlooConfiguration::getDeploymentType() == eGlooConfiguration::STAGING) {
			$this->compile_check = true;
			$this->force_compile = false;
			// $this->caching = true;
			// $this->caching = 2;
			$this->caching = false;
		} else if (eGlooConfiguration::getDeploymentType() == eGlooConfiguration::DEVELOPMENT) {
			$this->compile_check = true;
			$this->force_compile = true;
			// $this->caching = 2;
			$this->caching = false;
		} else {
			throw new CSSTemplateEngineException('Unknown Deployment Type Specified');
		}

    }

}
