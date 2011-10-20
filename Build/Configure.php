#! /usr/bin/env php
<?php
/**
 * eGloo Framework Configuration Build Script (OS X)
 *
 * This script is invoked with parameters to build or rebuild the eGloo configuration file based upon
 * the installation settings chosen.
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
 * @package Build
 * @subpackage Installation
 * @version 1.0
 */

include('../PHP/Classes/System/Configuration/Deprecated/eGlooConfiguration.php');

$value_pairs = array();
$configuration_options = array(
		'ApplicationsPath'		=> '',
		'CachePath' 			=> '',
		'ConfigurationPath'		=> '',
		'CubesPath'				=> '',
		'DataStorePath'			=> '',
		'DoctrinePath'			=> '',
		'DocumentationPath'		=> '',
		'DocumentRoot'			=> '',
		'FrameworkRootPath'		=> '',
		'LoggingPath'			=> '',
		'SmartyPath'			=> '',
		'UseDoctrine'			=> true,
		'UseSmarty'				=> true,
		'egBuild'				=> '',
		'egMaintenanceVersion'	=> '0',
		'egMajorVersion'		=> '1',
		'egMinorVersion'		=> '0',
		'egUseDoctrine'			=> 'false',
		'egUseDoctrine2DBAL'	=> 'false',
		'egUseDoctrine2ORM'		=> 'false',
		'egUseS3'				=> 'false',
		'egUseSmarty'			=> 'true',
		'egUseSmarty2'			=> 'false',
		'egUseSmarty3'			=> 'false',
		'egUseSwift4'			=> 'false',
		'egUseTwig'				=> 'false',
		);

$xml_config_options = array(
	'System' => array(
		'Components' => array(
			'ApplicationsPath' => array(
				'value' => ''
				),
			'CachePath' => array(
				'value' => ''
				),
			'ConfigurationPath' => array(
				'value' => ''
				),
			'CubesPath' => array(
				'value' => ''
				),
			'DataStorePath' => array(
				'value' => ''
				),
			'DoctrinePath' => array(
				'value' => ''
				),
			'DocumentationPath' => array(
				'value' => ''
				),
			'DocumentRoot' => array(
				'value' => ''
				),
			'FrameworkRootPath' => array(
				'value' => ''
				),
			'LoggingPath' => array(
				'value' => ''
				),
			'SmartyPath' => array(
				'value' => ''
				),
			),
		'Options' => array(
			'egBuild' => array(
				'value' => ''
				),
			'egMaintenanceVersion' => array(
				'value' => ''
				),
			'egMajorVersion' => array(
				'value' => 'true'
				),
			'egMinorVersion' => array(
				'value' => 'true'
				),
			'egUseDoctrine' => array(
				'value' => 'true'
				),
			'egUseSmarty' => array(
				'value' => 'true'
				)
			)
		),
	);

// Build a value pairs array out of provided arguments
foreach($argv as $argument) {
	$matches = array();
	preg_match('/--([a-zA-Z]+?)=([a-zA-Z0-9\/_.: -]+)/', $argument, $matches);

	if (!empty($matches) && isset($matches[1]) && isset($matches[2])) {
		$value = str_replace('/cygdrive/c/', '/', $matches[2]);
		$value_pairs[$matches[1]] = $value;
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

// Loop through all system xml components we expect
foreach($xml_config_options['System']['Components'] as $component_name => $component) {
	if (isset($value_pairs[$component_name])) {
		// Change strings for booleans to actual boolean types

		// Set the given option value in our configuration array
		$xml_config_options['System']['Components'][$component_name]['value'] = $value_pairs[$component_name];
	}
}

// Loop through all system xml options we expect
foreach($xml_config_options['System']['Options'] as $option_name => $option) {
	if (isset($value_pairs[$option_name])) {
		// Change strings for booleans to actual boolean types

		// Set the given option value in our configuration array
		$xml_config_options['System']['Options'][$option_name]['value'] = $value_pairs[$option_name];
	}
}

eGlooConfiguration::writeFrameworkSystemXML( $xml_config_options, true, '../XML/System.skeleton.xml', './System.xml');

// We're good, let's get out of here
exit;