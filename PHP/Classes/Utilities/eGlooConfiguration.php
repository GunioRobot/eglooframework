<?php

final class eGlooConfiguration {

	/* Class Constants */

	// Deployment Type
	const DEVELOPMENT	= 0x00;
	const STAGING		= 0x01;
	const PRODUCTION	= 0x02;

	// Database Engines
	const DOCTRINE		= 0x00;
	const MYSQL			= 0x01;
	const POSTGRESQL	= 0x02;

	/* Static Members */

	// Configuration Attributes
	private static $configuration_options = array();

	// Configuration Attribute Choices
	private static $configuration_possible_options = array(
			'ApplicationsPath'		=> '',
			'CachePath' 			=> '',
			'CompiledTemplatesPath'	=> '',
			'ConfigurationPath'		=> '',
			'CubesPath'				=> '',
			'Deployment'			=> '',
			'DisplayErrors'			=> false,
			'DisplayTraces'			=> false,
			'DoctrinePath'			=> '',
			'DocumentationPath'		=> '',
			'DocumentRoot'			=> '',
			'FrameworkRootPath'		=> '',
			'LoggingPath'			=> '',
			'SmartyPath'			=> '',
			'UseCache'				=> true,
			'UseFileCache'			=> false,
			'UseMemcache'			=> false,
			'UseAPCCache'			=> true,
			'UseDoctrine'			=> false,
			'UseSmarty'				=> true,
			'UsePostgreSQL'			=> true,
			);

