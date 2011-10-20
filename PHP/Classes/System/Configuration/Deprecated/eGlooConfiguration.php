<?php
/**
 * eGlooConfiguration Class File
 *
 * Contains the class definition for the eGlooConfiguration
 * 
 * Copyright 2011 eGloo LLC
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
 * @copyright 2011 eGloo LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @category System
 * @package Configuration
 * @version 1.0
 */

/**
 * eGlooConfiguration
 *
 * $short_description
 *
 * $long_description
 *
 * @category System
 * @package Configuration
 */
final class eGlooConfiguration {

	/* Class Constants */

	// Deployment Type
	const DEVELOPMENT	= 0xff;
	const STAGING		= 0xfe;
	const PRODUCTION	= 0xf8;

	// CDN Providers
	const AKAMAI		= 0x00;
	const CLOUDFRONT	= 0x01;

	// Database Engines
	const AQUINAS		= 0x00;
	const CASSANDRA		= 0x01;
	const DOCTRINE		= 0x02;
	const EGLOO			= 0x03;
	const MONGO			= 0x04;
	const MYSQL			= 0x05;
	const MYSQLI		= 0x06;
	const MYSQLIOOP		= 0x07;
	const ORACLE		= 0x08;
	const PDO			= 0x09;
	const POSTGRESQL	= 0x0a;
	const REST			= 0x0b;
	const SOAP			= 0x0c;

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
	private static $_runtimeConfigurationCacheFilename = null;

	public static function loadCLIConfigurationOptions( $overwrite = true, $prefer_htaccess = true, $config_xml = './Config.xml', $config_cache = null ) {
		$useRuntimeCache = self::getUseRuntimeCache();

		$supported_operating_systems = array( 'Mac OS X' => array('Darwin'), 'Ubuntu' => array('Linux') );

		if ( in_array( PHP_OS, $supported_operating_systems['Mac OS X'] ) ) {
			$config_xml_path = '/Library/Application Support/eGloo/Framework/Configuration/Config.xml';
			$system_xml_path = '/Library/Application Support/eGloo/Framework/Configuration/System.xml';
		} else if ( in_array( PHP_OS, $supported_operating_systems['Ubuntu'] ) ) {
			$config_xml_path = '/etc/egloo/Config.xml';
			$system_xml_path = '/etc/egloo/System.xml';
		} else {
			echo 'eGloo CLI OS does not support this operating system.  Looking for eGloo configuration in Linux default...' . "\n";
			$config_xml_path = '/etc/egloo/Config.xml';
			$system_xml_path = '/etc/egloo/System.xml';
		}

		$matches = array();

		if ( preg_match_all( '~ *([^/]+)[/]?~', getcwd(), $matches ) !== 0 ) {
			$cwd_chunks = $matches[1];
			$cwd_chunks_reversed = array_reverse($cwd_chunks);

			$application_path = null;
			$application_xml_path = null;
			$found_application_path = false;

			foreach( $cwd_chunks_reversed as $cwd_chunk ) {
				if ( strpos( $cwd_chunk, '.gloo' ) !== false ) {
					$found_application_path = true;
					break;
				} else {
					$application_path .= '../';
				}
			}

			if ( $found_application_path ) {
				$application_path = './' . $application_path;
				$application_xml_path = $application_path . 'Configuration/Config.xml';
			}
		}

		// TODO move this somewhere cleaner
		self::$configuration_options['egCDNConnections'] = array();
		self::$configuration_options['egDatabaseConnections'] = array();

		// if ( ($useRuntimeCache && !self::loadRuntimeCache()) || !$useRuntimeCache ) {
			// $success = self::loadFrameworkSystemCache();
			$success = false;

			if ( !$success ) {
				self::loadFrameworkSystemXML( $system_xml_path, true );
			}

			// $success = self::loadFrameworkConfigurationCache( $overwrite, $config_cache );

			if ( !$success ) {
				self::loadFrameworkConfigurationXML( $overwrite, $config_xml_path, true );
			}

			// $application_path = self::getApplicationPath();

			// $success = self::loadApplicationConfigurationCache( $application_path, $overwrite, $config_cache );
		
			if ( !$success && $found_application_path ) {
				self::loadApplicationConfigurationXML( $application_path, $overwrite );
			}

			// HACK Loading the XML will overwrite this, so add it back
			if ( $found_application_path ) {
				self::setApplicationPath( $application_path );
			}

			if ( $prefer_htaccess ) {
				// self::loadWebRootConfig( $overwrite );
			}

			if ( self::getUseRuntimeCache() ) {
				self::writeRuntimeCache();
			}
		// }

		// TODO Set the rewrite base the correct way
		self::$rewriteBase = '/';

		self::$uniqueInstanceID = md5(realpath('.') . self::getApplicationPath() . self::getUIBundleName());
	}

	public static function loadConfigurationOptions( $overwrite = true, $prefer_htaccess = true, $config_xml = './Config.xml', $config_cache = null ) {
		$useRuntimeCache = self::getUseRuntimeCache();

		// TODO move this somewhere cleaner
		self::$configuration_options['egCDNConnections'] = array();
		self::$configuration_options['egDatabaseConnections'] = array();

		if ( ($useRuntimeCache && !self::loadRuntimeCacheClass()) || !$useRuntimeCache ) {
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

			$application_path = self::getApplicationPath();

			$success = self::loadApplicationConfigurationCache($application_path, $overwrite, $config_cache);

			if (!$success) {
				self::loadApplicationConfigurationXML($application_path, $overwrite);
				self::writeApplicationConfigurationCache($application_path);
			}

			if ($prefer_htaccess) {
				self::loadWebRootConfig($overwrite);
			}

			if (self::getUseRuntimeCache()) {
				self::writeRuntimeCacheClass();
			}
		}

		// Set the rewrite base
		if ($_SERVER['SCRIPT_NAME'] !== '/index.php') {
			$matches = array();
			preg_match('~^(.*)?(index.php)$~', $_SERVER['SCRIPT_NAME'], $matches);
			self::$rewriteBase = $matches[1];
		}

		self::$uniqueInstanceID = md5(realpath('.') . self::getApplicationPath() . self::getUIBundleName());
		
		if ( isset( $_SERVER['EG_SECURE_ENVIRONMENT'] ) && $_SERVER['EG_SECURE_ENVIRONMENT'] === 'ON' ) {
			self::secureEnvironment();
		}
	}

