<?php
/**
 * ConfigureApplicationCacheCoreeGlooRequestProcessor Class File
 *
 * Contains the class definition for the ConfigureApplicationCacheCoreeGlooRequestProcessor, a
 * subclass of the RequestProcessor abstract class.
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
 * @copyright 2009 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package RequestProcessing
 * @version 1.0
 */

/**
 * Configure Application Cache Core eGloo Request Processor
 * 
 * Handles client requests to retrieve the external main page (the domain root;
 * e.g. www.egloo.com).
 * 
 * @package RequestProcessing
 * @subpackage RequestProcessors
 */
class ConfigureApplicationCacheCoreeGlooRequestProcessor extends RequestProcessor {

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
    public function processRequest() {
        eGlooLogger::writeLog( eGlooLogger::DEBUG, "ConfigureApplicationCacheCoreeGlooRequestProcessor: Entered processRequest()" );

		$applications_info = $this->getApplications(eGlooConfiguration::getApplicationsPath());

		$templateVariables = array();

		if ($this->requestInfoBean->issetPOST('application_group_selected')) {
			// die_r("Junk");
			$templateVariables['application_group_selected'] = $this->requestInfoBean->getPOST('application_group_selected');
		} else {
			$application_groups = array();

			foreach($applications_info as $application_info) {
				$application_groups[$application_info['application_group']] = $application_info['application_group'];
			}

			$templateVariables['application_groups'] = $application_groups;
		}

		if ($this->requestInfoBean->issetPOST('applications_selected_serialized')) {
			$templateVariables['applications_selected'] = unserialize(urldecode($this->requestInfoBean->getPOST('applications_selected_serialized')));
			$templateVariables['applications_selected_serialized'] = $this->requestInfoBean->getPOST('applications_selected_serialized');
		} else if ($this->requestInfoBean->issetPOST('applications_selected')) {
			$templateVariables['applications_selected'] = $this->requestInfoBean->getPOST('applications_selected');
			$templateVariables['applications_selected_serialized'] = urlencode(serialize($this->requestInfoBean->getPOST('applications_selected')));
		} else {
			$templateVariables['applications'] = $applications_info;
		}

		if ($this->requestInfoBean->issetPOST('applications_selected_serialized') &&
			$this->requestInfoBean->issetPOST('applications_selected') &&
			$this->requestInfoBean->issetPOST('application_bundle_selected')) {

			$templateVariables['application_bundle_selected'] = $this->requestInfoBean->getPOST('application_bundle_selected');
		} else if ($this->requestInfoBean->issetPOST('applications_selected_serialized') ||
				   $this->requestInfoBean->issetPOST('applications_selected')) {

			$applications_selected = $templateVariables['applications_selected'];

			$bundles = array();

			foreach($applications_selected as $application_name) {
				foreach($applications_info[$application_name]['application_interface_bundles'] as $application_bundle) {
					$bundles[] = $application_bundle;
				}
			}

			$templateVariables['bundles'] = $bundles;
		}

		if ($this->requestInfoBean->issetPOST('submit')) {
			if ($this->requestInfoBean->issetPOST('countries_selected')) {
				$countries_selected = $this->requestInfoBean->getPOST('countries_selected');
			} else {
				$countries_selected = array();
			}

			if ($this->requestInfoBean->issetPOST('languages_selected')) {
				$languages_selected = $this->requestInfoBean->getPOST('languages_selected');
			} else {
				$languages_selected = array();
			}

			$cache_path_sets = array();

			foreach ($applications_info as $application_info) {
				$cache_path_sets[$application_info['application_name']] = $this->getCachePaths( $application_info['application_name'],
																	  $application_info['application_interface_bundles'],
																	  $countries_selected,
																	  $languages_selected,
																	  $application_info['application_group']);
			}

			// Build cache paths
			foreach($cache_path_sets as $cache_path_set) {
				foreach($cache_path_set as $cache_path) {
					if (!file_exists($cache_path)) {
						mkdir($cache_path, 0755, true);
					}
				}
			}
		} else {
			$countries_selected = array();
			$languages_selected = array();
			$localizations = array();

			foreach ($applications_info as $application_info) {
				$localizations[$application_info['application_name']] = $this->getLocalizations( $application_info['application_name'],
															 $application_info['application_interface_bundles'],
															 $application_info['application_group']);
			}


			foreach($localizations as $bundles) {
				foreach($bundles as $localization) {
					foreach($localization as $country => $languages) {
						if (!in_array($country, $countries_selected)) {
							$countries_selected[] = $country;
						}

						foreach($languages as $language) {
							if (!in_array($language, $languages_selected)) {
								$languages_selected[] = $language;
							}
						}
					}
				}
			}
			
		}
		// echo_r($countries_selected);
		// echo_r($languages_selected);
		// echo_r($localizations);
		// die;


		$templateDirector = TemplateDirectorFactory::getTemplateDirector( $this->requestInfoBean );
		$templateBuilder = new XHTMLBuilder();

		$templateDirector->setTemplateBuilder( $templateBuilder );

		$templateDirector->preProcessTemplate();

		// Sort these by name, case insensitive
		uksort($applications_info, 'strnatcasecmp');

		$countries = eval('return ' . file_get_contents('../PHP/Data/Countries.php') .';');
		$languages = eval('return ' . file_get_contents('../PHP/Data/Languages.php') .';');

		$templateVariables['app'] = eGlooConfiguration::getApplicationName();
		$templateVariables['bundle'] = eGlooConfiguration::getUIBundleName();
		$templateVariables['applications'] = $applications_info;

		$templateVariables['countries'] = $countries;
		$templateVariables['languages'] = $languages;
		
		$templateVariables['countries_selected'] = $countries_selected;
		$templateVariables['languages_selected'] = $languages_selected;

		$templateDirector->setTemplateVariables( $templateVariables );            
		// echo_r(file('.htaccess'));
		$output = $templateDirector->processTemplate();

		eGlooLogger::writeLog( eGlooLogger::DEBUG, "ConfigureApplicationCacheCoreeGlooRequestProcessor: Echoing Response" );

		// TODO move header declarations to a decorator
		header("Content-type: text/html; charset=UTF-8");

		// TODO buffer output
		echo $output;

        eGlooLogger::writeLog( eGlooLogger::DEBUG, "ConfigureApplicationCacheCoreeGlooRequestProcessor: Exiting processRequest()" );
    }

