<?php
namespace eGloo;

/**
 * eGloo\Bundle Class File
 *
 * Contains the class definition for the eGloo\Bundle
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
 * @subpackage Bundle
 * @version 1.0
 */

/**
 * eGloo\Bundle
 *
 * $short_description
 *
 * $long_description
 *
 * @category System
 * @package Main
 * @subpackage Bundle
 */
abstract class Bundle {

	/**
	 * @var array File paths for build files this bundle contains
	 */
	protected $_build_file_paths = array();

	/**
	 * @var array File paths for configuration files this bundle contains
	 */
	protected $_config_file_paths = array();

	/**
	 * @var array File paths for database files this bundle contains
	 */
	protected $_database_file_paths = array();

	/**
	 * @var array File paths for network files this bundle contains
	 */
	protected $_network_file_paths = array();

	/**
	 * @var array File paths for PHP files this bundle contains
	 */
	protected $_php_file_paths = array();

	/**
	 * @var Type Real path for this bundle
	 */
	protected $_real_path = null;

	/**
	 * @var array File paths for template files this bundle contains
	 */
	protected $_template_file_paths = array();

	/**
	 * @var array File paths for test files this bundle contains
	 */
	protected $_test_file_paths = array();

	/**
	 * @var array File paths for top level files this bundle contains
	 */
	protected $_top_level_file_paths = null;

	/**
	 * @var array File paths for XML files this bundle contains
	 */
	protected $_xml_file_paths = array();

	/**
	 * Returns protected class member $_build_file_paths
	 *
	 * @return array File paths for build files this bundle contains
	 */
	public function getBuildFilePaths() {
		return $this->_build_file_paths;
	}

	/**
	 * Sets protected class member $_build_file_paths
	 *
	 * @param build_file_paths array File paths for build files this bundle contains
	 */
	public function setBuildFilePaths( $build_file_paths ) {
		$this->_build_file_paths = $build_file_paths;
	}

	/**
	 * Returns protected class member $_config_file_paths
	 *
	 * @return array File paths for configuration files this bundle contains
	 */
	public function getConfigFilePaths() {
		return $this->_config_file_paths;
	}

	/**
	 * Sets protected class member $_config_file_paths
	 *
	 * @param config_file_paths array File paths for configuration files this bundle contains
	 */
	public function setConfigFilePaths( $config_file_paths ) {
		$this->_config_file_paths = $config_file_paths;
	}

	/**
	 * Returns protected class member $_database_file_paths
	 *
	 * @return array File paths for database files this bundle contains
	 */
	public function getDatabaseFilePaths() {
		return $this->_database_file_paths;
	}

	/**
	 * Sets protected class member $_database_file_paths
	 *
	 * @param database_file_paths array File paths for database files this bundle contains
	 */
	public function setDatabaseFilePaths( $database_file_paths ) {
		$this->_database_file_paths = $database_file_paths;
	}

	/**
	 * Returns protected class member $_network_file_paths
	 *
	 * @return array File paths for network files this bundle contains
	 */
	public function getNetworkFilePaths() {
		return $this->_network_file_paths;
	}

	/**
	 * Sets protected class member $_network_file_paths
	 *
	 * @param network_file_paths array File paths for network files this bundle contains
	 */
	public function setNetworkFilePaths( $network_file_paths ) {
		$this->_network_file_paths = $network_file_paths;
	}

	/**
	 * Returns protected class member $_php_file_paths
	 *
	 * @return array File paths for PHP files this bundle contains
	 */
	public function getPHPFilePaths() {
		return $this->_php_file_paths;
	}

	/**
	 * Sets protected class member $_php_file_paths
	 *
	 * @param php_file_paths array File paths for PHP files this bundle contains
	 */
	public function setPHPFilePaths( $php_file_paths ) {
		$this->_php_file_paths = $php_file_paths;
	}

	/**
	 * Returns protected class member $_real_path
	 *
	 * @return Type Real path for this bundle
	 */
	public function getRealPath() {
		return $this->_real_path;
	}

	/**
	 * Sets protected class member $_real_path
	 *
	 * @param real_path Type Real path for this bundle
	 */
	public function setRealPath( $real_path ) {
		$this->_real_path = $real_path;
	}

	/**
	 * Returns protected class member $_template_file_paths
	 *
	 * @return array File paths for template files this bundle contains
	 */
	public function getTemplateFilePaths() {
		return $this->_template_file_paths;
	}

	/**
	 * Sets protected class member $_template_file_paths
	 *
	 * @param template_file_paths array File paths for template files this bundle contains
	 */
	public function setTemplateFilePaths( $template_file_paths ) {
		$this->_template_file_paths = $template_file_paths;
	}

	/**
	 * Returns protected class member $_test_file_paths
	 *
	 * @return array File paths for test files this bundle contains
	 */
	public function getTestFilePaths() {
		return $this->_test_file_paths;
	}

	/**
	 * Sets protected class member $_test_file_paths
	 *
	 * @param test_file_paths array File paths for test files this bundle contains
	 */
	public function setTestFilePaths( $test_file_paths ) {
		$this->_test_file_paths = $test_file_paths;
	}

	/**
	 * Returns protected class member $_top_level_file_paths
	 *
	 * @return array File paths for top level files this bundle contains
	 */
	public function getTopLevelFilePaths() {
		return $this->_top_level_file_paths;
	}

	/**
	 * Sets protected class member $_top_level_file_paths
	 *
	 * @param top_level_file_paths array File paths for top level files this bundle contains
	 */
	public function setTopLevelFilePaths( $top_level_file_paths ) {
		$this->_top_level_file_paths = $top_level_file_paths;
	}

	/**
	 * Returns protected class member $_xml_file_paths
	 *
	 * @return array File paths for XML files this bundle contains
	 */
	public function getXMLFilePaths() {
		return $this->_xml_file_paths;
	}

	/**
	 * Sets protected class member $_xml_file_paths
	 *
	 * @param xml_file_paths array File paths for XML files this bundle contains
	 */
	public function setXMLFilePaths( $xml_file_paths ) {
		$this->_xml_file_paths = $xml_file_paths;
	}

}

