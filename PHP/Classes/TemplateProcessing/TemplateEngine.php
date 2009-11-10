<?php
/**
 * TemplateEngine Class File
 *
 * Contains the class definition for the TemplateEngine, a subclass of 
 * the Smarty template engine class.
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

include( eGlooConfiguration::getSmartyIncludePath() );

/**
 * TemplateEngine
 * 
 * Provides a class definition for a generic template engine subclass of
 * the Smarty template class.
 *
 * @package Template
 */
class TemplateEngine extends Smarty {

	protected $templateRoots = null;
	protected $packagePrefix = '';

    public function __construct( $interfacebundle, $local = 'US', $language = 'en' ) {
		parent::__construct( $interfacebundle, $local = 'US', $language = 'en' );
        // $this->Smarty();
        $this->left_delimiter = '<!--{'; 
        $this->right_delimiter = '}-->'; 

        $this->plugins_dir = array( 'plugins', 'PHP/Classes/components' );

        // Get the template paths for the application and the framework
		$application_template_path = eGlooConfiguration::getApplicationsPath() . '/' . 
			eGlooConfiguration::getApplicationName() . '/InterfaceBundles/' . eGlooConfiguration::getUIBundleName();

		$framework_template_path = 'Templates';

		$this->templateRoots = array('Application' => $application_template_path, 'Framework' => $framework_template_path);

		// We look in all template directories
		// This does NOT guarantee priority (undefined which will be grabbed if name collision exists)
        $this->template_dir = $this->templateRoots;

		// Set the configuration directory
        $this->config_dir   = eGlooConfiguration::getConfigurationPath() . '/Smarty';

		$this->compile_dir	= eGlooConfiguration::getCachePath() . '/CompiledTemplates/' . $local . '/' . $language;
		$this->cache_dir	= eGlooConfiguration::getCachePath() . '/SmartyCache' . $local . '/' . $language;

        //$this->cache_handler_func = 'smarty_cache_memcache';
        $this->caching = false;
    }

	public function useApplicationTemplates( $useFrameworkTemplates = true, $interfaceBundle = null ) {
		if (!$useFrameworkTemplates) {
			unset($this->templateRoots['Application']);
			$this->config_dir = $this->templateRoots;
		} else {
			$application_template_path = eGlooConfiguration::getApplicationsPath() . '/' . 
				eGlooConfiguration::getApplicationName() . '/InterfaceBundles/' . $interfaceBundle . '/' . $this->packagePrefix . '/';
			$this->templateRoots['Application'] = $application_template_path;
			$this->template_dir = $this->templateRoots;
		}

	}

	public function useFrameworkTemplates( $useFrameworkTemplates = true, $scope = null, $package = null ) {
		if (!$useFrameworkTemplates) {
			unset($this->templateRoots['Framework']);
			$this->template_dir = $this->templateRoots;
		} else {
			$framework_template_path = 'Templates/' . $scope . '/' . $package . '/';
			$this->templateRoots['Framework'] = $framework_template_path;
			$this->template_dir = $this->templateRoots;
		}
	}

}

?>