	public static function loadWebRootConfig( $overwrite = true ) {
		$webRootConfigOptions = array();
		$webRootConfigOptions['egApplication']		= $_SERVER['EG_APP'];
		$webRootConfigOptions['egApplicationName']		= preg_replace('~([a-zA-Z0-9/ ]*/)?([a-zA-Z0-9 ]*?)\.gloo~', '$2', $_SERVER['EG_APP']);
		$webRootConfigOptions['egInterfaceBundle']		= $_SERVER['EG_UI'];

		if ( isset($_SERVER['EG_CACHE']) ) {
			switch( $_SERVER['EG_CACHE'] ) {
				case 'ON' :
					$webRootConfigOptions['egCacheEnabled'] = true;
					break;
				case 'OFF' :
				default :
					$webRootConfigOptions['egCacheEnabled'] = false;
					break;
			}
		} else if ( !isset(self::$configuration_options['egCacheEnabled']) ) {
			self::$configuration_options['egCacheEnabled'] = true;
		}

		if ( isset($_SERVER['EG_CACHE_APC']) ) {
			switch( $_SERVER['EG_CACHE_APC'] ) {
				case 'ON' :
					$webRootConfigOptions['egAPCCacheEnabled'] = true;
					break;
				case 'OFF' :
				default :
					$webRootConfigOptions['egAPCCacheEnabled'] = false;
					break;
			}
		} else if ( !isset(self::$configuration_options['egAPCCacheEnabled']) ) {
			self::$configuration_options['egAPCCacheEnabled'] = false;
		}

		if ( isset($_SERVER['EG_CACHE_CDN']) ) {
			switch( $_SERVER['EG_CACHE_CDN'] ) {
				case 'ON' :
					$webRootConfigOptions['egCDNEnabled'] = true;
					break;
				case 'OFF' :
				default :
					$webRootConfigOptions['egCDNEnabled'] = false;
					break;
			}
		} else if ( !isset(self::$configuration_options['egCDNEnabled']) ) {
			self::$configuration_options['egCDNEnabled'] = false;
		}

		if ( isset($_SERVER['EG_CACHE_FILE']) ) {
			switch( $_SERVER['EG_CACHE_FILE'] ) {
				case 'ON' :
					$webRootConfigOptions['egFileCacheEnabled'] = true;
					break;
				case 'OFF' :
				default :
					$webRootConfigOptions['egFileCacheEnabled'] = false;
					break;
			}
		} else if ( !isset(self::$configuration_options['egFileCacheEnabled']) ) {
			self::$configuration_options['egFileCacheEnabled'] = false;
		}

		if ( isset($_SERVER['EG_CACHE_MEMCACHE']) ) {
			switch( $_SERVER['EG_CACHE_MEMCACHE'] ) {
				case 'ON' :
					$webRootConfigOptions['egMemcacheCacheEnabled'] = true;
					break;
				case 'OFF' :
				default :
					$webRootConfigOptions['egMemcacheCacheEnabled'] = false;
					break;
			}
		} else if ( !isset(self::$configuration_options['egMemcacheCacheEnabled']) ) {
			self::$configuration_options['egMemcacheCacheEnabled'] = true;
		}

		// Primary CDN info, if any
		if ( isset($_SERVER['EG_CDN_CONNECTION_PRIMARY_NAME']) ) {
			self::$configuration_options['egCDNConnections']['egCDNPrimary'] = array();

			self::$configuration_options['egCDNConnections']['egCDNPrimary']['name']				= $_SERVER['EG_CDN_CONNECTION_PRIMARY_NAME'];
			self::$configuration_options['egCDNConnections']['egCDNPrimary']['bucket']				= $_SERVER['EG_CDN_CONNECTION_PRIMARY_BUCKET'];
			self::$configuration_options['egCDNConnections']['egCDNPrimary']['distribution_url']	= $_SERVER['EG_CDN_CONNECTION_PRIMARY_DISTRIBUTION_URL'];
			self::$configuration_options['egCDNConnections']['egCDNPrimary']['access_key_id']		= $_SERVER['EG_CDN_CONNECTION_PRIMARY_ACCESS_KEY_ID'];
			self::$configuration_options['egCDNConnections']['egCDNPrimary']['secret_access_key']	= $_SERVER['EG_CDN_CONNECTION_PRIMARY_SECRET_ACCESS_KEY'];

			// Determine which CDN system we're using
			switch( $_SERVER['EG_CDN_CONNECTION_PRIMARY_PROVIDER'] ) {
				case 'AKAMAI' :
					self::$configuration_options['egCDNConnections']['egCDNPrimary']['provider'] = self::AKAMAI;
					break;
				case 'CLOUDFRONT' :
					self::$configuration_options['egCDNConnections']['egCDNPrimary']['provider'] = self::CLOUDFRONT;
					break;
				default:
					self::$configuration_options['egCDNConnections']['egCDNPrimary']['provider'] = self::CLOUDFRONT;
					break;
			}
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
				case 'MYSQLI' :
					self::$configuration_options['egDatabaseConnections']['egPrimary']['engine'] = self::MYSQLI;
					break;
				case 'MYSQLIOOP' :
					self::$configuration_options['egDatabaseConnections']['egPrimary']['engine'] = self::MYSQLIOOP;
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

		if ( isset($_SERVER['EG_HOTFILE_CSS_CLUSTERING_ENABLED']) ) {
			switch( $_SERVER['EG_HOTFILE_CSS_CLUSTERING_ENABLED'] ) {
				case 'ON' :
					$webRootConfigOptions['egHotFileCSSClusteringEnabled'] = true;
					break;
				case 'OFF' :
				default :
					$webRootConfigOptions['egHotFileCSSClusteringEnabled'] = false;
					break;
			}
		} else {
			$webRootConfigOptions['egHotFileCSSClusteringEnabled'] = false;
		}

		if ( isset($_SERVER['EG_HOTFILE_IMAGE_CLUSTERING_ENABLED']) ) {
			switch( $_SERVER['EG_HOTFILE_IMAGE_CLUSTERING_ENABLED'] ) {
				case 'ON' :
					$webRootConfigOptions['egHotFileImageClusteringEnabled'] = true;
					break;
				case 'OFF' :
				default :
					$webRootConfigOptions['egHotFileImageClusteringEnabled'] = false;
					break;
			}
		} else {
			$webRootConfigOptions['egHotFileImageClusteringEnabled'] = false;
		}

		if ( isset($_SERVER['EG_HOTFILE_JAVASCRIPT_CLUSTERING_ENABLED']) ) {
			switch( $_SERVER['EG_HOTFILE_JAVASCRIPT_CLUSTERING_ENABLED'] ) {
				case 'ON' :
					$webRootConfigOptions['egHotFileJavascriptClusteringEnabled'] = true;
					break;
				case 'OFF' :
				default :
					$webRootConfigOptions['egHotFileJavascriptClusteringEnabled'] = false;
					break;
			}
		} else {
			$webRootConfigOptions['egHotFileJavascriptClusteringEnabled'] = false;
		}

		if ( isset($_SERVER['EG_HOTFILE_MEDIA_CLUSTERING_ENABLED']) ) {
			switch( $_SERVER['EG_HOTFILE_MEDIA_CLUSTERING_ENABLED'] ) {
				case 'ON' :
					$webRootConfigOptions['egHotFileMediaClusteringEnabled'] = true;
					break;
				case 'OFF' :
				default :
					$webRootConfigOptions['egHotFileMediaClusteringEnabled'] = false;
					break;
			}
		} else {
			$webRootConfigOptions['egHotFileMediaClusteringEnabled'] = false;
		}

		if ( isset($_SERVER['EG_ENABLE_DEFAULT_REQUEST_CLASS']) ) {
			switch( $_SERVER['EG_ENABLE_DEFAULT_REQUEST_CLASS'] ) {
				case 'ON' :
					$webRootConfigOptions['egEnableDefaultRequestClass'] = true;
					break;
				case 'OFF' :
				default :
					$webRootConfigOptions['egEnableDefaultRequestClass'] = false;
					break;
			}
		} else if ( $webRootConfigOptions['egEnvironment'] === self::DEVELOPMENT ) {
			$webRootConfigOptions['egEnableDefaultRequestClass'] = false;
		} else {
			$webRootConfigOptions['egEnableDefaultRequestClass'] = true;
		}

		if ( isset($_SERVER['EG_ENABLE_DEFAULT_REQUEST_ID']) ) {
			switch( $_SERVER['EG_ENABLE_DEFAULT_REQUEST_ID'] ) {
				case 'ON' :
					$webRootConfigOptions['egEnableDefaultRequestID'] = true;
					break;
				case 'OFF' :
				default :
					$webRootConfigOptions['egEnableDefaultRequestID'] = false;
					break;
			}
		} else if ( $webRootConfigOptions['egEnvironment'] === self::DEVELOPMENT ) {
			$webRootConfigOptions['egEnableDefaultRequestID'] = false;
		} else {
			$webRootConfigOptions['egEnableDefaultRequestID'] = true;
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
		if (!isset(self::$_runtimeConfigurationCacheFilename)) {
			self::$_runtimeConfigurationCacheFilename = md5(realpath('.')) . '.runtime.gloocache';
		}

		return self::$_runtimeConfigurationCacheFilename;
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
		$config_dump = var_export(self::$configuration_options, true);
		file_put_contents($runtime_cache_path, $config_dump);
	}

	public static function loadRuntimeCacheClass() {
		$retVal = false;

		$runtime_cache_path = self::getRuntimeConfigurationCachePath() . self::getRuntimeConfigurationCacheFilename();

		if ( file_exists($runtime_cache_path . '.class.php') && is_file($runtime_cache_path . '.class.php') && is_readable($runtime_cache_path . '.class.php') ) {
			include( $runtime_cache_path . '.class.php' );
			self::$configuration_options = &eGlooRuntimeCacheClass::$configuration_options;

			$retVal = true;
		}

		return $retVal;
	}

	public static function writeRuntimeCacheClass( $runtime_cache_path = null ) {
		if (!$runtime_cache_path) {
			if ( !is_writable( self::getRuntimeConfigurationCachePath() ) ) {
				$old_umask = umask(0);
				mkdir( self::getRuntimeConfigurationCachePath(), 0775 );
				umask($old_umask);
			}

			$runtime_cache_path = self::getRuntimeConfigurationCachePath() . self::getRuntimeConfigurationCacheFilename();
		}

		$class_definition = '<?php final class eGlooRuntimeCacheClass {' .
			'public static $configuration_options = ' . getArrayDefinitionString( self::$configuration_options ) . ';}';

		file_put_contents( $runtime_cache_path . '.class.php', $class_definition );

		// Dump our configuration set
		$config_dump = var_export(self::$configuration_options, true);
		file_put_contents($runtime_cache_path, $config_dump);
	}

	public static function clearRuntimeCache( $runtime_cache_path = null ) {
		$retVal = false;

		$systemInfoBean = SystemInfoBean::getInstance();
		$systemInfoBean->appendValue( 'SystemActions', 'Preparing to delete runtime cache' );

		if (!$runtime_cache_path) {
			if ( !is_writable( self::getRuntimeConfigurationCachePath() ) ) {
				// TODO figure out what to do here
				$retVal = false;
			}

			$runtime_cache_path = self::getRuntimeConfigurationCachePath() . self::getRuntimeConfigurationCacheFilename();
		}

		$systemInfoBean->appendValue( 'SystemActions', 'Runtime cache path is ' . $runtime_cache_path );

		if ( file_exists($runtime_cache_path) && is_file($runtime_cache_path) && is_writable($runtime_cache_path) ) {
			$systemInfoBean->appendValue( 'SystemActions', 'Deleting runtime cache at ' . $runtime_cache_path );
			$retVal = unlink($runtime_cache_path);
		} else {
			$systemInfoBean->appendValue( 'SystemActions', 'Runtime cache not found at ' . $runtime_cache_path );
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

	public static function loadApplicationConfigurationXML( $application_name, $overwrite = true, $config_xml_filename = 'Config.xml', $load_cli_config = false ) {
		if ( self::getApplicationPath() !== $application_name ) {
			$config_xml_path = $application_name . 'Configuration/' . $config_xml_filename;
		} else {
			$config_xml_path = self::getApplicationsPath() . '/' . self::getApplicationPath() . '/Configuration/' . $config_xml_filename;
		}

		if ( file_exists($config_xml_path) && is_file($config_xml_path) && is_readable($config_xml_path) ) {
			$configXMLObject = simplexml_load_file( $config_xml_path );

			// TODO Error handling
			// $errors = libxml_get_errors();
			// echo_r($errors);

			// Load applications after system... 
			foreach( $configXMLObject->xpath( '/tns:Configuration/tns:Applications/tns:Component' ) as $component ) {
				$componentID = (string) $component['id'];

				if (isset(self::$configuration_possible_options[$componentID])) {
					// For backwards compatibility until more testing done
					if ( !isset(self::$configuration_options[$componentID]) || $overwrite ) {
						self::$configuration_options[$componentID] = (string) $component['value'];
					}
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

			if (isset(self::$configuration_options['AppBuild'])) {
				$build_file_path = self::getApplicationsPath() . '/' .
					self::getApplicationPath() . '/' . self::$configuration_options['AppBuild'];

				if ( file_exists($build_file_path) && is_file($build_file_path) && is_readable($build_file_path) ) {
					self::$configuration_options['AppBuild'] = file_get_contents($build_file_path);
				} else {
					self::$configuration_options['AppBuild'] = 'No application build provided';
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
		$config_dump = var_export(self::$configuration_options, true);
		file_put_contents($config_cache_path, $config_dump);
	}

	public static function writeApplicationConfigurationXML( $application_name, $config_xml_path = 'Config.xml' ) {
		
	}

	public static function clearApplicationCache( $application_cache_path = null ) {
		$retVal = false;

		$systemInfoBean = SystemInfoBean::getInstance();

		$systemInfoBean->appendValue( 'SystemActions', 'Preparing to delete application cache' );

		if (!$application_cache_path) {
			if ( !is_writable( self::getApplicationConfigurationCachePath() ) ) {
				// TODO figure out what to do here
				$retVal = false;
			}

			$application_cache_path = self::getApplicationConfigurationCachePath() . self::getApplicationConfigurationCacheFilename();
		}

		$systemInfoBean->appendValue( 'SystemActions', 'Application cache path is ' . $application_cache_path );

		if ( file_exists($application_cache_path) && is_file($application_cache_path) && is_writable($application_cache_path) ) {
			$systemInfoBean->appendValue( 'SystemActions', 'Deleting application cache at ' . $application_cache_path );
			$retVal = unlink($application_cache_path);
		} else {
			$systemInfoBean->appendValue( 'SystemActions', 'Application cache not found at ' . $application_cache_path );
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
			$cached_options_array_string = file_get_contents($config_cache_path);
			
			if ($cached_options_array_string) {
				$cached_options = eval( 'return ' . file_get_contents($config_cache_path) .';' );

				if (isset($cached_options) && is_array($cached_options)) {
					// Grab our environment variables to determine which application and deployment to run
					foreach ($cached_options as $possible_option_key => $option_default_value) {
						if (isset(self::$configuration_possible_options[$possible_option_key])) {
							self::$configuration_options[$possible_option_key] = $option_default_value;
						}
					}

					// No errors
					$retVal = true;
				} else {
					// We don't have eGlooLogger access at this point
					// eGlooLogger::writeLog( eGlooLogger::EMERGENCY, 'Cached options read from ' . $config_cache_path . ' were invalid.  Content: ' .
					// "\n\n" . $cached_options);
				}
			} else {
				// We don't have eGlooLogger access at this point
				// eGlooLogger::writeLog( eGlooLogger::WARN, 'Attempting to read ' . $config_cache_path . ' returned false');
			}
		}
		// We don't have eGlooLogger access at this point
		//	else {
		//	eGlooLogger::writeLog( eGlooLogger::NOTICE, 'Could not read ' . $config_cache_path);
		// }

		return $retVal;
	}

	public static function loadFrameworkConfigurationXML( $overwrite = true, $config_xml_path = './Config.xml', $load_cli_config = false ) {
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

			if ( !isset(self::$configuration_options['Alerts']) ) {
				self::$configuration_options['Alerts'] = array( 'Alerts' => array() );
			} else if ( !isset(self::$configuration_options['Alerts']['Alerts']) ) {
				self::$configuration_options['Alerts']['Alerts'] = array();
			}

			foreach( $configXMLObject->xpath( '/tns:Configuration/tns:Alerts/tns:Alert' ) as $alert ) {
				$alert_id = (string) $alert['id'];
				$alert_active = (string) $alert['active'];
				$alert_type = (string) $alert['type'];
				$alert_override = (string) $alert['override'];
				$alert_trigger = (string) $alert['trigger'];
				$alert_value = (string) $alert['value'];

				if ( isset(self::$configuration_possible_options['Alerts']['Alerts'][$alert_id]) ) {
					self::$configuration_options['Alerts']['Alerts'][$alert_id] = array( 'id' => $alert_id, 'type' => $alert_type, 'trigger' => $alert_trigger,
						'value' => $alert_value, 'active' => $alert_active );
				} else if ( !isset(self::$configuration_options['Alerts']['Alerts'][$alert_id]) ) {
					self::$configuration_options['Alerts']['Alerts'][$alert_id] = array( 'id' => $alert_id, 'type' => $alert_type, 'trigger' => $alert_trigger,
						'value' => $alert_value, 'active' => $alert_active );
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
		$config_dump = var_export(self::$configuration_options, true);
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
			$xmlData .= 'xsi:schemaLocation="com.egloo.www/eGlooConfiguration ../XML/Schemas/eGlooConfiguration.xsd">';
			
			$xmlData .= '</tns:Configuration>';

			$xmlObject = new SimpleXMLElement($xmlData);

			$applicationsXMLObject	= $xmlObject->addChild('Applications');
			$cachingXMLObject		= $xmlObject->addChild('Caching');
			$cubesXMLObject			= $xmlObject->addChild('Cubes');
			$databasesXMLObject		= $xmlObject->addChild('Databases');
			$documentsXMLObject		= $xmlObject->addChild('Documents');
			$frameworkXMLObject		= $xmlObject->addChild('Framework');
			$libraryXMLObject		= $xmlObject->addChild('Library');
			$networkXMLObject		= $xmlObject->addChild('Network');
			$peeringXMLObject		= $xmlObject->addChild('Peering');
			$systemXMLObject		= $xmlObject->addChild('System');
			
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
	}

	public static function clearFrameworkCache( $config_cache_path = null ) {
		$retVal = false;

		$systemInfoBean = SystemInfoBean::getInstance();
		$systemInfoBean->appendValue( 'SystemActions', 'Preparing to delete framework cache' );

		if (!$config_cache_path) {
			if ( !is_writable( self::getFrameworkConfigurationCachePath() ) ) {
				// TODO figure out what to do here
				$retVal = false;
			}

			$config_cache_path = self::getFrameworkConfigurationCachePath() . self::getFrameworkConfigurationCacheFilename();
		}

		$systemInfoBean->appendValue( 'SystemActions', 'Framework cache path is ' . $config_cache_path );

		if ( file_exists($config_cache_path) && is_file($config_cache_path) && is_writable($config_cache_path) ) {
			$systemInfoBean->appendValue( 'SystemActions', 'Deleting framework cache at ' . $config_cache_path );
			$retVal = unlink($config_cache_path);
		} else {
			$systemInfoBean->appendValue( 'SystemActions', 'Framework cache not found at ' . $config_cache_path );
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

	public static function loadFrameworkSystemXML( $system_xml_path = './System.xml', $load_cli_config = false ) {
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

				if ( !isset($system_configuration['Alerts']) ) {
					$system_configuration['Alerts'] = array( 'Alerts' => array());
				}

				foreach( $configuration->xpath( '/tns:Configuration/tns:Alerts/tns:Alert' ) as $alert ) {
					$alert_id = (string) $alert['id'];
					$alert_active = (string) $alert['active'];
					$alert_type = (string) $alert['type'];
					$alert_override = (string) $alert['override'];
					$alert_trigger = (string) $alert['trigger'];
					$alert_value = (string) $alert['value'];

					$system_configuration['Alerts']['Alerts'][$alert_id] = array( 'id' => $alert_id, 'type' => $alert_type, 'trigger' => $alert_trigger,
						'value' => $alert_value, 'active' => $alert_active, 'override' => $alert_override );
				}
			}


			//////////////////////////////////////// HMMMMMMMMMMM SHOULD THIS BE HERE?	Should be application array set
			if ( isset($system_configuration['Alerts']) && isset($system_configuration['Alerts']['Alerts']) ) {
				foreach($system_configuration['Alerts']['Alerts'] as $alert) {
					if ($alert['override'] === 'true') {
						if ( !isset(self::$configuration_possible_options['Alerts']) ) {
							self::$configuration_possible_options['Alerts'] = array( 'Alerts' => array() );
						} else if ( !isset(self::$configuration_possible_options['Alerts']['Alerts']) ) {
							self::$configuration_possible_options['Alerts'] = array( 'Alerts' => array() );
						}

						self::$configuration_possible_options['Alerts']['Alerts'][$alert['id']] = $alert;
					}

					self::$system_configuration['Alerts']['Alerts'][$alert['id']] = $alert;
					self::$configuration_options['Alerts']['Alerts'][$alert['id']] = array( 'id' => $alert['id'], 'type' => $alert['type'],
						'trigger' => $alert['trigger'], 'value' => $alert['value'], 'active' => $alert['active'] );
				}
			}

			foreach($system_configuration['Applications']['Components'] as $component) {
				if ($component['override'] === 'true') {
					self::$configuration_possible_options[$component['id']] = $component;
				}

				self::$system_configuration[$component['id']] = $component;
				self::$configuration_options[$component['id']] = $component['value'];
			}

			//////////////////////////////////////// HMMMMMMMMMMM SHOULD THIS BE HERE?	Should be application array set
			foreach($system_configuration['Applications']['Options'] as $option) {
				if ($option['override'] === 'true') {
					self::$configuration_possible_options[$option['id']] = $option;
				}

				self::$system_configuration[$option['id']] = $option;
				self::$configuration_options[$option['id']] = $option['value'];
			}

			if ( $load_cli_config ) {
				if ( !isset(self::$configuration_possible_options['CLI']) ) {
					self::$configuration_possible_options['CLI'] = array();
				}

				if ( !isset(self::$system_configuration['CLI']) ) {
					self::$system_configuration['CLI'] = array();
				}

				if ( !isset(self::$configuration_options['CLI']) ) {
					self::$configuration_options['CLI'] = array();
				}

				foreach($system_configuration['CLI']['Components'] as $component) {
					if ($component['override'] === 'true') {
						self::$configuration_possible_options['CLI'][$component['id']] = $component;
					}

					self::$system_configuration['CLI'][$component['id']] = $component;
					self::$configuration_options['CLI'][$component['id']] = $component['value'];
				}

				//////////////////////////////////////// HMMMMMMMMMMM SHOULD THIS BE HERE?	Should be application array set
				foreach($system_configuration['CLI']['Options'] as $option) {
					if ($option['override'] === 'true') {
						self::$configuration_possible_options['CLI'][$option['id']] = $option;
					}

					self::$system_configuration['CLI'][$option['id']] = $option;
					self::$configuration_options['CLI'][$option['id']] = $option['value'];
				}
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
		$system_dump = var_export(self::$system_configuration, true);
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
			$xmlData .= 'xsi:schemaLocation="com.egloo.www/eGlooConfiguration ../XML/Schemas/eGlooConfiguration.xsd">';
			
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

		$systemInfoBean = SystemInfoBean::getInstance();
		$systemInfoBean->appendValue( 'SystemActions', 'Preparing to delete system cache' );

		if (!$system_cache_path) {
			if ( !is_writable( self::getFrameworkSystemCachePath() ) ) {
				// TODO figure out what to do here
				$retVal = false;
			}

			$system_cache_path = self::getFrameworkSystemCachePath() . self::getFrameworkSystemCacheFilename();
		}

		$systemInfoBean->appendValue( 'SystemActions', 'System cache path is ' . $system_cache_path );

		if ( file_exists($system_cache_path) && is_file($system_cache_path) && is_writable($system_cache_path) ) {
			$systemInfoBean->appendValue( 'SystemActions', 'Deleting system cache at ' . $system_cache_path );
			$retVal = unlink($system_cache_path);
		} else {
			$systemInfoBean->appendValue( 'SystemActions', 'System cache not found at ' . $system_cache_path );
			$retVal = true;
		}

		return $retVal;
	}

	public static function secureEnvironment() {
		unset($_SERVER['EG_APP']);
		unset($_SERVER['EG_UI']);

		unset($_SERVER['EG_CACHE']);
		unset($_SERVER['EG_CACHE_APC']);
		unset($_SERVER['EG_CACHE_FILE']);
		unset($_SERVER['EG_CACHE_MEMCACHE']);
		unset($_SERVER['EG_CACHE_RUNTIME']);

		unset($_SERVER['EG_ENV']);

		unset($_SERVER['EG_SANITY_CHECK_CLASS_LOADING']);

		unset($_SERVER['EG_DB_CONNECTION_PRIMARY_NAME']);
		unset($_SERVER['EG_DB_CONNECTION_PRIMARY_HOST']);
		unset($_SERVER['EG_DB_CONNECTION_PRIMARY_PORT']);
		unset($_SERVER['EG_DB_CONNECTION_PRIMARY_DATABASE']);
		unset($_SERVER['EG_DB_CONNECTION_PRIMARY_USER']);
		unset($_SERVER['EG_DB_CONNECTION_PRIMARY_PASSWORD']);
		unset($_SERVER['EG_DB_CONNECTION_PRIMARY_ENGINE']);

		unset($_SERVER['EG_DISPLAY_ERRORS']);
		unset($_SERVER['EG_DISPLAY_TRACES']);

		unset($_SERVER['EG_LOG_LEVEL']);
		unset($_SERVER['EG_LOG_FORMAT']);

		unset($_SERVER['EG_HOTFILE_CSS_CLUSTERING_ENABLED']);
		unset($_SERVER['EG_HOTFILE_IMAGE_CLUSTERING_ENABLED']);
		unset($_SERVER['EG_HOTFILE_JAVASCRIPT_CLUSTERING_ENABLED']);
		unset($_SERVER['EG_HOTFILE_MEDIA_CLUSTERING_ENABLED']);

		unset($_SERVER['EG_ENABLE_DEFAULT_REQUEST_CLASS']);
		unset($_SERVER['EG_ENABLE_DEFAULT_REQUEST_ID']);

		unset($_SERVER['EG_SECURE_ENVIRONMENT']);

		unset($_SERVER['REDIRECT_EG_APP']);
		unset($_SERVER['REDIRECT_EG_UI']);

		unset($_SERVER['REDIRECT_EG_CACHE']);
		unset($_SERVER['REDIRECT_EG_CACHE_APC']);
		unset($_SERVER['REDIRECT_EG_CACHE_FILE']);
		unset($_SERVER['REDIRECT_EG_CACHE_MEMCACHE']);
		unset($_SERVER['REDIRECT_EG_CACHE_RUNTIME']);

		unset($_SERVER['REDIRECT_EG_ENV']);

		unset($_SERVER['REDIRECT_EG_SANITY_CHECK_CLASS_LOADING']);

		unset($_SERVER['REDIRECT_EG_DB_CONNECTION_PRIMARY_NAME']);
		unset($_SERVER['REDIRECT_EG_DB_CONNECTION_PRIMARY_HOST']);
		unset($_SERVER['REDIRECT_EG_DB_CONNECTION_PRIMARY_PORT']);
		unset($_SERVER['REDIRECT_EG_DB_CONNECTION_PRIMARY_DATABASE']);
		unset($_SERVER['REDIRECT_EG_DB_CONNECTION_PRIMARY_USER']);
		unset($_SERVER['REDIRECT_EG_DB_CONNECTION_PRIMARY_PASSWORD']);
		unset($_SERVER['REDIRECT_EG_DB_CONNECTION_PRIMARY_ENGINE']);

		unset($_SERVER['REDIRECT_EG_DISPLAY_ERRORS']);
		unset($_SERVER['REDIRECT_EG_DISPLAY_TRACES']);

		unset($_SERVER['REDIRECT_EG_LOG_LEVEL']);
		unset($_SERVER['REDIRECT_EG_LOG_FORMAT']);

		unset($_SERVER['REDIRECT_EG_HOTFILE_CSS_CLUSTERING_ENABLED']);
		unset($_SERVER['REDIRECT_EG_HOTFILE_IMAGE_CLUSTERING_ENABLED']);
		unset($_SERVER['REDIRECT_EG_HOTFILE_JAVASCRIPT_CLUSTERING_ENABLED']);
		unset($_SERVER['REDIRECT_EG_HOTFILE_MEDIA_CLUSTERING_ENABLED']);

		unset($_SERVER['REDIRECT_EG_ENABLE_DEFAULT_REQUEST_CLASS']);
		unset($_SERVER['REDIRECT_EG_ENABLE_DEFAULT_REQUEST_ID']);

		unset($_SERVER['REDIRECT_EG_SECURE_ENVIRONMENT']);
	}

	// Accessors
	public static function getAlerts() {
		$retVal = array();

		if (isset(self::$configuration_options['Alerts']['Alerts'])) {
			$retVal = self::$configuration_options['Alerts']['Alerts'];
		}

		return $retVal;
	}

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

	public static function getApplicationPath( $absolute_path = false ) {
		$retVal = null;
		
		if ( $absolute_path ) {
			$retVal = self::getApplicationsPath() . '/' . self::$configuration_options['egApplication'];
		} else {
			$retVal = self::$configuration_options['egApplication'];
		}

		return $retVal;
	}

	public static function setApplicationPath( $relative_path ) {
		self::$configuration_options['egApplication'] = $relative_path;
	}

	public static function getApplicationPHPPath( $absolute_path = false ) {
		$retVal = null;

		if ( $absolute_path ) {
			$retVal = self::getApplicationsPath() . '/' . self::$configuration_options['egApplication'] . '/PHP';
		} else {
			$retVal = self::$configuration_options['egApplication'] . '/PHP';
		}

		return $retVal;
	}

	public static function getApplicationTemplatesPath( $absolute_path = false ) {
		$retVal = null;
		
		if ( $absolute_path ) {
			$retVal = self::getApplicationsPath() . '/' . self::$configuration_options['egApplication'] . '/Templates';
		} else {
			$retVal = self::$configuration_options['egApplication'] . '/Templates';
		}

		return $retVal;
	}

	public static function getApplicationXMLPath( $absolute_path = false ) {
		$retVal = null;
		
		if ( $absolute_path ) {
			$retVal = self::getApplicationsPath() . '/' . self::$configuration_options['egApplication'] . '/XML';
		} else {
			$retVal = self::$configuration_options['egApplication'] . '/XML';
		}

		return $retVal;
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

	public static function getCLICombineList() {
		$retVal = null;

		if (isset(self::$configuration_options['CLI'])) {
			$retVal = self::$configuration_options['CLI'];
		} else {
			throw new ErrorException('Details for unset CLI combine list requested');
		}

		return $retVal;
	}

	public static function getCLICombineMapping( $combine_id ) {
		$retVal = null;

		if (isset(self::$configuration_options['CLI'][$combine_id])) {
			$retVal = self::$configuration_options['CLI'][$combine_id];
		}

		return $retVal;
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

	public static function getCDNConnectionInfo( $connection_name = 'egCDNPrimary' ) {
		$retVal = null;

		if (isset(self::$configuration_options['egCDNConnections'][$connection_name])) {
			$retVal = self::$configuration_options['egCDNConnections'][$connection_name];
		} else {
			throw new ErrorException('Details for unknown CDN connection \'' . $connection_name . '\' requested');
		}

		return $retVal;
	}

	public static function issetCDNConnection( $connection_name ) {
		return isset(self::$configuration_options['egCDNConnections'][$connection_name]);
	}

	public static function getCDNConnections() {
		return self::$configuration_options['egCDNConnections'];
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

	public static function issetDatabaseConnection( $connection_name ) {
		return isset(self::$configuration_options['egDatabaseConnections'][$connection_name]);
	}

	public static function getDatabaseConnections() {
		return self::$configuration_options['egDatabaseConnections'];
	}

	public static function getDataStorePath() {
		return self::$configuration_options['DataStorePath'];
	}

	public static function getDeploymentType() {
		// TODO deprecate this
		return self::getDeployment();
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

	/**
	 * Return the help information for this class as a string
	 *
	 * @return string the help information for this class
	 * @author George Cooper
	 **/
	public static function getHelpString() {
		return 'eGloo Configuration Help';
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

	public static function getEngineModeFromString( $engine_mode ) {
		$retVal = null;

		switch( $engine_mode ) {
			case 'Aquinas' :
				$retVal = self::AQUINAS;
				break;
			case 'Cassandra' :
				$retVal = self::CASSANDRA;
				break;
			case 'Doctrine' :
				$retVal = self::DOCTRINE;
				break;
			case 'eGloo' :
				$retVal = self::EGLOO;
				break;
			case 'Mongo' :
				$retVal = self::MONGO;
				break;
			case 'MySQL' :
				$retVal = self::MYSQL;
				break;
			case 'MySQLi' :
				$retVal = self::MYSQLI;
				break;
			case 'MySQLiOOP' :
				$retVal = self::MYSQLIOOP;
				break;
			case 'Oracle' :
				$retVal = self::ORACLE;
				break;
			case 'PDO' :
				$retVal = self::PDO;
				break;
			case 'PostgreSQL' :
				$retVal = self::POSTGRESQL;
				break;
			case 'REST' :
				$retVal = self::REST;
				break;
			case 'SOAP' :
				$retVal = self::SOAP;
				break;
			default :
				break;
		}

		return $retVal;
	}

	public static function getStringFromEngineMode( $engine_mode ) {
		$retVal = null;

		switch( $engine_mode ) {
			case self::AQUINAS :
				$retVal = 'Aquinas';
				break;
			case self::CASSANDRA :
				$retVal = 'Cassandra';
				break;
			case self::DOCTRINE :
				$retVal = 'Doctrine';
				break;
			case self::EGLOO :
				$retVal = 'eGloo';
				break;
			case self::MONGO :
				$retVal = 'Mongo';
				break;
			case self::MYSQL :
				$retVal = 'MySQL';
				break;
			case self::MYSQLI :
				$retVal = 'MySQLi';
				break;
			case self::MYSQLIOOP :
				$retVal = 'MySQLiOOP';
				break;
			case self::ORACLE :
				$retVal = 'Oracle';
				break;
			case self::PDO :
				$retVal = 'PDO';
				break;
			case self::POSTGRESQL :
				$retVal = 'PostgreSQL';
				break;
			case self::REST :
				$retVal = 'REST';
				break;
			case self::SOAP :
				$retVal = 'SOAP';
				break;
			default :
				break;
		}

		return $retVal;
	}

	public static function getExtraClassPath( $absolute_path = false ) {
		$retVal = '';

		if ( isset(self::$configuration_options['ExtraClassPath']) ) {
			if ( $absolute_path ) {
				$retVal = self::getApplicationsPath() . '/' . self::getApplicationPath() . '/' . self::$configuration_options['ExtraClassPath'];
			} else {
				$retVal = self::$configuration_options['ExtraClassPath'];
			}
		}

		return $retVal;
	}

	public static function getExtraDatabasePath( $absolute_path = false ) {
		$retVal = '';

		if ( isset(self::$configuration_options['ExtraDatabasePath']) ) {
			if ( $absolute_path ) {
				$retVal = self::getApplicationsPath() . '/' . self::getApplicationPath() . '/' . self::$configuration_options['ExtraDatabasePath'];
			} else {
				$retVal = self::$configuration_options['ExtraDatabasePath'];
			}
		}

		return $retVal;
	}

	public static function getExtraConfigurationPath( $absolute_path = false ) {
		$retVal = '';

		if ( isset(self::$configuration_options['ExtraConfigurationPath']) ) {
			if ( $absolute_path ) {
				$retVal = self::getApplicationsPath() . '/' . self::getApplicationPath() . '/' . self::$configuration_options['ExtraConfigurationPath'];
			} else {
				$retVal = self::$configuration_options['ExtraConfigurationPath'];
			}
		}

		return $retVal;
	}

	public static function getExtraTemplatePath( $absolute_path = false ) {
		$retVal = '';

		if ( isset(self::$configuration_options['ExtraTemplatePath']) ) {
			if ( $absolute_path ) {
				$retVal = self::getApplicationsPath() . '/' . self::getApplicationPath() . '/' . self::$configuration_options['ExtraTemplatePath'];
			} else {
				$retVal = self::$configuration_options['ExtraTemplatePath'];
			}
		}

		return $retVal;
	}

	public static function getFrameworkRootPath( $absolute_path = false ) {
		return self::$configuration_options['FrameworkRootPath'];
	}

	public static function getFrameworkPHPPath( $absolute_path = false ) {
		return self::$configuration_options['FrameworkRootPath'] . '/PHP';
	}

	public static function getFrameworkTemplatesPath( $absolute_path = false ) {
		return self::$configuration_options['FrameworkRootPath'] . '/Templates';
	}

	public static function getFrameworkXMLPath( $absolute_path = false ) {
		return self::$configuration_options['FrameworkRootPath'] . '/XML';
	}

	public static function getHaangaIncludePath() {
		// TODO make this customizable
		// return self::$configuration_options['HaangaPath'];
		return self::$configuration_options['FrameworkRootPath'] . '/Library/Haanga/lib/Haanga.php';
	}

	public static function getLogFormat( $update_cache_on_set = true ) {
		if ( !isset(self::$configuration_options['egLogFormat']) ) {
			self::$configuration_options['egLogFormat'] = eGlooLogger::LOG_LOG;

			if ( $update_cache_on_set ) {
				// TODO see if we should move this into an "update cache" method
				self::writeFrameworkConfigurationCache();

				if (self::getUseRuntimeCache()) {
					// self::writeRuntimeCache();
					self::writeRuntimeCacheClass();
				}
			}
		} else if ( is_string(self::$configuration_options['egLogFormat']) ) {
			switch( strtoupper(self::$configuration_options['egLogFormat']) ) {
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

			if ( $update_cache_on_set ) {
				// TODO see if we should move this into an "update cache" method
				self::writeFrameworkConfigurationCache();
			
				if (self::getUseRuntimeCache()) {
					// self::writeRuntimeCache();
					self::writeRuntimeCacheClass();
				}
			}
		}

		return self::$configuration_options['egLogFormat'];
	}

	public static function getLoggingPath() {
		return self::$configuration_options['LoggingPath'];
	}
	
	public static function getLoggingLevel( $update_cache_on_set = true ) {
		if ( !isset(self::$configuration_options['egLogLevel']) ) {
			self::$configuration_options['egLogLevel'] = eGlooLogger::DEVELOPMENT;

			if ( $update_cache_on_set ) {
				// TODO see if we should move this into an "update cache" method
				self::writeFrameworkConfigurationCache();

				if (self::getUseRuntimeCache()) {
					// self::writeRuntimeCache();
					self::writeRuntimeCacheClass();
				}
			}
		} else if ( is_string(self::$configuration_options['egLogLevel']) ) {
			switch( strtoupper(self::$configuration_options['egLogLevel']) ) {
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

			if ( $update_cache_on_set ) {
				// TODO see if we should move this into an "update cache" method
				self::writeFrameworkConfigurationCache();
			
				if (self::getUseRuntimeCache()) {
					// self::writeRuntimeCache();
					self::writeRuntimeCacheClass();
				}
			}
		}

		return self::$configuration_options['egLogLevel'];
	}

	public static function getPerformSanityCheckClassLoading() {
		return isset(self::$configuration_options['egSanityCheckClassLoading']) ? self::$configuration_options['egSanityCheckClassLoading'] : true;
	}

	public static function getRewriteBase() {
		return self::$rewriteBase;
	}

	public static function getS3IncludePath() {
		// TODO make this customizable
		// return self::$configuration_options['S3Path'];
		return self::$configuration_options['FrameworkRootPath'] . '/Library/S3/S3.php';
	}

	public static function getSimpleTestIncludePath() {
		// TODO make this customizable
		// return self::$configuration_options['S3Path'];
		return self::$configuration_options['FrameworkRootPath'] . '/Library/SimpleTest/autorun.php';
	}

	public static function getSmartyIncludePath() {
		// TODO make this customizable based upon version
		return self::$configuration_options['SmartyPath'];
	}

	public static function getSwiftIncludePath() {
		// TODO make this customizable
		// return self::$configuration_options['SwiftPath'];
		return self::$configuration_options['FrameworkRootPath'] . '/Library/Swift4/lib/swift_required.php';
	}

	public static function getTwigIncludePath() {
		// TODO make this customizable
		// return self::$configuration_options['TwigPath'];
		return self::$configuration_options['FrameworkRootPath'] . '/Library/Twig/lib/Twig/Autoloader.php';
	}

	public static function getUniqueInstanceIdentifier() {
		return self::$uniqueInstanceID;
	}

	public static function getUseAPCCache() {
		return isset(self::$configuration_options['egAPCCacheEnabled']) ? self::$configuration_options['egAPCCacheEnabled'] : false;
	}

	public static function getUseCache() {
		return isset(self::$configuration_options['egCacheEnabled']) ? self::$configuration_options['egCacheEnabled'] : true;
	}

	public static function getUseCDN() {
		return isset(self::$configuration_options['egCDNEnabled']) ? self::$configuration_options['egCDNEnabled'] : false;
	}

	public static function getUseDefaultRequestClassHandler() {
		return self::$configuration_options['egEnableDefaultRequestClass'];
	}

	public static function getUseDefaultRequestIDHandler() {
		return self::$configuration_options['egEnableDefaultRequestID'];
	}

	public static function getUseDoctrine() {
		$retVal = false;

		if ( isset(self::$configuration_options['egUseDoctrine']) ) {
			if ( self::$configuration_options['egUseDoctrine'] === 'true' || self::$configuration_options['egUseDoctrine'] === true ) {
				$retVal = true;
			}
		}

		return $retVal;
	}

	public static function getUseFileCache() {
		return isset(self::$configuration_options['egFileCacheEnabled']) ? self::$configuration_options['egFileCacheEnabled'] : false;
	}

	public static function getUseHaanga() {
		$retVal = false;

		if ( isset(self::$configuration_options['egUseHaanga']) ) {
			if ( self::$configuration_options['egUseHaanga'] === 'true' || self::$configuration_options['egUseHaanga'] === true ) {
				$retVal = true;
			}
		}

		return $retVal;
	}

	public static function getUseHotFileCSSClustering() {
		return self::$configuration_options['egHotFileCSSClusteringEnabled'];
	}

	public static function getUseHotFileImageClustering() {
		return self::$configuration_options['egHotFileImageClusteringEnabled'];
	}

	public static function getUseHotFileJavascriptClustering() {
		return self::$configuration_options['egHotFileJavascriptClusteringEnabled'];
	}

	public static function getUseHotFileMediaClustering() {
		return self::$configuration_options['egHotFileMediaClusteringEnabled'];
	}

	public static function getUseMemcache() {
		return isset(self::$configuration_options['egMemcacheCacheEnabled']) ? self::$configuration_options['egMemcacheCacheEnabled'] : true;
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

	public static function getUseS3() {
		$retVal = false;

		if ( isset(self::$configuration_options['egUseS3']) ) {
			if ( self::$configuration_options['egUseS3'] === 'true' || self::$configuration_options['egUseS3'] === true ) {
				$retVal = true;
			}
		}

		return $retVal;
	}

	public static function getUseSmarty() {
		$retVal = false;

		if ( isset(self::$configuration_options['egUseSmarty']) ) {
			if ( self::$configuration_options['egUseSmarty'] === 'true' || self::$configuration_options['egUseSmarty'] === true ) {
				$retVal = true;
			}
		}

		return $retVal;
	}

	public static function getUseSwift() {
		$retVal = false;

		if ( isset(self::$configuration_options['egUseSwift']) ) {
			if ( self::$configuration_options['egUseSwift'] === 'true' || self::$configuration_options['egUseSwift'] === true ) {
				$retVal = true;
			}
		}

		return $retVal;
	}

	public static function getUseTwig() {
		$retVal = false;

		if ( isset(self::$configuration_options['egUseTwig']) ) {
			if ( self::$configuration_options['egUseTwig'] === 'true' || self::$configuration_options['egUseTwig'] === true ) {
				$retVal = true;
			}
		}

		return $retVal;
	}

	public static function getWebRoot() {
		if (self::$web_root === null) {
			$matches = array();
			preg_match('~^(.*)?(index.php)$~', $_SERVER['SCRIPT_FILENAME'], $matches);
			self::$web_root = $matches[1];
		}

		return self::$web_root;
	}

	public static function issetCustomVariable( $index ) {
		$retVal = false;
		
		if (isset(self::$configuration_options['CustomVariables'][$index])) {
			$retVal = true;
		}

		return $retVal;
	}

}

if ( class_exists('\eGloo\Utility\Logger', false) )  {
	deprecate( __FILE__, '\eGloo\Configuration' );
}

