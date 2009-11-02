<?php

final class eGlooConfiguration {
	
	// Configuration Attributes
	private static $configuration_options = array(
			'ApplicationsPath'		=> '',
			'CachePath' 			=> '',
			'CompiledTemplatesPath'	=> '',
			'ConfigurationPath'		=> '',
			'CubesPath'				=> '',
			'DocumentationPath'		=> '',
			'DocumentRoot'			=> '',
			'FrameworkRootPath'		=> '',
			'LoggingPath'			=> '',
			'SmartyPath'			=> ''
			);

	public static function loadConfigurationOptions($config_cache_path = '../Build/ConfigCache.php') {
		self::$configuration_options = eval('return ' . file_get_contents($config_cache_path) .';');
	}

    public static function getApplicationsPath() {
		return self::$configuration_options['ApplicationsPath'];
	}

    public static function getCachePath() {
		return self::$configuration_options['CachePath'];
	}

    public static function getCompiledTemplatesPath() {
		return self::$configuration_options['CompiledTemplatesPath'];
	}

    public static function getConfigurationPath() {
		return self::$configuration_options['ConfigurationPath'];
	}

    public static function getCubesPath() {
		return self::$configuration_options['CubesPath'];
	}

    public static function getDocumentationPath() {
		return self::$configuration_options['DocumentationPath'];
	}

    public static function getDocumentRoot() {
		return self::$configuration_options['DocumentRoot'];
	}

    public static function getFrameworkRootPath() {
		return self::$configuration_options['FrameworkRootPath'];
	}

    public static function getLoggingPath() {
		return self::$configuration_options['LoggingPath'];
	}

	public static function getSmartyIncludePath() {
		return self::$configuration_options['SmartyPath'];
	}

}