	public static function loadConfigurationOptions( $config_cache_path = 'ConfigCache.php' ) {
		self::$configuration_options = eval( 'return ' . file_get_contents($config_cache_path) .';' );

		// Grab our environment variables to determine which application and deployment to run
		self::$configuration_options['ApplicationName']	= $_SERVER['EG_APP'];
		self::$configuration_options['UIBundleName']		= $_SERVER['EG_UI'];

		switch( $_SERVER['EG_CACHE'] ) {
			case 'ON' :
				self::$configuration_options['UseCache'] = true;
				break;
			case 'OFF' :
			default :
				self::$configuration_options['UseCache'] = false;
				break;
		}

		switch( $_SERVER['EG_CACHE_FILE'] ) {
			case 'ON' :
				self::$configuration_options['UseFileCache'] = true;
				break;
			case 'OFF' :
			default :
				self::$configuration_options['UseFileCache'] = false;
				break;
		}

		switch( $_SERVER['EG_CACHE_MEMCACHE'] ) {
			case 'ON' :
				self::$configuration_options['UseMemcache'] = true;
				break;
			case 'OFF' :
			default :
				self::$configuration_options['UseMemcache'] = false;
				break;
		}

		switch( $_SERVER['EG_ENV'] ) {
			case 'DEVELOPMENT' :
				self::$configuration_options['Deployment'] = self::DEVELOPMENT;
				break;
			case 'STAGING' :
				self::$configuration_options['Deployment'] = self::STAGING;
				break;
			case 'PRODUCTION' :
				self::$configuration_options['Deployment'] = self::PRODUCTION;
				break;
			default :
				self::$configuration_options['Deployment'] = self::DEVELOPMENT;
				break;
		}

		switch( $_SERVER['EG_DB_ENGINE'] ) {
			case 'DOCTRINE' :
				self::$configuration_options['DatabaseEngine'] = self::DOCTRINE;
				break;
			case 'MYSQL' :
				self::$configuration_options['DatabaseEngine'] = self::MYSQL;
				break;
			case 'POSTGRESQL' :
				self::$configuration_options['DatabaseEngine'] = self::POSTGRESQL;
				break;
			default:
				self::$configuration_options['DatabaseEngine'] = self::POSTGRESQL;
				break;
		}

		if ( isset($_SERVER['EG_DISPLAY_ERRORS']) ) {
			switch( $_SERVER['EG_DISPLAY_ERRORS'] ) {
				case 'ON' :
					self::$configuration_options['DisplayErrors'] = true;
					break;
				case 'OFF' :
					self::$configuration_options['DisplayErrors'] = false;
					break;
				default :
					break;
			}
		} else if ( self::$configuration_options['Deployment'] === self::DEVELOPMENT ) {
			self::$configuration_options['DisplayErrors'] = true;
		}

		if ( isset($_SERVER['EG_DISPLAY_TRACES']) ) {
			switch( $_SERVER['EG_DISPLAY_TRACES'] ) {
				case 'ON' :
					self::$configuration_options['DisplayTraces'] = true;
					break;
				case 'OFF' :
					self::$configuration_options['DisplayTraces'] = false;
					break;
				default :
					break;
			}
		} else if ( self::$configuration_options['Deployment'] === self::DEVELOPMENT ) {
			self::$configuration_options['DisplayTraces'] = true;
		}


		foreach (self::$configuration_possible_options as $possible_option_key => $option_default_value) {
			if (!isset(self::$configuration_options[$possible_option_key])) {
				self::$configuration_options[$possible_option_key] = $option_default_value;
			}
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

	public static function getDatabaseEngine() {
		return self::$configuration_options['DatabaseEngine'];
	}

    public static function getDeploymentType() {
		return self::$configuration_options['Deployment'];
	}

    public static function getDisplayErrors() {
		return self::$configuration_options['DisplayErrors'];
	}

    public static function getDisplayTraces() {
		return self::$configuration_options['DisplayTraces'];
	}

	public static function getDoctrineIncludePath() {
		return self::$configuration_options['DoctrinePath'];
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
		if ( !isset(self::$configuration_options['LogFormat']) ) {
			switch( $_SERVER['EG_LOG_FORMAT'] ) {
				case 'LOG' :
					self::$configuration_options['LogFormat'] = eGlooLogger::LOG_LOG;
					break;
				case 'HTML' :
					self::$configuration_options['LogFormat'] = eGlooLogger::LOG_HTML;
					break;
				case 'XML' :
					self::$configuration_options['LogFormat'] = eGlooLogger::LOG_XML;
					break;
				default:
					self::$configuration_options['LogFormat'] = eGlooLogger::LOG_LOG;
					break;
			}
		}

		return self::$configuration_options['LogFormat'];
	}

    public static function getLoggingPath() {
		return self::$configuration_options['LoggingPath'];
	}
	
	public static function getLoggingLevel() {
		if ( !isset(self::$configuration_options['LoggingLevel']) ) {
			switch( $_SERVER['EG_LOG_LEVEL'] ) {
				case 'LOG_OFF' : 
					self::$configuration_options['LoggingLevel'] = eGlooLogger::LOG_OFF;
					break;
				case 'PRODUCTION' : 
					self::$configuration_options['LoggingLevel'] = eGlooLogger::PRODUCTION;
					break;
				case 'STAGING' : 
					self::$configuration_options['LoggingLevel'] = eGlooLogger::STAGING;
					break;
				case 'DEVELOPMENT' : 
					self::$configuration_options['LoggingLevel'] = eGlooLogger::DEVELOPMENT;
					break;
				default : 
					self::$configuration_options['LoggingLevel'] = eGlooLogger::DEVELOPMENT;
					break;
			}
		}

		return self::$configuration_options['LoggingLevel'];
	}

	public static function getSmartyIncludePath() {
		return self::$configuration_options['SmartyPath'];
	}

	public static function getUseCache() {
		return self::$configuration_options['UseCache'];
	}

	public static function getUseFileCache() {
		return self::$configuration_options['UseFileCache'];
	}

	public static function getUseMemcache() {
		return self::$configuration_options['UseMemcache'];
	}

	public static function getUseDoctrine() {
		return self::$configuration_options['UseDoctrine'];
	}

	public static function getUseSmarty() {
		return self::$configuration_options['UseSmarty'];
	}

	// public static function getCacheStatus() {
	// 	return self::$configuration_options['Cache'];
	// }
	// 
	// public static function getFileCacheStatus() {
	// 	return self::$configuration_options['FileCache'];
	// }
	// 
	// public static function getMemcacheCacheStatus() {
	// 	return self::$configuration_options['MemcacheCache'];
	// }

}