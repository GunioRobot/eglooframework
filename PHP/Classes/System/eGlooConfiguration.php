<?php

final class eGlooConfiguration {

	/* Class Constants */

	// Deployment Type
	const DEVELOPMENT	= 0xff;
	const STAGING		= 0xfe;
	const PRODUCTION	= 0xf8;

	// Database Engines
	const DOCTRINE		= 0x00;
	const MYSQL			= 0x01;
	const POSTGRESQL	= 0x02;

	/* Static Members */
	private static $rewriteBase = '/';
 	private static $web_root = null;
	private static $userAgentHash = null;

	// Configuration Attributes
	private static $configuration_options = array();
	private static $system_configuration = array();
	private static $configuration_possible_options = array();
	private static $uniqueInstanceID = null;

	// Runtime Initialization / Configuration

	public static function loadConfigurationOptions( $overwrite = true, $prefer_htaccess = true, $config_xml = './Config.xml', $config_cache = null ) {
		$useRuntimeCache = self::getUseRuntimeCache();

		// TODO move this somewhere cleaner
		self::$configuration_options['egDatabaseConnections'] = array();

		if ( ($useRuntimeCache && !self::loadRuntimeCache()) || !$useRuntimeCache ) {
			$success = self::loadFrameworkSystemCache();

			if (!$success) {
				self::loadFrameworkSystemXML();
				self::writeFrameworkSystemCache();
			}

			$success = self::loadFrameworkConfigurationCache($overwrite, $config_cache);

			if (!$success) {
				self::loadFrameworkConfigurationXML($overwrite, $config_xml);
				self::writeFrameworkConfigurationCache($config_cache);
			}

			$application_path = eGlooConfiguration::getApplicationPath();

			$success = self::loadApplicationConfigurationCache($application_path, $overwrite, $config_cache);
		
			if (!$success) {
				self::loadApplicationConfigurationXML($application_path, $overwrite);
				self::writeApplicationConfigurationCache($application_path);
			}

			if ($prefer_htaccess) {
				self::loadWebRootConfig($overwrite);
			}

			if (eGlooConfiguration::getUseRuntimeCache()) {
				self::writeRuntimeCache();
			}
		}

		// Set the rewrite base
		if ($_SERVER['SCRIPT_NAME'] !== '/index.php') {
			$matches = array();
			preg_match('~^(.*)?(index.php)$~', $_SERVER['SCRIPT_NAME'], $matches);
			self::$rewriteBase = $matches[1];
		}

		self::$uniqueInstanceID = md5(realpath('.') . self::getApplicationPath() . self::getUIBundleName());
	}