	private function getApplications( $applicationsPath ) {
		$paths = array();

		if ( file_exists( $applicationsPath ) && is_dir( $applicationsPath ) ) {
			$it = new RecursiveDirectoryIterator( $applicationsPath );

			foreach ($it as $i) {
				if ($i->isLink()) {
					if (strpos($i->getFilename(), '.gloo')) {
						$application_name = preg_replace('~\.gloo~', '', $i->getFilename());

						if (strpos($i->getPath(), eGlooConfiguration::getApplicationsPath()) === false) {
							$application_group = '-None-';
						} else {
							$application_group = preg_replace('~' . eGlooConfiguration::getApplicationsPath() . '/~', '', $i->getPath());

							if (trim($application_group) === '' || $application_group === $i->getPath()) {
								$application_group = '-None-';
							}
						}

						$interface_bundles = null;

						try {
							$interface_bundles = $this->getInterfaceBundles($i->getRealPath());
						} catch (Exception $e) {

						}

						$paths[$application_name] = array( 'application_name' => $application_name, 
														   'application_path' => $i->getRealPath(),
														   'application_group' => $application_group,
														   'application_interface_bundles' => $interface_bundles);
					} else {
						$paths = array_merge($this->getApplications($i->getRealPath()), $paths);
					}
				} else if ($i->isDir() && strpos($i->getFilename(), '.gloo')) {
					$application_name = preg_replace('~\.gloo~', '', $i->getFilename());

					if (strpos($i->getPath(), eGlooConfiguration::getApplicationsPath()) === false) {
						$application_group = '-None-';
					} else {
						$application_group = preg_replace('~' . eGlooConfiguration::getApplicationsPath() . '/~', '', $i->getPath());
						
						if (trim($application_group) === '' || $application_group === $i->getPath()) {
							$application_group = '-None-';
						}
					}

					$interface_bundles = null;

					try {
						$interface_bundles = $this->getInterfaceBundles($i->getRealPath());
					} catch (Exception $e) {
						
					}

					$paths[$application_name] = array( 'application_name' => $application_name,
													   'application_path' => $i->getRealPath(),
													   'application_group' => $application_group,
													   'application_interface_bundles' => $interface_bundles);

				} else if ($i->isDir()) {
					$paths += (array) $this->getApplications($i->getRealPath());
				}
			}
		}

		return $paths;
	}

