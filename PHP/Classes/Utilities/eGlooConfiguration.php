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

	public static function loadConfigurationOptions( $config_cache_path = '../Build/ConfigCache.php' ) {
		self::$configuration_options = eval( 'return ' . file_get_contents($config_cache_path) .';' );

		// Grab our environment variables to determine which application and deployment to run
		self::$configuration_options['ApplicationName'] = $_SERVER['EG_APP'];
		self::$configuration_options['UIBundleName'] = $_SERVER['EG_UI'];
		self::$configuration_options['Deployment'] = $_SERVER['EG_ENV'];

		switch( $_SERVER['EG_LOG_LEVEL'] ) {
			case 'LOG_OFF' : 
				self::$configuration_options['LoggingLevel'] = eGlooLogger::$LOG_OFF;
				break;
			case 'PRODUCTION' : 
				self::$configuration_options['LoggingLevel'] = eGlooLogger::$PRODUCTION;
				break;
			case 'STAGING' : 
				self::$configuration_options['LoggingLevel'] = eGlooLogger::$STAGING;
				break;
			case 'DEVELOPMENT' : 
				self::$configuration_options['LoggingLevel'] = eGlooLogger::$DEVELOPMENT;
				break;
			default : 
				self::$configuration_options['LoggingLevel'] = eGlooLogger::$DEVELOPMENT;
				break;
		}

		switch( $_SERVER['EG_LOG_FORMAT'] ) {
			case 'LOG' :
				self::$configuration_options['LogFormat'] = eGlooLogger::$LOG_LOG;
				break;
			case 'HTML' :
				self::$configuration_options['LogFormat'] = eGlooLogger::$LOG_HTML;
				break;
			case 'XML' :
				self::$configuration_options['LogFormat'] = eGlooLogger::$LOG_XML;
				break;
			default:
				self::$configuration_options['LogFormat'] = eGlooLogger::$LOG_LOG;
				break;
		}

		// No errors
		return true;
	}

	public static function getApplicationName() {
		return self::$configuration_options['ApplicationName'];
	}
	
	public static function getUIBundleName() {
		return self::$configuration_options['UIBundleName'];
	}
	
	public static function getDeployment() {
		return self::$configuration_options['Deployment'];
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

    public static function getLogFormat() {
		return self::$configuration_options['LogFormat'];
	}

    public static function getLoggingPath() {
		return self::$configuration_options['LoggingPath'];
	}
	
	public static function getLoggingLevel() {
		return self::$configuration_options['LoggingLevel'];
	}

	public static function getSmartyIncludePath() {
		return self::$configuration_options['SmartyPath'];
	}

}