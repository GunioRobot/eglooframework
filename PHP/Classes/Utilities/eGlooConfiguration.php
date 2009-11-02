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
			'LoggingPath'			=> ''
			);

	public static function loadConfigurationOptions() {
		self::$configuration_options = eval('return ' . file_get_contents('ConfigCache.php') .';');
		echo "<pre>";
		print_r(self::$configuration_options);
		echo "</pre>";
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

}