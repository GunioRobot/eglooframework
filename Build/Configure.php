#! /usr/bin/env php
<?php
/**
 * eGloo Framework Configuration Build Script (OS X)
 *
 * This script is invoked with parameters to build or rebuild the eGloo configuration file based upon
 * the installation settings chosen.
 *
 * Copyright 2009 eGloo, LLC
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
 * @package Build
 * @subpackage Installation
 * @version 1.0
 */

$value_pairs = array();
$configuration_options = array(
		'ApplicationsPath'		=> '',
		'CachePath' 			=> '',
		'CompiledTemplatesPath'	=> '',
		'ConfigurationPath'		=> '',
		'CubesPath'				=> '',
		'DoctrinePath'			=> '',
		'DocumentationPath'		=> '',
		'DocumentRoot'			=> '',
		'FrameworkRootPath'		=> '',
		'LoggingPath'			=> '',
		'SmartyPath'			=> '',
		'UseDoctrine'			=> true,
		'UseSmarty'				=> true,
		);

// Build a value pairs array out of provided arguments
foreach($argv as $argument) {
	$matches = array();
	preg_match('/--([a-zA-Z]+?)=([a-zA-Z0-9\/_. ]+)/', $argument, $matches);

	if (!empty($matches) && isset($matches[1]) && isset($matches[2])) {
		$value_pairs[$matches[1]] = $matches[2];
	}
}

// Loop through all configuration options we expect
foreach($configuration_options as $option_name => $option_value) {
	if (isset($value_pairs[$option_name])) {
		// Change strings for booleans to actual boolean types
		if ($value_pairs[$option_name] === 'true') {
			$value_pairs[$option_name] = true;
		} else if ($value_pairs[$option_name] === 'false') {
			$value_pairs[$option_name] = false;
		}

		// Set the given option value in our configuration array
		$configuration_options[$option_name] = $value_pairs[$option_name];
	}
}

// Check if we're going to be writing out localization cache paths
if (isset($value_pairs['WriteLocalizationPaths']) && $value_pairs['WriteLocalizationPaths'] === 'true') {
	echo "Writing localization paths in cache path...\n";
	$countries = eval('return ' . file_get_contents('./Countries.php') .';');
	$languages = eval('return ' . file_get_contents('./Languages.php') .';');

	// Build paths for each locale
	foreach($countries as $country) {
		if (!file_exists($configuration_options['CachePath'] . '/CompiledTemplates/' . $country['A2'])) {
			mkdir($configuration_options['CachePath'] . '/CompiledTemplates/' . $country['A2'], 0755, true);
		}

		if (!file_exists($configuration_options['CachePath'] . '/SmartyCache/' . $country['A2'])) {
			mkdir($configuration_options['CachePath'] . '/SmartyCache/' . $country['A2'], 0755, true);
		}

		// Build paths for each language in each country
		foreach($languages as $language) {
			if (!file_exists($configuration_options['CachePath'] . '/CompiledTemplates/' . $country['A2'] . '/' . $language['code'])) {
				mkdir($configuration_options['CachePath'] . '/CompiledTemplates/' . $country['A2'] . '/' . $language['code'], 0755, true);
			}
			if (!file_exists($configuration_options['CachePath'] . '/SmartyCache/' . $country['A2'] . '/' . $language['code'])) {
				mkdir($configuration_options['CachePath'] . '/SmartyCache/' . $country['A2'] . '/' . $language['code'], 0755, true);
			}
		}
	}
}

// Dump our fresh configuration set
$config_dump = var_export($configuration_options, TRUE);
file_put_contents('ConfigCache.php', $config_dump);

// We're good, let's get out of here
exit;