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

	private static $configuration_possible_options = array(
			'ApplicationsPath'			=> array( 'value' => '', 'elementType' => 'Component', 'id' => 'ApplicationsPath',
												  'displayName' => 'Applications Path', 'override' => 'false', 'type' => 'path', 'required' => 'true'),

			'CachePath' 				=> array( 'value' => '', 'elementType' => 'Component', 'id' => 'CachePath',
												  'displayName' => 'Cache Path', 'override' => 'false', 'type' => 'path', 'required' => 'true'),

			'CompiledTemplatesPath'		=> array( 'value' => '', 'elementType' => 'Component', 'id' => 'CompiledTemplatesPath',
												  'displayName' => 'Compiled Templates Path', 'override' => 'false', 'type' => 'path', 'required' => 'true'),

			'ConfigurationPath'			=> array( 'value' => '', 'elementType' => 'Component', 'id' => 'ConfigurationPath',
												  'displayName' => 'Configuration Path', 'override' => 'false', 'type' => 'path', 'required' => 'true'),

			'CubesPath'					=> array( 'value' => '', 'elementType' => 'Component', 'id' => 'CubesPath',
												  'displayName' => 'Cubes Path', 'override' => 'false', 'type' => 'path', 'required' => 'true'),

			'DoctrinePath'				=> array( 'value' => '', 'elementType' => 'Component', 'id' => 'DoctrinePath',
												  'displayName' => 'Doctrine Path', 'override' => 'false', 'type' => 'path', 'required' => 'true'),

			'DocumentationPath'			=> array( 'value' => '', 'elementType' => 'Component', 'id' => 'DocumentationPath',
												  'displayName' => 'Documentation Path', 'override' => 'false', 'type' => 'path', 'required' => 'true'),

			'DocumentRoot'				=> array( 'value' => '', 'elementType' => 'Component', 'id' => 'DocumentRoot',
												  'displayName' => 'Document Root', 'override' => 'false', 'type' => 'path', 'required' => 'true'),

			'FrameworkRootPath'			=> array( 'value' => '', 'elementType' => 'Component', 'id' => 'FrameworkRootPath',
												  'displayName' => 'Framework Root Path', 'override' => 'false', 'type' => 'path', 'required' => 'true'),

			'LoggingPath'				=> array( 'value' => '', 'elementType' => 'Component', 'id' => 'LoggingPath',
												  'displayName' => 'Logging Path', 'override' => 'false', 'type' => 'path', 'required' => 'true'),

			'SmartyPath'				=> array( 'value' => '', 'elementType' => 'Component', 'id' => 'SmartyPath',
												  'displayName' => 'Smarty Path', 'override' => 'false', 'type' => 'path', 'required' => 'true'),

			'egApplication'				=> array( 'value' => 'eGloo/Default.gloo', 'elementType' => 'Option', 'id' => 'egApplication',
												  'displayName' => 'Default Application', 'override' => 'true', 'type' => 'enum', 'required' => 'true'),

			'egInterfaceBundle'			=> array( 'value' => 'OverlayInterface', 'elementType' => 'Option', 'id' => 'egInterfaceBundle',
												  'displayName' => 'Default Interface Bundle', 'override' => 'true', 'type' => 'enum', 'required' => 'true'),

			'egAPCCacheEnabled'			=> array( 'value' => true, 'elementType' => 'Option', 'id' => '',
												  'displayName' => '', 'override' => '', 'type' => '', 'required' => ''),

			'egCacheEnabled'			=> array( 'value' => true, 'elementType' => 'Option', 'id' => '',
												  'displayName' => '', 'override' => '', 'type' => '', 'required' => ''),

			'egDatabaseEngine'			=> array( 'value' => 'POSTGRESQL', 'elementType' => 'Option', 'id' => 'egDatabaseEngine',
												  'displayName' => 'eGloo Database Engine', 'override' => 'true', 'type' => 'enum', 'required' => 'true'),

			'egDisplayErrors'			=> array( 'value' => false, 'elementType' => 'Option', 'id' => '',
												  'displayName' => '', 'override' => '', 'type' => '', 'required' => ''),

			'egDisplayTraces'			=> array( 'value' => false, 'elementType' => 'Option', 'id' => '',
												  'displayName' => '', 'override' => '', 'type' => '', 'required' => ''),

			'egEnvironment'				=> array( 'value' => '', 'elementType' => 'Option', 'id' => '',
												  'displayName' => '', 'override' => '', 'type' => '', 'required' => ''),

			'egFileCacheEnabled'		=> array( 'value' => false, 'elementType' => 'Option', 'id' => '',
												  'displayName' => '', 'override' => '', 'type' => '', 'required' => ''),

			'egMemcacheCacheEnabled'	=> array( 'value' => false, 'elementType' => 'Option', 'id' => '',
												  'displayName' => '', 'override' => '', 'type' => '', 'required' => ''),

			'egSanityCheckClassLoading'	=> array( 'value' => false, 'elementType' => 'Option', 'id' => '',
												  'displayName' => '', 'override' => '', 'type' => '', 'required' => ''),

			'egUseDoctrine'				=> array( 'value' => false, 'elementType' => 'Option', 'id' => '',
												  'displayName' => '', 'override' => '', 'type' => '', 'required' => ''),

			'egUsePostgreSQL'			=> array( 'value' => true, 'elementType' => 'Option', 'id' => '',
												  'displayName' => '', 'override' => '', 'type' => '', 'required' => ''),

			'egUseSmarty'				=> array( 'value' => true, 'elementType' => 'Option', 'id' => '',
												  'displayName' => '', 'override' => '', 'type' => '', 'required' => ''),

			);

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

	public static function loadFrameworkSystemCache( $overwrite = true, $config_cache_path = './SystemCache.php' ) {
		$retVal = false;

		$config_cache_path = self::getFrameworkConfigurationCachePath() . self::getFrameworkConfigurationCacheFilename();

		if ( file_exists($config_cache_path) && is_file($config_cache_path) && is_readable($config_cache_path) ) {
			self::$configuration_options = eval( 'return ' . file_get_contents($config_cache_path) .';' );

			// Grab our environment variables to determine which application and deployment to run

			foreach (self::$configuration_possible_options as $possible_option_key => $option_default_value) {
				if (!isset(self::$configuration_options[$possible_option_key])) {
					self::$configuration_options[$possible_option_key] = $option_default_value['value'];
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

			// TODO Error handling
			// $errors = libxml_get_errors();
			// echo_r($errors);

			$system_configuration = array();

			foreach( $configXMLObject->xpath( '/tns:Configuration' ) as $configuration ) {
				foreach($configuration->children() as $section) {
					$sectionID = (string) $section->getName();
					$system_configuration[$sectionID] = array('Components' => array(), 'Options' => array());

					foreach($section->xpath( 'Component' ) as $component) {
						$nextComponent = array();

						$nextComponent['id'] = (string) $component['id'];
						$nextComponent['displayName'] = (string) $component['displayName'];
						$nextComponent['override'] = (string) $component['override'];
						$nextComponent['type'] = (string) $component['type'];
						$nextComponent['value'] = (string) $component['value'];
						$nextComponent['required'] = (string) $component['required'];

						$system_configuration[$sectionID]['Components'][] = $nextComponent;
					}

					foreach($section->xpath( 'Option' ) as $option) {
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

			echo_r($system_configuration);
die;

		} else {
			trigger_error("System XML for eGloo Framework not found");
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
					self::$configuration_options[$possible_option_key] = $option_default_value['value'];
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

    public static function getFrameworkRootPath() {
		return self::$configuration_options['FrameworkRootPath'];
	}

    public static function getLogFormat() {
		if ( !isset(self::$configuration_options['egLogFormat']) ) {
			switch( $_SERVER['EG_LOG_FORMAT'] ) {
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
			switch( $_SERVER['EG_LOG_LEVEL'] ) {
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
		}

		return self::$configuration_options['egLogLevel'];
	}

	public static function getPerformSanityCheckClassLoading() {
		return self::$configuration_options['egSanityCheckClassLoading'];
	}

	public static function getSmartyIncludePath() {
		return self::$configuration_options['SmartyPath'];
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
		return self::$configuration_options['egUseDoctrine'];
	}

	public static function getUseSmarty() {
		return self::$configuration_options['egUseSmarty'];
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