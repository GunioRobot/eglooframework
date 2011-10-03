<?php
namespace eGloo;

/**
 * eGloo\Application Class File
 *
 * Contains the class definition for the eGloo\Application
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
 * @package Main
 * @subpackage Application
 * @version 1.0
 */

/**
 * eGloo\Application
 *
 * $short_description
 *
 * $long_description
 *
 * @category System
 * @package Main
 * @subpackage Application
 */
class Application extends Bundle {

	/**
	 * @var string Name of this application
	 */
	protected $_application_name = null;

	/**
	 * @var array Paths for Cubes this application contains
	 */
	protected $_cube_paths = array();

	/**
	 * @var array Interface bundles of this application
	 */
	protected $_interface_bundles = array( 'Default' => 'Default' );

	/**
	 * Application constructor
	 *
	 * @param string Path for this application bundle
	 */
	public function __construct( $app_bundle_path = null ) {
		$this->_real_path = $app_bundle_path;

		$this->init( $app_bundle_path );
	}

	public function build( $frameworkObj = null ) {
		if ( $frameworkObj ) {
			
		} else {
			
		}
	}

	public function check( $frameworkObj = null ) {
		if ( $frameworkObj ) {
			
		} else {
			
		}
	}

	public function generateChecksum( $frameworkObj = null ) {
		if ( $frameworkObj ) {
			
		} else {
			
		}
	}

	public function init( $app_bundle_path = null ) {
		if ( file_exists($app_bundle_path) && is_dir($app_bundle_path) && strpos($app_bundle_path, '.gloo') ) {
			$dir_name = preg_replace( '~.*/~', '', $app_bundle_path );
			$dir_name = preg_replace( '~/~', '', $dir_name );

			$this->_application_name = preg_replace( '~\.gloo~', '', $dir_name );

			try {
				$this->initInterfaceBundles( realpath($app_bundle_path) );
			} catch (\Exception $e) {
				echo $e->getMessage() . "\n";
			}
		}
	}

	public function install( $frameworkObj = null ) {
		if ( $frameworkObj ) {
			$applications_path = $frameworkObj->getApplicationsPath();

			rename( $this->_real_path, $applications_path . '/' . $this->_application_name . '.gloo' );
		} else {
		}
	}

	public function load( $app_bundle_path ) {
		$framework = new Framework();
		die_r($framework);
	}

	public function save( $app_bundle_path ) {
		$retVal = false;

		$save_path = $app_bundle_path . '/' . $this->_application_name . '.gloo';

		if ( !is_writable( $save_path ) ) {
			try {
				mkdir( $save_path, 0755, true );
				$retVal = true;
			} catch (\Exception $e){
				echo $e->getMessage() . "\n";
			}
		}

		return $retVal;
	}

	public function uninstall( $frameworkObj = null ) {
		if ( !$frameworkObj ) {
			
		} else {
			
		}
	}

	public function upgrade( $frameworkObj = null ) {
		if ( !$frameworkObj ) {
			
		} else {
			
		}
	}

	public function verify( $frameworkObj = null ) {
		if ( !$frameworkObj ) {
			
		} else {
			
		}
	}

	/**
	 * Returns protected class member $_application_name
	 *
	 * @return string Name of this application
	 */
	public function getApplicationName() {
		return $this->_application_name;
	}

	/**
	 * Sets protected class member $_application_name
	 *
	 * @param application_name string Name of this application
	 */
	public function setApplicationName( $application_name ) {
		$this->_application_name = $application_name;
	}

	/**
	 * Returns protected class member $_cube_paths
	 *
	 * @return array Paths for Cubes this application contains
	 */
	public function getCubePaths() {
		return $this->_cube_paths;
	}

	/**
	 * Sets protected class member $_cube_paths
	 *
	 * @param cube_paths array Paths for Cubes this application contains
	 */
	public function setCubePaths( $cube_paths ) {
		$this->_cube_paths = $cube_paths;
	}

	/**
	 * Adds an interface bundle entry to class member $_interface_bundles
	 *
	 * @param string Interface bundle to add to this application
	 */
	public function addInterfaceBundle( $bundle_name ) {
		$this->_interface_bundles[$bundle_name] = $bundle_name;
	}

	/**
	 * Removes an interface bundle entry from class member $_interface_bundles
	 *
	 * @param string Interface bundle to remove from this application
	 */
	public function removeInterfaceBundle( $bundle_name ) {
		unset($this->_interface_bundles[$bundle_name]);
	}

	/**
	 * Returns all interface bundles for this bundle
	 *
	 * @return array File paths for build files this application contains
	 */
	protected function initInterfaceBundles( $app_bundle_path ) {
		if (!file_exists($app_bundle_path . '/InterfaceBundles')) {
			throw new \ErrorException('No interface bundles found');
		}

		$retVal = array();

		$it = new DirectoryIterator( $app_bundle_path . '/InterfaceBundles' );

		foreach ($it as $i) {
			if ( !in_array($i->getFilename(), array('.', '..', '.DS_Store')) ) {
				$retVal[] = $i->getFilename();
			}
		}

		$this->_interface_bundles = $retVal;

		return $retVal;
	}

	/**
	 * Returns protected class member $_interface_bundles
	 *
	 * @return array Interface bundles of this application
	 */
	public function getInterfaceBundles() {
		return $this->_interface_bundles;
	}

	/**
	 * Sets protected class member $_interface_bundles
	 *
	 * @param interface_bundles array Interface bundles of this application
	 */
	public function setInterfaceBundles( array $interface_bundles ) {
		$this->_interface_bundles = $interface_bundles;
	}

	public static function getFreshSkeleton( $application_name, $options = array() ) {
		$retVal = new self();

		$retVal->setApplicationName( $application_name );

		return $retVal;
	}

}