	private function getCachePaths( $application_name, $bundles, $localizations, $languages, $application_group = '' ) {
		$retVal = array();

		foreach($bundles as $bundle) {
			foreach($localizations as $localization) {
				foreach($languages as $language) {
					$compiled_templates_path = eGlooConfiguration::getCachePath();
					$compiled_templates_path .= $application_group !== null && $application_group !== '-None-' && $application_group !== ''  ? '/' . $application_group : '';
					$compiled_templates_path .= '/' . $application_name;
					$compiled_templates_path .= '/' . $bundle;
					$compiled_templates_path .= '/' . 'CompiledTemplates';
					$compiled_templates_path .= '/' . $localization;
					$compiled_templates_path .=  '/' . $language;

					$retVal[] = $compiled_templates_path;

					$smarty_cache_path = eGlooConfiguration::getCachePath();
					$smarty_cache_path .= $application_group !== null && $application_group !== '-None-' && $application_group !== '' ? '/' . $application_group : '';
					$smarty_cache_path .= '/' . $application_name;
					$smarty_cache_path .= '/' . $bundle;
					$smarty_cache_path .= '/' . 'SmartyCache';
					$smarty_cache_path .= '/' . $localization;
					$smarty_cache_path .=  '/' . $language;

					$retVal[] = $smarty_cache_path;
				}
			}
		}

		return $retVal;
	}

	private function getCountries( $application_name, $bundles, $application_group = null ) {
		$retVal = array();

		foreach($bundles as $bundle) {
			$compiled_templates_path = eGlooConfiguration::getCachePath();
			$compiled_templates_path .= $application_group !== null && $application_group !== '' ? '/' . $application_group : '';
			$compiled_templates_path .= '/' . $application_name;
			$compiled_templates_path .= '/' . $bundle;
			$compiled_templates_path .= '/' . 'CompiledTemplates';
			// $compiled_templates_path .= '/' . $localization;
			// $compiled_templates_path .=  '/' . $language;

			$retVal[] = $compiled_templates_path;

			$smarty_cache_path = eGlooConfiguration::getCachePath();
			$smarty_cache_path .= $application_group !== null && $application_group !== '' ? '/' . $application_group : '';
			$smarty_cache_path .= '/' . $application_name;
			$smarty_cache_path .= '/' . $bundle;
			$smarty_cache_path .= '/' . 'SmartyCache';
			// $smarty_cache_path .= '/' . $localization;
			// $smarty_cache_path .=  '/' . $language;

			$retVal[] = $smarty_cache_path;
		}

		return $retVal;
	}

	private function getInterfaceBundles( $application_path ) {
		if (!file_exists($application_path . '/InterfaceBundles')) {
			throw new ErrorException('Application has no interface bundles');
		}

		$retVal = array();

		$it = new DirectoryIterator( $application_path . '/InterfaceBundles' );

		foreach ($it as $i) {
			if ( !in_array($i->getFilename(), array('.', '..', '.DS_Store')) ) {
				$retVal[] = $i->getFilename();
			}
		}

		return $retVal;
	}

