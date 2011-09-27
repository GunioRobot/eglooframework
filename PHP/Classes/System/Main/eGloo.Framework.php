<?php
namespace eGloo;

/**
 * eGloo\Framework Class File
 *
 * Contains the class definition for the eGloo\Framework
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
 * @subpackage Framework
 * @version 1.0
 */

/**
 * eGloo\Framework
 *
 * $short_description
 *
 * $long_description
 *
 * @category System
 * @package Main
 * @subpackage Framework
 */
class Framework {

	/**
	 * @var string Path to eGloo applications
	 */
	protected $_applications_path = null;

	/**
	 * @var string Path to eGloo caches (template engine, etc)
	 */
	protected $_cache_path = null;

	/**
	 * @var string Path to configuration files
	 */
	protected $_configuration_path = null;

	/**
	 * @var string Path to eGloo cubes
	 */
	protected $_cubes_path = null;

	/**
	 * @var string Path to the eGloo data store
	 */
	protected $_data_store_path = null;

	/**
	 * @var string Path to Doctrine
	 */
	protected $_doctrine_path = null;

	/**
	 * @var string Path to eGloo documentation
	 */
	protected $_documentation_path = null;

	/**
	 * @var string Path to the local web server document root
	 */
	protected $_document_root_path = null;

	/**
	 * @var string Path to the framework root
	 */
	protected $_framework_root_path = null;

	/**
	 * @var string Path to the framework logs
	 */
	protected $_logging_path = null;

	/**
	 * @var string Path to Smarty
	 */
	protected $_smarty_path = null;

	/**
	 * Constructor for this object
	 */
	public function __construct() {
		$this->_applications_path = \eGlooConfiguration::getApplicationsPath();
		$this->_cache_path = \eGlooConfiguration::getCachePath();
		$this->_configuration_path = \eGlooConfiguration::getConfigurationPath();
		$this->_cubes_path = \eGlooConfiguration::getCubesPath();
		$this->_data_store_path = \eGlooConfiguration::getDataStorePath();
		$this->_doctrine_path = \eGlooConfiguration::getDoctrineIncludePath();
		$this->_documentation_path = \eGlooConfiguration::getDocumentationPath();
		$this->_document_root_path = \eGlooConfiguration::getDocumentRoot();
		$this->_framework_root_path = \eGlooConfiguration::getFrameworkRootPath();
		$this->_logging_path = \eGlooConfiguration::getLoggingPath();
		$this->_smarty_path = \eGlooConfiguration::getSmartyIncludePath();
	}

	/**
	 * Returns protected class member $_applications_path
	 *
	 * @return string Path to eGloo applications
	 */
	public function getApplicationsPath() {
		return $this->_applications_path;
	}

	/**
	 * Sets protected class member $_applications_path
	 *
	 * @param applications_path string Path to eGloo applications
	 */
	public function setApplicationsPath( $applications_path ) {
		$this->_applications_path = $applications_path;
	}

	/**
	 * Returns protected class member $_cache_path
	 *
	 * @return string Path to eGloo caches (template engine, etc)
	 */
	public function getCachePath() {
		return $this->_cache_path;
	}

	/**
	 * Sets protected class member $_cache_path
	 *
	 * @param cache_path string Path to eGloo caches (template engine, etc)
	 */
	public function setCachePath( $cache_path ) {
		$this->_cache_path = $cache_path;
	}

	/**
	 * Returns protected class member $_configuration_path
	 *
	 * @return string Path to configuration files
	 */
	public function getConfigurationPath() {
		return $this->_configuration_path;
	}

	/**
	 * Sets protected class member $_configuration_path
	 *
	 * @param configuration_path string Path to configuration files
	 */
	public function setConfigurationPath( $configuration_path ) {
		$this->_configuration_path = $configuration_path;
	}

	/**
	 * Returns protected class member $_cubes_path
	 *
	 * @return string Path to eGloo cubes
	 */
	public function getCubesPath() {
		return $this->_cubes_path;
	}

	/**
	 * Sets protected class member $_cubes_path
	 *
	 * @param cubes_path string Path to eGloo cubes
	 */
	public function setCubesPath( $cubes_path ) {
		$this->_cubes_path = $cubes_path;
	}

	/**
	 * Returns protected class member $_data_store_path
	 *
	 * @return string Path to the eGloo data store
	 */
	public function getDataStorePath() {
		return $this->_data_store_path;
	}

	/**
	 * Sets protected class member $_data_store_path
	 *
	 * @param data_store_path string Path to the eGloo data store
	 */
	public function setDataStorePath( $data_store_path ) {
		$this->_data_store_path = $data_store_path;
	}

	/**
	 * Returns protected class member $_doctrine_path
	 *
	 * @return string Path to Doctrine
	 */
	public function getDoctrinePath() {
		return $this->_doctrine_path;
	}

	/**
	 * Sets protected class member $_doctrine_path
	 *
	 * @param doctrine_path string Path to Doctrine
	 */
	public function setDoctrinePath( $doctrine_path ) {
		$this->_doctrine_path = $doctrine_path;
	}

	/**
	 * Returns protected class member $_documentation_path
	 *
	 * @return string Path to eGloo documentation
	 */
	public function getDocumentationPath() {
		return $this->_documentation_path;
	}

	/**
	 * Sets protected class member $_documentation_path
	 *
	 * @param documentation_path string Path to eGloo documentation
	 */
	public function setDocumentationPath( $documentation_path ) {
		$this->_documentation_path = $documentation_path;
	}

	/**
	 * Returns protected class member $_document_root_path
	 *
	 * @return string Path to the local web server document root
	 */
	public function getDocumentRootPath() {
		return $this->_document_root_path;
	}

	/**
	 * Sets protected class member $_document_root_path
	 *
	 * @param document_root_path string Path to the local web server document root
	 */
	public function setDocumentRootPath( $document_root_path ) {
		$this->_document_root_path = $document_root_path;
	}

	/**
	 * Returns protected class member $_framework_root_path
	 *
	 * @return string Path to the framework root
	 */
	public function getFrameworkRootPath() {
		return $this->_framework_root_path;
	}

	/**
	 * Sets protected class member $_framework_root_path
	 *
	 * @param framework_root_path string Path to the framework root
	 */
	public function setFrameworkRootPath( $framework_root_path ) {
		$this->_framework_root_path = $framework_root_path;
	}

	/**
	 * Returns protected class member $_logging_path
	 *
	 * @return string Path to the framework logs
	 */
	public function getLoggingPath() {
		return $this->_logging_path;
	}

	/**
	 * Sets protected class member $_logging_path
	 *
	 * @param logging_path string Path to the framework logs
	 */
	public function setLoggingPath( $logging_path ) {
		$this->_logging_path = $logging_path;
	}

	/**
	 * Returns protected class member $_smarty_path
	 *
	 * @return string Path to Smarty
	 */
	public function getSmartyPath() {
		return $this->_smarty_path;
	}

	/**
	 * Sets protected class member $_smarty_path
	 *
	 * @param smarty_path string Path to Smarty
	 */
	public function setSmartyPath( $smarty_path ) {
		$this->_smarty_path = $smarty_path;
	}

}

