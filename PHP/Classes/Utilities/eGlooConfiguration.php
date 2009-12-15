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

	public static function loadWebRootConfig( $overwrite = true ) {
		$webRootConfigOptions = array();
		$webRootConfigOptions['ApplicationPath']		= $_SERVER['EG_APP'];
		$webRootConfigOptions['ApplicationName']		= preg_replace('~([a-zA-Z0-9/ ])+?/([a-zA-Z0-9 ]*?)\.gloo~', '$2', $_SERVER['EG_APP']);
		$webRootConfigOptions['UIBundleName']		= $_SERVER['EG_UI'];

		switch( $_SERVER['EG_CACHE'] ) {
			case 'ON' :
				$webRootConfigOptions['UseCache'] = true;
				break;
			case 'OFF' :
			default :
				$webRootConfigOptions['UseCache'] = false;
				break;
		}

		switch( $_SERVER['EG_CACHE_FILE'] ) {
			case 'ON' :
				$webRootConfigOptions['UseFileCache'] = true;
				break;
			case 'OFF' :
			default :
				$webRootConfigOptions['UseFileCache'] = false;
				break;
		}

		switch( $_SERVER['EG_CACHE_MEMCACHE'] ) {
			case 'ON' :
				$webRootConfigOptions['UseMemcache'] = true;
				break;
			case 'OFF' :
			default :
				$webRootConfigOptions['UseMemcache'] = false;
				break;
		}

		// Determine our deployment type
		switch( $_SERVER['EG_ENV'] ) {
			case 'DEVELOPMENT' :
				$webRootConfigOptions['Deployment'] = self::DEVELOPMENT;
				break;
			case 'STAGING' :
				$webRootConfigOptions['Deployment'] = self::STAGING;
				break;
			case 'PRODUCTION' :
				$webRootConfigOptions['Deployment'] = self::PRODUCTION;
				break;
			default :
				$webRootConfigOptions['Deployment'] = self::DEVELOPMENT;
				break;
		}

		// Determine which DB system we're using
		switch( $_SERVER['EG_DB_ENGINE'] ) {
			case 'DOCTRINE' :
				$webRootConfigOptions['DatabaseEngine'] = self::DOCTRINE;
				break;
			case 'MYSQL' :
				$webRootConfigOptions['DatabaseEngine'] = self::MYSQL;
				break;
			case 'POSTGRESQL' :
				$webRootConfigOptions['DatabaseEngine'] = self::POSTGRESQL;
				break;
			default:
				$webRootConfigOptions['DatabaseEngine'] = self::POSTGRESQL;
				break;
		}

		// Check if we're displaying errors in the UI or not
		if ( isset($_SERVER['EG_DISPLAY_ERRORS']) ) {
			switch( $_SERVER['EG_DISPLAY_ERRORS'] ) {
				case 'ON' :
					$webRootConfigOptions['DisplayErrors'] = true;
					break;
				case 'OFF' :
					$webRootConfigOptions['DisplayErrors'] = false;
					break;
				default :
					break;
			}
		} else if ( $webRootConfigOptions['Deployment'] === self::DEVELOPMENT ) {
			$webRootConfigOptions['DisplayErrors'] = true;
		}

		// Check if we're displaying traces in the UI or not
		if ( isset($_SERVER['EG_DISPLAY_TRACES']) ) {
			switch( $_SERVER['EG_DISPLAY_TRACES'] ) {
				case 'ON' :
					$webRootConfigOptions['DisplayTraces'] = true;
					break;
				case 'OFF' :
					$webRootConfigOptions['DisplayTraces'] = false;
					break;
				default :
					break;
			}
		} else if ( $webRootConfigOptions['Deployment'] === self::DEVELOPMENT ) {
			$webRootConfigOptions['DisplayTraces'] = true;
		}

		// Check if these options should be allowed to override the ones specified in the cache or XML config
		if ($overwrite) {
			// Set these options, overwriting existing settings if necessary
			foreach($webRootConfigOptions as $key => $value) {
				self::$configuration_options[$key] = $value;
			}
		} else {
			// Set these options, respecting existing settings
			foreach($webRootConfigOptions as $key => $value) {
				if (!isset(self::$configuration_options[$key])) {
					self::$configuration_options[$key] = $value;
				}
			}
		}

	}

	public static function loadFrameworkConfigurationCache( $overwrite = true, $config_cache_path = './ConfigCache.php' ) {
		$retVal = false;

		$config_cache_path = self::getFrameworkConfigurationCachePath() . self::getFrameworkConfigurationCacheFilename();

		if ( file_exists($config_cache_path) && is_file($config_cache_path) && is_readable($config_cache_path) ) {
			self::$configuration_options = eval( 'return ' . file_get_contents($config_cache_path) .';' );

			// Grab our environment variables to determine which application and deployment to run

			foreach (self::$configuration_possible_options as $possible_option_key => $option_default_value) {
				if (!isset(self::$configuration_options[$possible_option_key])) {
					self::$configuration_options[$possible_option_key] = $option_default_value;
				}
			}

			// No errors
			$retVal = true;
		}

		return $retVal;
	}

	public static function loadFrameworkConfigurationXML( $overwrite = true, $config_xml_path = './Config.xml' ) {
		if ( file_exists($config_xml_path) && is_file($config_xml_path) && is_readable($config_xml_path) ) {
			$configXMLObject = simplexml_load_file( $config_xml_path );

			// TODO Error handling
			// $errors = libxml_get_errors();
			// echo_r($errors);

			foreach( $configXMLObject->xpath( '/tns:Configuration/System/Component' ) as $component ) {
				$componentID = (string) $component['id'];
				
				if (isset(self::$configuration_possible_options[$componentID])) {
					if (!isset(self::$configuration_options[$componentID])) {
						self::$configuration_options[$componentID] = (string) $component['value'];
					}
				}
			}

			foreach( $configXMLObject->xpath( '/tns:Configuration/System/Option' ) as $option ) {
				$optionID = (string) $option['id'];
				
				if (isset(self::$configuration_possible_options[$optionID])) {
					if (!isset(self::$configuration_options[$optionID])) {
						self::$configuration_options[$optionID] = (string) $option['value'];
					}
				}
			}

		} else {
			trigger_error("Configuration XML for eGloo Framework not found");
		}
	}

	public static function getFrameworkConfigurationCachePath() {
		return '/var/tmp/com.egloo.framework.cache/';
	}

	public static function getApplicationConfigurationCachePath() {
		return '/var/tmp/com.egloo.application.cache/';
	}

	public static function getFrameworkConfigurationCacheFilename() {
		return md5(realpath('.')) . '.gloocache';
	}

	public static function getApplicationConfigurationCacheFilename() {
		return md5(realpath('.')) . '.gloocache';
	}

	public static function writeFrameworkConfigurationCache( $config_cache_path = null ) {
		if (!$config_cache_path) {
			if ( !is_writable( self::getFrameworkConfigurationCachePath() ) ) {
			    mkdir( self::getFrameworkConfigurationCachePath() );
			}

			$config_cache_path = self::getFrameworkConfigurationCachePath() . self::getFrameworkConfigurationCacheFilename();
		}

		// Dump our configuration set
		$config_dump = var_export(self::$configuration_options, TRUE);
		file_put_contents($config_cache_path, $config_dump);
	}

	public static function writeFrameworkConfigurationXML( $config_xml_path = './Config.xml' ) {
		if ( file_exists($config_xml_path) ) {
			$full_config_xml_path = realpath($config_xml_path);
			$folder = dirname($full_config_xml_path);
			echo $full_config_xml_path;
			die_r($folder);
		} else {
			echo_r("Here");
			echo_r($config_xml_path);
			// echo_r(preg_replace('~([a-zA-Z0-9. ]+/)*?(Config.xml)~', '$1', $config_xml_path));
			$full_parent_directory_path = realpath(preg_replace('~^([a-zA-Z0-9. ]+/)*?([a-zA-Z0-9.]*)$~', '$1', $config_xml_path));
			$config_xml_filename = preg_replace('~^([a-zA-Z0-9. ]+/)*?([a-zA-Z0-9.]*)$~', '$2', $config_xml_path);
			
			echo_r("Parent Directory: " . $full_parent_directory_path);
			echo_r("Configuration XML Filename: " . $config_xml_filename);
			
			if (is_writable($full_parent_directory_path)) {
				echo_r("Writing");

			}

			die;
		}

        if ( is_writable( $config_xml_path ) ) {

        } else {
	
		}

		if ( file_exists($config_xml_path) && is_file($config_xml_path) && is_readable($config_xml_path) ) {
			$configXML = simplexml_load_file( $config_xml_path );

			$character = $configXML->movie[0]->characters->addChild('character');
			$character->addChild('name', 'Mr. Parser');
			$character->addChild('actor', 'John Doe');

			$rating = $configXML->movie[0]->addChild('rating', 'PG');
			$rating->addAttribute('type', 'mpaa');

			echo $configXML->asXML();
		} else {
			trigger_error("Configuration XML for eGloo Framework not found");
		}
	}

	public static function loadApplicationConfigurationCache( $application, $overwrite = true, $config_cache_path = './ConfigCache.php' ) {
		
	}

	public static function loadApplicationConfigurationXML( $application, $overwrite = true, $config_xml_path = './Config.xml' ) {
		
	}

	public static function writeApplicationConfigurationCache( $application, $config_cache_path = './ConfigCache.php' ) {
		
	}

	public static function writeApplicationConfigurationXML( $application, $config_xml_path = './Config.xml' ) {
		
	}

	public static function loadConfigurationOptions( $overwrite = true, $prefer_htaccess = true, $config_xml = './Config.xml', $config_cache = null ) {
		$success = self::loadFrameworkConfigurationCache($overwrite, $config_cache);

		if (!$success) {
			self::loadFrameworkConfigurationXML($overwrite, $config_xml);
			self::writeFrameworkConfigurationCache($config_cache);
		}

		if ($prefer_htaccess) {
			self::loadWebRootConfig($overwrite);
		}
	}

	public static function getApplicationName() {
		return self::$configuration_options['ApplicationName'];
	}

	public static function getApplicationPath() {
		return self::$configuration_options['ApplicationPath'];
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