	public static function loadWebRootConfig( $overwrite = true ) {
		$webRootConfigOptions = array();
		$webRootConfigOptions['egApplication']		= $_SERVER['EG_APP'];
		$webRootConfigOptions['egApplicationName']		= preg_replace('~([a-zA-Z0-9/ ])+?/([a-zA-Z0-9 ]*?)\.gloo~', '$2', $_SERVER['EG_APP']);
		$webRootConfigOptions['egInterfaceBundle']		= $_SERVER['EG_UI'];

		switch( $_SERVER['EG_CACHE'] ) {
			case 'ON' :
				$webRootConfigOptions['egCacheEnabled'] = true;
				break;
			case 'OFF' :
			default :
				$webRootConfigOptions['egCacheEnabled'] = false;
				break;
		}

		switch( $_SERVER['EG_CACHE_FILE'] ) {
			case 'ON' :
				$webRootConfigOptions['egFileCacheEnabled'] = true;
				break;
			case 'OFF' :
			default :
				$webRootConfigOptions['egFileCacheEnabled'] = false;
				break;
		}

		switch( $_SERVER['EG_CACHE_MEMCACHE'] ) {
			case 'ON' :
				$webRootConfigOptions['egMemcacheCacheEnabled'] = true;
				break;
			case 'OFF' :
			default :
				$webRootConfigOptions['egMemcacheCacheEnabled'] = false;
				break;
		}

		// Determine our deployment type
		switch( $_SERVER['EG_ENV'] ) {
			case 'DEVELOPMENT' :
				$webRootConfigOptions['egEnvironment'] = self::DEVELOPMENT;
				$webRootConfigOptions['egSanityCheckClassLoading'] = true;
				break;
			case 'STAGING' :
				$webRootConfigOptions['egEnvironment'] = self::STAGING;
				break;
			case 'PRODUCTION' :
				$webRootConfigOptions['egEnvironment'] = self::PRODUCTION;
				break;
			default :
				$webRootConfigOptions['egEnvironment'] = self::DEVELOPMENT;
				break;
		}

		if (isset($_SERVER['EG_SANITY_CHECK_CLASS_LOADING'])) {
			switch( $_SERVER['EG_SANITY_CHECK_CLASS_LOADING'] ) {
				case 'ON' :
					$webRootConfigOptions['egSanityCheckClassLoading'] = true;
					break;
				case 'OFF' :
				default :
					$webRootConfigOptions['egSanityCheckClassLoading'] = false;
					break;
			}
		}

		// Determine which DB system we're using
		switch( $_SERVER['EG_DB_ENGINE'] ) {
			case 'DOCTRINE' :
				$webRootConfigOptions['egDatabaseEngine'] = self::DOCTRINE;
				break;
			case 'MYSQL' :
				$webRootConfigOptions['egDatabaseEngine'] = self::MYSQL;
				break;
			case 'POSTGRESQL' :
				$webRootConfigOptions['egDatabaseEngine'] = self::POSTGRESQL;
				break;
			default:
				$webRootConfigOptions['egDatabaseEngine'] = self::POSTGRESQL;
				break;
		}

		if ( isset($_SERVER['EG_DB_CONNECTION_PRIMARY_HOST']) ) {
			self::$configuration_options['egDatabaseConnections']['egPrimary'] = array();

			self::$configuration_options['egDatabaseConnections']['egPrimary']['name']		= $_SERVER['EG_DB_CONNECTION_PRIMARY_NAME'];
			self::$configuration_options['egDatabaseConnections']['egPrimary']['host']		= $_SERVER['EG_DB_CONNECTION_PRIMARY_HOST'];
			self::$configuration_options['egDatabaseConnections']['egPrimary']['port']		= $_SERVER['EG_DB_CONNECTION_PRIMARY_PORT'];
			self::$configuration_options['egDatabaseConnections']['egPrimary']['database']	= $_SERVER['EG_DB_CONNECTION_PRIMARY_DATABASE'];
			self::$configuration_options['egDatabaseConnections']['egPrimary']['user']		= $_SERVER['EG_DB_CONNECTION_PRIMARY_USER'];
			self::$configuration_options['egDatabaseConnections']['egPrimary']['password']	= $_SERVER['EG_DB_CONNECTION_PRIMARY_PASSWORD'];

			// Determine which DB system we're using
			switch( $_SERVER['EG_DB_CONNECTION_PRIMARY_ENGINE'] ) {
				case 'DOCTRINE' :
					self::$configuration_options['egDatabaseConnections']['egPrimary']['engine'] = self::DOCTRINE;
					break;
				case 'MYSQL' :
					self::$configuration_options['egDatabaseConnections']['egPrimary']['engine'] = self::MYSQL;
					break;
				case 'POSTGRESQL' :
					self::$configuration_options['egDatabaseConnections']['egPrimary']['engine'] = self::POSTGRESQL;
					break;
				default:
					self::$configuration_options['egDatabaseConnections']['egPrimary']['engine'] = self::POSTGRESQL;
					break;
			}
		}

		// Check if we're displaying errors in the UI or not
		if ( isset($_SERVER['EG_DISPLAY_ERRORS']) ) {
			switch( $_SERVER['EG_DISPLAY_ERRORS'] ) {
				case 'ON' :
					$webRootConfigOptions['egDisplayErrors'] = true;
					break;
				case 'OFF' :
					$webRootConfigOptions['egDisplayErrors'] = false;
					break;
				default :
					break;
			}
		} else if ( $webRootConfigOptions['egEnvironment'] === self::DEVELOPMENT ) {
			$webRootConfigOptions['egDisplayErrors'] = true;
		}

		// Check if we're displaying traces in the UI or not
		if ( isset($_SERVER['EG_DISPLAY_TRACES']) ) {
			switch( $_SERVER['EG_DISPLAY_TRACES'] ) {
				case 'ON' :
					$webRootConfigOptions['egDisplayTraces'] = true;
					break;
				case 'OFF' :
					$webRootConfigOptions['egDisplayTraces'] = false;
					break;
				default :
					break;
			}
		} else if ( $webRootConfigOptions['egEnvironment'] === self::DEVELOPMENT ) {
			$webRootConfigOptions['egDisplayTraces'] = true;
		}

		if ( isset($_SERVER['EG_LOG_LEVEL']) ) {
			$webRootConfigOptions['egLogLevel'] = $_SERVER['EG_LOG_LEVEL'];
		}

		if ( isset($_SERVER['EG_LOG_FORMAT']) ) {
			$webRootConfigOptions['egLogFormat'] = $_SERVER['EG_LOG_FORMAT'];
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

	public static function getRuntimeConfigurationCacheFilename() {
		return md5(realpath('.')) . '.runtime.gloocache';
	}

	public static function getRuntimeConfigurationCachePath() {
		return '/var/tmp/com.egloo.cache/';
	}

	public static function loadRuntimeCache() {
		$retVal = false;

		$runtime_cache_path = self::getRuntimeConfigurationCachePath() . self::getRuntimeConfigurationCacheFilename();

		if ( file_exists($runtime_cache_path) && is_file($runtime_cache_path) && is_readable($runtime_cache_path) ) {
			self::$configuration_options = eval( 'return ' . file_get_contents($runtime_cache_path) .';' );

			// No errors
			$retVal = true;
		}

		return $retVal;
	}

	public static function writeRuntimeCache( $runtime_cache_path = null ) {
		if (!$runtime_cache_path) {
			if ( !is_writable( self::getRuntimeConfigurationCachePath() ) ) {
				$old_umask = umask(0);
			    mkdir( self::getRuntimeConfigurationCachePath(), 0775 );
				umask($old_umask);
			}

			$runtime_cache_path = self::getRuntimeConfigurationCachePath() . self::getRuntimeConfigurationCacheFilename();
		}

		// Dump our configuration set
		$config_dump = var_export(self::$configuration_options, TRUE);
		file_put_contents($runtime_cache_path, $config_dump);
	}

	public static function clearRuntimeCache( $runtime_cache_path = null ) {
		$retVal = false;

		echo_r("Preparing to delete runtime cache... ");

		if (!$runtime_cache_path) {
			if ( !is_writable( self::getRuntimeConfigurationCachePath() ) ) {
				// TODO figure out what to do here
				$retVal = false;
			}

			$runtime_cache_path = self::getRuntimeConfigurationCachePath() . self::getRuntimeConfigurationCacheFilename();
		}

		echo_r("Runtime cache path is " . $runtime_cache_path);

		if ( file_exists($runtime_cache_path) && is_file($runtime_cache_path) && is_writable($runtime_cache_path) ) {
			echo_r("Deleting runtime cache at " . $runtime_cache_path);
			$retVal = unlink($runtime_cache_path);
		} else {
			echo_r("Runtime cache not found at " . $runtime_cache_path);
			$retVal = true;
		}

		return $retVal;
	}

	public static function clearAllCache() {
		$retVal = false;

		$retVal = self::clearRuntimeCache() && self::clearApplicationCache() && self::clearFrameworkCache() && self::clearSystemCache();

		return $retVal;
	}

	// Application Configuration

	public static function getApplicationConfigurationCacheFilename( $application_name = '' ) {
		return md5(realpath('.') . $application_name) . '.application.gloocache';
	}

	public static function getApplicationConfigurationCachePath() {
		return '/var/tmp/com.egloo.cache/';
	}

	public static function loadApplicationConfigurationCache( $application_name, $overwrite = true ) {
		$retVal = false;

		$config_cache_path = self::getApplicationConfigurationCachePath() . self::getApplicationConfigurationCacheFilename($application_name);

		if ( file_exists($config_cache_path) && is_file($config_cache_path) && is_readable($config_cache_path) ) {
			$cached_options = eval( 'return ' . file_get_contents($config_cache_path) .';' );

			foreach ($cached_options as $possible_option_key => $option_default_value) {
				if (isset(self::$configuration_possible_options[$possible_option_key])) {
					self::$configuration_options[$possible_option_key] = $option_default_value;
				}
			}

			if (!isset(self::$configuration_options['CustomVariables'])) {
				self::$configuration_options['CustomVariables'] = array();
			}

			if (isset($cached_options['CustomVariables'])) {
				foreach($cached_options['CustomVariables'] as $key => $value) {
					self::$configuration_options['CustomVariables'][$key] = $value;
				}
			}

			// No errors
			$retVal = true;
		}

		return $retVal;
	}

	public static function loadApplicationConfigurationXML( $application_name, $overwrite = true, $config_xml_filename = 'Config.xml' ) {
		$config_xml_path = self::getApplicationsPath() . '/' . self::getApplicationPath() . '/Configuration/' . $config_xml_filename;

		if ( file_exists($config_xml_path) && is_file($config_xml_path) && is_readable($config_xml_path) ) {
			$configXMLObject = simplexml_load_file( $config_xml_path );

			// TODO Error handling
			// $errors = libxml_get_errors();
			// echo_r($errors);

			// foreach( $configXMLObject->xpath( '/tns:Configuration/tns:System/tns:Component' ) as $component ) {
			// 	$componentID = (string) $component['id'];
			// 
			// 	if (isset(self::$configuration_possible_options[$componentID])) {
			// 		// if (!isset(self::$configuration_options[$componentID])) {
			// 			self::$configuration_options[$componentID] = (string) $component['value'];
			// 		// }
			// 	}
			// }
			// 
			foreach( $configXMLObject->xpath( '/tns:Configuration/tns:Applications/tns:Option' ) as $option ) {
				$optionID = (string) $option['id'];
			
				if (isset(self::$configuration_possible_options[$optionID])) {
					// if (!isset(self::$configuration_options[$optionID])) {
						self::$configuration_options[$optionID] = (string) $option['value'];
					// }
				}
			}

			if (isset(self::$configuration_options['AppBuild'])) {
				$build_file_path = eGlooConfiguration::getApplicationsPath() . '/' .
					eGlooConfiguration::getApplicationPath() . '/' . self::$configuration_options['AppBuild'];

				self::$configuration_options['AppBuild'] = trim(file_get_contents($build_file_path));
			}

			// 
			// // Load applications after system... 
			// foreach( $configXMLObject->xpath( '/tns:Configuration/tns:Applications/tns:Component' ) as $component ) {
			// 	$componentID = (string) $component['id'];
			// 
			// 	if (isset(self::$configuration_possible_options[$componentID])) {
			// 		// if (!isset(self::$configuration_options[$componentID])) {
			// 			self::$configuration_options[$componentID] = (string) $component['value'];
			// 		// }
			// 	}
			// }

			if (!isset(self::$configuration_options['CustomVariables'])) {
				self::$configuration_options['CustomVariables'] = array();
			}

			foreach( $configXMLObject->xpath( '/tns:Configuration/tns:Applications/tns:Option[@type="customVariable"]' ) as $option ) {
				$optionID = (string) $option['id'];

				self::$configuration_options['CustomVariables'][$optionID] = (string) $option['value'];
			}

		} else {
			trigger_error("Configuration XML for eGloo application not found");
		}
	}

	public static function writeApplicationConfigurationCache( $application_path, $config_cache_path = null ) {
		if (!$config_cache_path) {
			if ( !is_writable( self::getApplicationConfigurationCachePath() ) ) {
				$old_umask = umask(0);
			    mkdir( self::getApplicationConfigurationCachePath(), 0775 );
				umask($old_umask);
			}

			$config_cache_path = self::getApplicationConfigurationCachePath() . self::getApplicationConfigurationCacheFilename($application_path);
		}

		// Dump our configuration set
		$config_dump = var_export(self::$configuration_options, TRUE);
		file_put_contents($config_cache_path, $config_dump);
	}

	public static function writeApplicationConfigurationXML( $application_name, $config_xml_path = 'Config.xml' ) {
		
	}

	public static function clearApplicationCache( $application_cache_path = null ) {
		$retVal = false;

		echo_r("Preparing to delete application cache... ");

		if (!$application_cache_path) {
			if ( !is_writable( self::getApplicationConfigurationCachePath() ) ) {
				// TODO figure out what to do here
				$retVal = false;
			}

			$application_cache_path = self::getApplicationConfigurationCachePath() . self::getApplicationConfigurationCacheFilename();
		}

		echo_r("Application cache path is " . $application_cache_path);

		if ( file_exists($application_cache_path) && is_file($application_cache_path) && is_writable($application_cache_path) ) {
			echo_r("Deleting application cache at " . $application_cache_path);
			$retVal = unlink($application_cache_path);
		} else {
			echo_r("Application cache not found at " . $application_cache_path);
			$retVal = true;
		}

		return $retVal;
	}

	// Framework Configuration

	public static function getFrameworkConfigurationCacheFilename() {
		return md5(realpath('.')) . '.framework.gloocache';
	}

	public static function getFrameworkConfigurationCachePath() {
		return '/var/tmp/com.egloo.cache/';
	}

	public static function loadFrameworkConfigurationCache( $overwrite = true, $config_cache_path = './ConfigCache.php' ) {
		$retVal = false;

		$config_cache_path = self::getFrameworkConfigurationCachePath() . self::getFrameworkConfigurationCacheFilename();

		if ( file_exists($config_cache_path) && is_file($config_cache_path) && is_readable($config_cache_path) ) {
			$cached_options = eval( 'return ' . file_get_contents($config_cache_path) .';' );
			// self::$configuration_options = eval( 'return ' . file_get_contents($config_cache_path) .';' );

			// Grab our environment variables to determine which application and deployment to run

			foreach ($cached_options as $possible_option_key => $option_default_value) {
				if (isset(self::$configuration_possible_options[$possible_option_key])) {
					self::$configuration_options[$possible_option_key] = $option_default_value;
				}
				// if (!isset(self::$configuration_options[$possible_option_key])) {
					// self::$configuration_options[$possible_option_key] = $option_default_value['value'];
				// }
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

			foreach( $configXMLObject->xpath( '/tns:Configuration/tns:System/tns:Component' ) as $component ) {
				$componentID = (string) $component['id'];

				if (isset(self::$configuration_possible_options[$componentID])) {
					// if (!isset(self::$configuration_options[$componentID])) {
						self::$configuration_options[$componentID] = (string) $component['value'];
					// }
				}
			}

			foreach( $configXMLObject->xpath( '/tns:Configuration/tns:System/tns:Option' ) as $option ) {
				$optionID = (string) $option['id'];

				if (isset(self::$configuration_possible_options[$optionID])) {
					// if (!isset(self::$configuration_options[$optionID])) {
						self::$configuration_options[$optionID] = (string) $option['value'];
					// }
				}
			}

			// Load applications after system... 
			foreach( $configXMLObject->xpath( '/tns:Configuration/tns:Applications/tns:Component' ) as $component ) {
				$componentID = (string) $component['id'];

				if (isset(self::$configuration_possible_options[$componentID])) {
					// if (!isset(self::$configuration_options[$componentID])) {
						self::$configuration_options[$componentID] = (string) $component['value'];
					// }
				}
			}

			foreach( $configXMLObject->xpath( '/tns:Configuration/tns:Applications/tns:Option' ) as $option ) {
				$optionID = (string) $option['id'];

				if (isset(self::$configuration_possible_options[$optionID])) {
					// if (!isset(self::$configuration_options[$optionID])) {
						self::$configuration_options[$optionID] = (string) $option['value'];
					// }
				}
			}

			if (!isset(self::$configuration_options['CustomVariables'])) {
				self::$configuration_options['CustomVariables'] = array();
			}

			foreach( $configXMLObject->xpath( '/tns:Configuration/tns:Applications/tns:Option[@type="customVariable"]' ) as $option ) {
				$optionID = (string) $option['id'];

				self::$configuration_options['CustomVariables'][$optionID] = (string) $option['value'];
			}
		} else {
			trigger_error("Configuration XML for eGloo Framework not found");
		}
	}

	public static function writeFrameworkConfigurationCache( $config_cache_path = null ) {
		if (!$config_cache_path) {
			if ( !is_writable( self::getFrameworkConfigurationCachePath() ) ) {
				$old_umask = umask(0);
			    mkdir( self::getFrameworkConfigurationCachePath(), 0775 );
				umask($old_umask);
			}

			$config_cache_path = self::getFrameworkConfigurationCachePath() . self::getFrameworkConfigurationCacheFilename();
		}

		// Dump our configuration set
		$config_dump = var_export(self::$configuration_options, TRUE);
		file_put_contents($config_cache_path, $config_dump);
	}

	public static function writeFrameworkConfigurationXML( $config_xml_path = './Config1.xml' ) {
		if ( file_exists($config_xml_path) ) {
			$full_config_xml_path = realpath($config_xml_path);
			$folder = dirname($full_config_xml_path);
			echo $full_config_xml_path;
			die_r($folder);
		} else {
			$full_parent_directory_path = realpath(preg_replace('~^([a-zA-Z0-9. ]+/)*?([a-zA-Z0-9.]*)$~', '$1', $config_xml_path));
			$config_xml_filename = preg_replace('~^([a-zA-Z0-9. ]+/)*?([a-zA-Z0-9.]*)$~', '$2', $config_xml_path);

			if (is_writable($full_parent_directory_path)) {
				// echo_r("Writing");
				// echo_r($config_xml_filename);
			}

			$xmlData = '';

			$xmlData .= '<?xml version="1.0" encoding="UTF-8"?>';
			$xmlData .= '<!--* Custom Generated eGloo Framework Configuration File *-->';
			$xmlData .= '<tns:Configuration xmlns:tns="com.egloo.www/eGlooConfiguration" ';
			$xmlData .= 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ';
			$xmlData .= 'xsi:schemaLocation="com.egloo.www/eGlooConfiguration ../XML/schemas/eGlooConfiguration.xsd">';
			
			$xmlData .= '</tns:Configuration>';

			$xmlObject = new SimpleXMLElement($xmlData);

			$applicationsXMLObject 	= $xmlObject->addChild('Applications');
			$cachingXMLObject 		= $xmlObject->addChild('Caching');
			$cubesXMLObject 		= $xmlObject->addChild('Cubes');
			$databasesXMLObject 	= $xmlObject->addChild('Databases');
			$documentsXMLObject 	= $xmlObject->addChild('Documents');
			$frameworkXMLObject 	= $xmlObject->addChild('Framework');
			$libraryXMLObject 		= $xmlObject->addChild('Library');
			$networkXMLObject 		= $xmlObject->addChild('Network');
			$peeringXMLObject 		= $xmlObject->addChild('Peering');
			$systemXMLObject 		= $xmlObject->addChild('System');
			
			foreach (self::$configuration_possible_options as $key => $value) {
				$childXMLObject = null;

				switch($value['elementType']) {
					case 'Component' :
						$childXMLObject = $systemXMLObject->addChild('Component');
						break;
					case 'Option' :
						$childXMLObject = $systemXMLObject->addChild('Option');
						break;
					default :
						break;
				}
				header('Content-type: text');

				$childXMLObject->addAttribute('id', $key);
				$childXMLObject->addAttribute('displayName', $value['displayName']);
				$childXMLObject->addAttribute('override', $value['override']);
				$childXMLObject->addAttribute('type', $value['type']);

				if (isset(self::$configuration_options[$key])) {
					$newValue = null;

					switch(self::$configuration_options[$key]) {
						case false :
							$newValue = 'false';
							break;
						case true :
							$newValue = 'true';
							break;
						default :
							$newValue = self::$configuration_options[$key];
							break;
					}

					$childXMLObject->addAttribute('value', $newValue);
				} else {
					$newValue = null;

					switch($value['value']) {
						case false :
							$newValue = 'false';
							break;
						case true :
							$newValue = 'true';
							break;
						default :
							$newValue = $value['value'];
							break;
					}

					$childXMLObject->addAttribute('value', $value['value']);
				}

				$childXMLObject->addAttribute('required', $value['required']);

			}

			$domObject = new DOMDocument();
			$domObject->loadXML($xmlObject->asXML());

			$domObject->formatOutput = true;
			$formattedXML = $domObject->saveXML( $domObject->documentElement, LIBXML_NSCLEAN );

			// $domObject->save( $config_xml_path );

			echo $formattedXML;
			// 
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

	public static function clearFrameworkCache( $config_cache_path = null ) {
		$retVal = false;

		echo_r("Preparing to delete framework cache... ");

		if (!$config_cache_path) {
			if ( !is_writable( self::getFrameworkConfigurationCachePath() ) ) {
				// TODO figure out what to do here
				$retVal = false;
			}

			$config_cache_path = self::getFrameworkConfigurationCachePath() . self::getFrameworkConfigurationCacheFilename();
		}

		echo_r("Framework cache path is " . $config_cache_path);

		if ( file_exists($config_cache_path) && is_file($config_cache_path) && is_writable($config_cache_path) ) {
			echo_r("Deleting framework cache at " . $config_cache_path);
			$retVal = unlink($config_cache_path);
		} else {
			echo_r("Framework cache not found at " . $config_cache_path);
			$retVal = true;
		}

		return $retVal;
	}

	// System Package Distribution Configuration

	public static function getFrameworkSystemCacheFilename() {
		return md5(realpath('.')) . '.system.gloocache';
	}

	public static function getFrameworkSystemCachePath() {
		return '/var/tmp/com.egloo.cache/';
	}

	public static function loadFrameworkSystemCache( $overwrite = true, $config_cache_path = './SystemCache.php' ) {
		$retVal = false;

		$config_cache_path = self::getFrameworkSystemCachePath() . self::getFrameworkSystemCacheFilename();

		if ( file_exists($config_cache_path) && is_file($config_cache_path) && is_readable($config_cache_path) ) {
			self::$system_configuration = eval( 'return ' . file_get_contents($config_cache_path) .';' );

			// Grab our environment variables to determine which application and deployment to run

			foreach (self::$system_configuration as $key => $value) {
				self::$configuration_options[$key] = $value['value'];
				
				if ($value['override'] === 'true') {
					self::$configuration_possible_options[$key] = $value;
				}
			}

			// No errors
			$retVal = true;
		}

		return $retVal;
	}

	public static function loadFrameworkSystemXML( $system_xml_path = './System.xml' ) {
		if ( file_exists($system_xml_path) && is_file($system_xml_path) && is_readable($system_xml_path) ) {
			$configXMLObject = simplexml_load_file( $system_xml_path );

			// // TODO Error handling
			// $errors = libxml_get_errors();
			// echo_r($errors);

			$system_configuration = array();

			foreach( $configXMLObject->xpath( '/tns:Configuration' ) as $configuration ) {
				foreach($configuration->children('com.egloo.www/eGlooConfiguration') as $section) {
					$sectionID = (string) $section->getName();
					$system_configuration[$sectionID] = array('Components' => array(), 'Options' => array());

					foreach($section->xpath( 'tns:Component' ) as $component) {
						$nextComponent = array();

						$nextComponent['id'] = (string) $component['id'];
						$nextComponent['displayName'] = (string) $component['displayName'];
						$nextComponent['override'] = (string) $component['override'];
						$nextComponent['type'] = (string) $component['type'];
						$nextComponent['value'] = (string) $component['value'];
						$nextComponent['required'] = (string) $component['required'];

						$system_configuration[$sectionID]['Components'][] = $nextComponent;
					}

					foreach($section->xpath( 'tns:Option' ) as $option) {
						$nextOption = array();

						$nextOption['id'] = (string) $option['id'];
						$nextOption['displayName'] = (string) $option['displayName'];
						$nextOption['override'] = (string) $option['override'];
						$nextOption['type'] = (string) $option['type'];
						$nextOption['value'] = (string) $option['value'];
						$nextOption['required'] = (string) $option['required'];

						$system_configuration[$sectionID]['Options'][] = $nextOption;
					}

				}

			}

			//////////////////////////////////////// HMMMMMMMMMMM SHOULD THIS BE HERE?  Should be application array set
			foreach($system_configuration['Applications']['Components'] as $component) {
				if ($component['override'] === 'true') {
					self::$configuration_possible_options[$component['id']] = $component;
				}

				self::$system_configuration[$component['id']] = $component;
				self::$configuration_options[$component['id']] = $component['value'];
			}

			//////////////////////////////////////// HMMMMMMMMMMM SHOULD THIS BE HERE?  Should be application array set
			foreach($system_configuration['Applications']['Options'] as $option) {
				if ($option['override'] === 'true') {
					self::$configuration_possible_options[$option['id']] = $option;
				}

				self::$system_configuration[$option['id']] = $option;
				self::$configuration_options[$option['id']] = $option['value'];
			}

			foreach($system_configuration['System']['Components'] as $component) {
				if ($component['override'] === 'true') {
					self::$configuration_possible_options[$component['id']] = $component;
				}

				self::$system_configuration[$component['id']] = $component;
				self::$configuration_options[$component['id']] = $component['value'];
			}

			foreach($system_configuration['System']['Options'] as $option) {
				if ($option['override'] === 'true') {
					self::$configuration_possible_options[$option['id']] = $option;
				}

				self::$system_configuration[$option['id']] = $option;
				self::$configuration_options[$option['id']] = $option['value'];
			}

			// self::$system_configuration = self::$configuration_options;
		} else {
			if (!file_exists($system_xml_path)) {
				trigger_error("System XML for eGloo Framework not found.");
			} else if (!is_file($system_xml_path)) {
				trigger_error("Expected path for System XML for eGloo Framework exists but is not a valid file.");
			} else if (!is_readable($system_xml_path)) {
				trigger_error("System XML for eGloo Framework file exists but cannot be read.  Check file permissions.");
			} else {
				trigger_error("Unknown Error Reading System XML for eGloo Framework.");
			}

			exit;
		}
	}

	public static function writeFrameworkSystemCache( $system_cache_path = null ) {
		if (!$system_cache_path) {
			if ( !is_writable( self::getFrameworkSystemCachePath() ) ) {
				$old_umask = umask(0);
			    mkdir( self::getFrameworkSystemCachePath(), 0775 );
				umask($old_umask);
			}

			$system_cache_path = self::getFrameworkSystemCachePath() . self::getFrameworkSystemCacheFilename();
		}

		// Dump our configuration set
		$system_dump = var_export(self::$system_configuration, TRUE);
		file_put_contents($system_cache_path, $system_dump);
	}

	public static function writeFrameworkSystemXML( $configuration_options, $overwrite, $skeleton_xml_path = null, $config_xml_path = null ) {
		if ($skeleton_xml_path === null) {
			$skeleton_xml_path = '../XML/System.skeleton.xml';
		}

		if ($config_xml_path === null) {
			$config_xml_path = './System.generated.xml';
		}

		if ( file_exists($skeleton_xml_path) && is_file($skeleton_xml_path) && is_readable($skeleton_xml_path) ) {
			$configXMLObject = simplexml_load_file( $skeleton_xml_path );

			// TODO Error handling
			// $errors = libxml_get_errors();
			// echo_r($errors);

			$system_configuration = array();

			// $configuration_options['System'] = array('Components' => array('DocumentRoot' => array('value' => 'junk')));

			foreach( $configXMLObject->xpath( '/tns:Configuration' ) as $configuration ) {
				foreach($configuration->children() as $section) {
					$sectionID = (string) $section->getName();
					$system_configuration[$sectionID] = array('Components' => array(), 'Options' => array());
					$options_section = array();

					if (isset($configuration_options[$sectionID])) {
						$options_section = $configuration_options[$sectionID];
					}

					if (isset($options_section['Components'])) {
						$options_components = $options_section['Components'];
					}

					foreach($section->xpath( 'Component' ) as $component) {
						$nextComponent = array();

						$nextComponent['id'] = $component_id = (string) $component['id'];

						$nextComponent['displayName'] = (string) $component['displayName'];
						$nextComponent['override'] = (string) $component['override'];
						$nextComponent['type'] = (string) $component['type'];
						$nextComponent['value'] = (string) $component['value'];
						$nextComponent['required'] = (string) $component['required'];

						if (isset($options_components[$component_id]) && isset($options_components[$component_id]['value'])) {
							$nextComponent['value'] = $options_components[$component_id]['value'];
						}

						$system_configuration[$sectionID]['Components'][] = $nextComponent;
					}

					if (isset($options_section['Options'])) {
						$options_options = $options_section['Options'];
					}

					foreach($section->xpath( 'Option' ) as $option) {
						$nextOption = array();

						$nextOption['id'] = $option_id = (string) $option['id'];
						$nextOption['displayName'] = (string) $option['displayName'];
						$nextOption['override'] = (string) $option['override'];
						$nextOption['type'] = (string) $option['type'];
						$nextOption['value'] = (string) $option['value'];
						$nextOption['required'] = (string) $option['required'];

						if (isset($options_options[$option_id]) && isset($options_options[$option_id]['value'])) {
							$nextOption['value'] = $options_options[$option_id]['value'];
						}

						$system_configuration[$sectionID]['Options'][] = $nextOption;
					}

				}

			}

		} else {
			trigger_error("System Skeleton XML for eGloo Framework not found");
		}

		$full_parent_directory_path = realpath(preg_replace('~^([a-zA-Z0-9. ]+/)*?([a-zA-Z0-9.]*)$~', '$1', $config_xml_path));
		$config_xml_filename = preg_replace('~^([a-zA-Z0-9. ]+/)*?([a-zA-Z0-9.]*)$~', '$2', $config_xml_path);

		$qualified_path = $full_parent_directory_path . '/' . $config_xml_filename;

		if ( file_exists($qualified_path) && !$overwrite) {
			trigger_error('System configuration XML exists - will not overwrite');
			// $full_config_xml_path = realpath($config_xml_path);
			// $folder = dirname($full_config_xml_path);
			// echo $full_config_xml_path;
			// die_r($folder);
		} else {
			if (!is_writable($full_parent_directory_path)) {
				trigger_error('Destination for generated system configuration XML is not writable');
			}

			$xmlData = '';

			$xmlData .= '<?xml version="1.0" encoding="UTF-8"?>';
			$xmlData .= '<!--* Custom Generated eGloo Framework Configuration File *-->';
			$xmlData .= '<tns:Configuration xmlns:tns="com.egloo.www/eGlooConfiguration" ';
			$xmlData .= 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ';
			$xmlData .= 'xsi:schemaLocation="com.egloo.www/eGlooConfiguration ../XML/schemas/eGlooConfiguration.xsd">';
			
			$xmlData .= '</tns:Configuration>';

			$xmlObject = new SimpleXMLElement($xmlData);
			
			foreach ($system_configuration as $sectionID => $section) {

				$sectionXMLObject = $xmlObject->addChild($sectionID);

				foreach($section['Components'] as $component) {
					$componentXMLObject = $sectionXMLObject->addChild('Component');

					$componentXMLObject->addAttribute('id', $component['id']);
					$componentXMLObject->addAttribute('displayName', $component['displayName']);
					$componentXMLObject->addAttribute('override', $component['override']);
					$componentXMLObject->addAttribute('type', $component['type']);

					$componentXMLObject->addAttribute('value', $component['value']);
				}

				foreach($section['Options'] as $option) {
					$optionXMLObject = $sectionXMLObject->addChild('Option');

					$optionXMLObject->addAttribute('id', $option['id']);
					$optionXMLObject->addAttribute('displayName', $option['displayName']);
					$optionXMLObject->addAttribute('override', $option['override']);
					$optionXMLObject->addAttribute('type', $option['type']);

					$optionXMLObject->addAttribute('value', $option['value']);
				}
			}

			$domObject = new DOMDocument();
			$domObject->loadXML($xmlObject->asXML());

			$domObject->formatOutput = true;
			$formattedXML = $domObject->saveXML( $domObject->documentElement, LIBXML_NSCLEAN );

			$domObject->save( $qualified_path );

			return $formattedXML;
		}

	}

	public static function clearSystemCache( $system_cache_path = null ) {
		$retVal = false;

		echo_r("Preparing to delete system cache... ");

		if (!$system_cache_path) {
			if ( !is_writable( self::getFrameworkSystemCachePath() ) ) {
				// TODO figure out what to do here
				$retVal = false;
			}

			$system_cache_path = self::getFrameworkSystemCachePath() . self::getFrameworkSystemCacheFilename();
		}

		echo_r("System cache path is " . $system_cache_path);

		if ( file_exists($system_cache_path) && is_file($system_cache_path) && is_writable($system_cache_path) ) {
			echo_r("Deleting system cache at " . $system_cache_path);
			$retVal = unlink($system_cache_path);
		} else {
			echo_r("System cache not found at " . $system_cache_path);
			$retVal = true;
		}

		return $retVal;
	}


	// Accessors

	public static function getApplicationBuild() {
		$retVal = '';

		if (isset(self::$configuration_options['AppBuild'])) {
			$retVal = self::$configuration_options['AppBuild'];
		}
		
		return $retVal;
	}

	public static function getApplicationMaintenanceVersion() {
		$retVal = '';

		if (isset(self::$configuration_options['AppMaintenanceVersion'])) {
			$retVal = self::$configuration_options['AppMaintenanceVersion'];
		}
		
		return $retVal;
	}

	public static function getApplicationMajorVersion() {
		$retVal = '';

		if (isset(self::$configuration_options['AppMajorVersion'])) {
			$retVal = self::$configuration_options['AppMajorVersion'];
		}
		
		return $retVal;
	}

	public static function getApplicationMinorVersion() {
		$retVal = '';

		if (isset(self::$configuration_options['AppMinorVersion'])) {
			$retVal = self::$configuration_options['AppMinorVersion'];
		}
		
		return $retVal;
	}

	public static function getApplicationName() {
		return self::$configuration_options['egApplicationName'];
	}

	public static function getApplicationPath() {
		return self::$configuration_options['egApplication'];
	}

	public static function getUIBundleName() {
		return self::$configuration_options['egInterfaceBundle'];
	}
	
	public static function getDeployment() {
		return self::$configuration_options['egEnvironment'];
	}

    public static function getApplicationsPath() {
		return self::$configuration_options['ApplicationsPath'];
	}

    public static function getCachePath() {
		return self::$configuration_options['CachePath'];
	}

    public static function getConfigurationPath() {
		return self::$configuration_options['ConfigurationPath'];
	}

	public static function getCubesPath() {
		return self::$configuration_options['CubesPath'];
	}

	public static function getCustomVariable( $index ) {
		if (!isset(self::$configuration_options['CustomVariables'][$index])) {
			throw new ErrorException('Invalid custom variable \'' . $index . '\' requested');
		}

		return self::$configuration_options['CustomVariables'][$index];
	}

	public static function getCustomVariables() {
		if (!isset(self::$configuration_options['CustomVariables'])) {
			self::$configuration_options['CustomVariables'] = array();
		}

		return self::$configuration_options['CustomVariables'];
	}

	public static function getDatabaseConnectionInfo( $connection_name = 'egPrimary' ) {
		$retVal = null;

		if (isset(self::$configuration_options['egDatabaseConnections'][$connection_name])) {
			$retVal = self::$configuration_options['egDatabaseConnections'][$connection_name];
		} else {
			throw new ErrorException('Details for unknown database connection \'' . $connection_name . '\' requested');
		}

		return $retVal;
	}

	public static function getDatabaseEngine() {
		return self::$configuration_options['egDatabaseEngine'];
	}

    public static function getDeploymentType() {
		return self::$configuration_options['egEnvironment'];
	}

    public static function getDisplayErrors() {
		return self::$configuration_options['egDisplayErrors'];
	}

    public static function getDisplayTraces() {
		return self::$configuration_options['egDisplayTraces'];
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

	public static function getBuild() {
		$retVal = '';

		if (isset(self::$configuration_options['egBuild'])) {
			$retVal = self::$configuration_options['egBuild'];
		}
		
		return $retVal;
	}

	public static function getMaintenanceVersion() {
		$retVal = '';

		if (isset(self::$configuration_options['egMaintenanceVersion'])) {
			$retVal = self::$configuration_options['egMaintenanceVersion'];
		}
		
		return $retVal;
	}

	public static function getMajorVersion() {
		$retVal = '';

		if (isset(self::$configuration_options['egMajorVersion'])) {
			$retVal = self::$configuration_options['egMajorVersion'];
		}
		
		return $retVal;
	}

	public static function getMinorVersion() {
		$retVal = '';

		if (isset(self::$configuration_options['egMinorVersion'])) {
			$retVal = self::$configuration_options['egMinorVersion'];
		}
		
		return $retVal;
	}

	public static function getExtraClassPath() {
		return isset(self::$configuration_options['ExtraClassPath']) ? self::$configuration_options['ExtraClassPath'] : '';
	}

    public static function getFrameworkRootPath() {
		return self::$configuration_options['FrameworkRootPath'];
	}

    public static function getLogFormat() {
		if ( !isset(self::$configuration_options['egLogFormat']) ) {
			self::$configuration_options['egLogFormat'] = eGlooLogger::LOG_LOG;
			self::writeFrameworkConfigurationCache();

			if (eGlooConfiguration::getUseRuntimeCache()) {
				self::writeRuntimeCache();
			}
		} else if (is_string(self::$configuration_options['egLogFormat'])) {
			switch( self::$configuration_options['egLogFormat'] ) {
				case 'LOG' :
					self::$configuration_options['egLogFormat'] = eGlooLogger::LOG_LOG;
					break;
				case 'HTML' :
					self::$configuration_options['egLogFormat'] = eGlooLogger::LOG_HTML;
					break;
				case 'XML' :
					self::$configuration_options['egLogFormat'] = eGlooLogger::LOG_XML;
					break;
				default:
					self::$configuration_options['egLogFormat'] = eGlooLogger::LOG_LOG;
					break;
			}
		}

		return self::$configuration_options['egLogFormat'];
	}

    public static function getLoggingPath() {
		return self::$configuration_options['LoggingPath'];
	}
	
	public static function getLoggingLevel() {
		if ( !isset(self::$configuration_options['egLogLevel']) ) {
			self::$configuration_options['egLogLevel'] = eGlooLogger::DEVELOPMENT;
			self::writeFrameworkConfigurationCache();
		} else if (is_string(self::$configuration_options['egLogLevel'])) {
			switch( self::$configuration_options['egLogLevel'] ) {
				case 'LOG_OFF' : 
					self::$configuration_options['egLogLevel'] = eGlooLogger::LOG_OFF;
					break;
				case 'PRODUCTION' : 
					self::$configuration_options['egLogLevel'] = eGlooLogger::PRODUCTION;
					break;
				case 'STAGING' : 
					self::$configuration_options['egLogLevel'] = eGlooLogger::STAGING;
					break;
				case 'DEVELOPMENT' : 
					self::$configuration_options['egLogLevel'] = eGlooLogger::DEVELOPMENT;
					break;
				default : 
					self::$configuration_options['egLogLevel'] = eGlooLogger::DEVELOPMENT;
					break;
			}

			// TODO see if we should move this into an "update cache" method
			self::writeFrameworkConfigurationCache();

			if (eGlooConfiguration::getUseRuntimeCache()) {
				self::writeRuntimeCache();
			}
		}

		return self::$configuration_options['egLogLevel'];
	}

	public static function getPerformSanityCheckClassLoading() {
		return self::$configuration_options['egSanityCheckClassLoading'];
	}

	public static function getRewriteBase() {
		return self::$rewriteBase;
	}

	public static function getSmartyIncludePath() {
		return self::$configuration_options['SmartyPath'];
	}

	public static function getUniqueInstanceIdentifier() {
		return self::$uniqueInstanceID;
	}

	public static function getUseCache() {
		return self::$configuration_options['egCacheEnabled'];
	}

	public static function getUseFileCache() {
		return self::$configuration_options['egFileCacheEnabled'];
	}

	public static function getUseMemcache() {
		return self::$configuration_options['egMemcacheCacheEnabled'];
	}

	public static function getUseDoctrine() {
		return isset(self::$configuration_options['egUseDoctrine']) ? self::$configuration_options['egUseDoctrine'] : false;
	}

	public static function getUseRuntimeCache() {
		$retVal = false;

		if (isset($_SERVER['EG_CACHE_RUNTIME'])) {
			switch( $_SERVER['EG_CACHE_RUNTIME'] ) {
				case 'ON' :
					$retVal = true;
					break;
				case 'OFF' :
				default :
					$retVal = false;
					break;
			}
		}

		return $retVal;
	}

	public static function getUseSmarty() {
		return isset(self::$configuration_options['egUseSmarty']) ? self::$configuration_options['egUseSmarty'] : false;
	}
	
	public static function getWebRoot() {
		if (self::$web_root === null) {
			$matches = array();
			preg_match('~^(.*)?(index.php)$~', $_SERVER['SCRIPT_FILENAME'], $matches);
			self::$web_root = $matches[1];
		}

		return self::$web_root;
	}

	public static function getUserAgentHash() {
		if (self::$userAgentHash === null) {
			self::$userAgentHash = hash('sha256', $_SERVER['HTTP_USER_AGENT']);
		}

		return self::$userAgentHash;
	}

	public static function issetCustomVariable( $index ) {
		$retVal = false;
		
		if (isset(self::$configuration_options['CustomVariables'][$index])) {
			$retVal = true;
		}

		return $retVal;
	}

}