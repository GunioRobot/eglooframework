<?php
/**
 * DefaultTemplateEngine Class File
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
 * @copyright 2011 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package $package
 * @subpackage $subpackage
 * @version 1.0
 */

/**
 * DefaultTemplateEngine
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class DefaultTemplateEngine extends Smarty implements TemplateEngineInterface {

	protected $templateRoots = null;
	protected $packagePrefix = '';

	public function __construct( $interfacebundle, $local = 'US', $language = 'en' ) {
		parent::__construct( $interfacebundle, $local = 'US', $language = 'en' );

		$this->error_reporting = E_ALL | E_STRICT;
		$this->error_unassigned = true;
	}

	protected function setTemplatePaths( $templatePaths = null ) {
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

