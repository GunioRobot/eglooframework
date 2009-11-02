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
		// self::$configuration_options['SmartyPath'] = 'Smarty/Smarty.class.php';
		// echo "<pre>";
		// print_r(self::$configuration_options);
		// echo "</pre>";
		// die;
	}

    public static function getApplicationsPath() {
	}

    public static function getCachePath() {
	}

    public static function getConfigurationPath() {
	}

    public static function getCubesPath() {
	}

    public static function getDocumentationPath() {
	}
	
    public static function getDocumentRoot() {
	}

    public static function getFrameworkRootPath() {
	}

    public static function getLoggingPath() {
		return self::$configuration_options['LoggingPath'];
	}

	public static function getSmartyIncludePath() {
		return self::$configuration_options['SmartyPath'];
	}

}