	private function getLanguages( $application_name, $bundles, $application_group = null ) {
		$retVal = array();

		foreach($bundles as $bundle) {
			$compiled_templates_path = eGlooConfiguration::getCachePath();
			$compiled_templates_path .= $application_group !== null && $application_group !== '' ? '/' . $application_group : '';
			$compiled_templates_path .= '/' . $application_name;
			$compiled_templates_path .= '/' . $bundle;
			$compiled_templates_path .= '/' . 'CompiledTemplates';

			try {
				$it = new DirectoryIterator( $compiled_templates_path );

				foreach ($it as $i) {
					if ( !in_array($i->getFilename(), array('.', '..', '.DS_Store')) ) {
						$retVal[$i->getFilename()] = $i->getFilename();
					}
				}
			} catch (Exception $e) {
				continue;
			}


			$smarty_cache_path = eGlooConfiguration::getCachePath();
			$smarty_cache_path .= $application_group !== null && $application_group !== '' ? '/' . $application_group : '';
			$smarty_cache_path .= '/' . $application_name;
			$smarty_cache_path .= '/' . $bundle;
			$smarty_cache_path .= '/' . 'SmartyCache';

			try {
				$it = new DirectoryIterator( $smarty_cache_path );

				foreach ($it as $i) {
					if ( !in_array($i->getFilename(), array('.', '..', '.DS_Store')) ) {
						$retVal[$i->getFilename()] = $i->getFilename();
					}
				}
			} catch (Exception $e) {
				continue;
			}

		}

		return $retVal;
	}

	private function getLocalizations( $application_name, $bundles, $application_group = null ) {
		$retVal = array();

		foreach($bundles as $bundle) {
			$retVal[$bundle] = array();

			$compiled_templates_path = eGlooConfiguration::getCachePath();
			$compiled_templates_path .= $application_group !== null && $application_group !== '' ? '/' . $application_group : '';
			$compiled_templates_path .= '/' . $application_name;
			$compiled_templates_path .= '/' . $bundle;
			$compiled_templates_path .= '/' . 'CompiledTemplates';

			try {
				$it = new DirectoryIterator( $compiled_templates_path );

				foreach ($it as $i) {
					if ( !in_array($i->getFilename(), array('.', '..', '.DS_Store')) ) {
						// $retVal[$i->getFilename()] = $i->getFilename();
						$retVal[$bundle][$i->getFilename()] = array();
						
						$languages = new DirectoryIterator( $i->getRealPath() );

						foreach ($languages as $language) {
							if ( !in_array($language->getFilename(), array('.', '..', '.DS_Store')) ) {
								// $retVal[$i->getFilename()] = $i->getFilename();
								$retVal[$bundle][$i->getFilename()][] = $language->getFilename();
							}
						}
					}
				}
			} catch (Exception $e) {
				continue;
			}


			$smarty_cache_path = eGlooConfiguration::getCachePath();
			$smarty_cache_path .= $application_group !== null && $application_group !== '' ? '/' . $application_group : '';
			$smarty_cache_path .= '/' . $application_name;
			$smarty_cache_path .= '/' . $bundle;
			$smarty_cache_path .= '/' . 'SmartyCache';

			try {
				// $it = new DirectoryIterator( $smarty_cache_path );
				// 
				// foreach ($it as $i) {
				// 	if ( !in_array($i->getFilename(), array('.', '..', '.DS_Store')) ) {
				// 		$retVal[$i->getFilename()] = $i->getFilename();
				// 	}
				// }
				$it = new DirectoryIterator( $smarty_cache_path );

				foreach ($it as $i) {
					if ( !in_array($i->getFilename(), array('.', '..', '.DS_Store')) ) {
						// $retVal[$i->getFilename()] = $i->getFilename();
						$retVal[$bundle][$i->getFilename()] = array();
						
						$languages = new DirectoryIterator( $i->getRealPath() );

						foreach ($languages as $language) {
							if ( !in_array($language->getFilename(), array('.', '..', '.DS_Store')) ) {
								// $retVal[$i->getFilename()] = $i->getFilename();
								$retVal[$bundle][$i->getFilename()][] = $language->getFilename();
							}
						}
					}
				}
			} catch (Exception $e) {
				continue;
			}

		}

		return $retVal;
	}

}


