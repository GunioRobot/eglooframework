<?php
/**
 * TwigTemplateEngine Class File
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
 * TwigTemplateEngine
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class TwigTemplateEngine implements TemplateEngineInterface {

	protected $templateRoots = null;
	protected $packagePrefix = '';
	protected $_custom_left_delimiter = '{';
	protected $_custom_right_delimiter = '}';
	protected $_interface_bundle = 'Default';
	protected $_locale = 'US';
	protected $_language = 'en';

	public function __construct( $interface_bundle, $locale = 'US', $language = 'en' ) {
		$this->_interface_bundle = $interface_bundle;
		$this->_locale = $locale;
		$this->_language = $language;

		$this->init();
	}

	protected function init() {
		$this->setErrorReporting();
		$this->setCustomDelimiters();
		$this->setEngineDirectories();
		$this->setTemplatePaths();
		$this->setDeploymentOptions();
		$this->setCacheHandler();
	}

	public function setCacheHandler() {
		// Oh, hi!
	}

	public function setCustomDelimiters() {
		// $this->left_delimiter = $this->_custom_left_delimiter; 
		// $this->right_delimiter = $this->_custom_right_delimiter; 
	}

	public function setDeploymentOptions() {
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
			$this->caching = false;
		} else {
			throw new TemplateEngineException('Unknown Deployment Type Specified');
		}
	}

	public function setEngineDirectories() {
		$this->plugins_dir = $this->plugins_dir + array( 'PHP/Classes/components' );

		// Set the configuration directory
		$this->config_dir	= eGlooConfiguration::getConfigurationPath() . '/Smarty';

		// Set compilation and cache directories
		$this->compile_dir	= eGlooConfiguration::getCachePath() . '/' . eGlooConfiguration::getApplicationPath() . '/' .
			eGlooConfiguration::getUIBundleName() . '/CompiledTemplates/' . $this->_locale. '/' . $this->_language;

		$this->cache_dir	= eGlooConfiguration::getCachePath() . '/' . eGlooConfiguration::getApplicationPath() . '/' .
			eGlooConfiguration::getUIBundleName() . '/SmartyCache/' . $this->_locale. '/' . $this->_language;

		// Because neither Windows nor Smarty is as dumb as both
		$this->compile_dir = str_replace('/', DIRECTORY_SEPARATOR, $this->compile_dir);
		$this->cache_dir = str_replace('/', DIRECTORY_SEPARATOR, $this->cache_dir);
	}

	public function setErrorReporting() {
		$this->error_reporting = E_ALL | E_STRICT;
		$this->error_unassigned = true;
	}

	public function setTemplatePaths( $templatePaths = null ) {
		if ( !$templatePaths ) {
	        // Get the template paths for the application and the framework
			$application_template_path = eGlooConfiguration::getApplicationsPath() . '/' . 
				eGlooConfiguration::getApplicationPath() . '/InterfaceBundles/' . eGlooConfiguration::getUIBundleName() . '/' . $this->packagePrefix . '/';

			$application_common_template_path = eGlooConfiguration::getApplicationsPath() . '/' . 
				eGlooConfiguration::getApplicationPath() . '/Templates/' . $this->packagePrefix . '/';

			$extra_template_path = eGlooConfiguration::getApplicationsPath() . '/' . eGlooConfiguration::getApplicationPath() . '/' . eGlooConfiguration::getExtraTemplatePath() . '/';

			$framework_local_template_path = eGlooConfiguration::getFrameworkRootPath() . '/Templates/Frameworks/Local/' . $this->packagePrefix . '/';

			$framework_common_template_path = eGlooConfiguration::getFrameworkRootPath() . '/Templates/Frameworks/Common/' . $this->packagePrefix . '/';

			$framework_core_template_path = eGlooConfiguration::getFrameworkRootPath() . '/Templates/Frameworks/Core/' . $this->packagePrefix . '/';

			// TODO this should have a package prefix used.  Existing apps will need updated dispatches
			$this->templateRoots = array(
				'Application' => $application_template_path,
				'ApplicationCommon' => $application_common_template_path,
				'ExtraTemplatePath' => $extra_template_path,
				'FrameworkLocal' => $framework_local_template_path,
				'FrameworkCommon' => $framework_common_template_path,
				'FrameworkCore' => $framework_core_template_path
			);

			// We look in all template directories
			// This does NOT guarantee priority (undefined which will be grabbed if name collision exists)
			$this->template_dir = $this->templateRoots;
		} else {
			$this->template_dir = $templatePaths;
		}
	}

	public function useApplicationTemplates( $useApplicationTemplates = true, $interfaceBundle = null ) {
		if (!$useApplicationTemplates) {
			unset($this->templateRoots['Application']);
			$this->config_dir = $this->templateRoots;
		} else {
			$application_template_path = eGlooConfiguration::getApplicationsPath() . '/' . 
				eGlooConfiguration::getApplicationPath() . '/InterfaceBundles/' . $interfaceBundle . '/' . $this->packagePrefix . '/';
			$this->templateRoots['Application'] = $application_template_path;
			$this->template_dir = $this->templateRoots;
		}
	}

	public function useApplicationCommonTemplates( $useApplicationCommonTemplates ) {
		if (!$useApplicationCommonTemplates) {
			unset($this->templateRoots['ApplicationCommon']);
			$this->config_dir = $this->templateRoots;
		} else {
			$application_common_template_path = eGlooConfiguration::getApplicationsPath() . '/' . 
				eGlooConfiguration::getApplicationPath() . '/Templates/';
			$this->templateRoots['ApplicationCommon'] = $application_common_template_path;
			$this->template_dir = $this->templateRoots;
		}
	}

	public function useFrameworkTemplates( $useFrameworkTemplates = true, $scope = null, $package = null ) {
		if (!$useFrameworkTemplates) {
			unset($this->templateRoots['Framework']);
			$this->template_dir = $this->templateRoots;
		} else {
			$framework_template_path = 'Templates/';

			if ($scope) {
				$framework_template_path .= $scope . '/';
			}

			if ($package) {
				$framework_template_path .= $package . '/';
			}

			$this->templateRoots['Framework'] = $framework_template_path;
			$this->template_dir = $this->templateRoots;
		}
	}

	public function getTemplatePaths() {
		return $this->template_dir;
	}

}

