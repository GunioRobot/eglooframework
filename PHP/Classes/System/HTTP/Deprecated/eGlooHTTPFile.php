<?php
/**
 * eGlooHTTPFile Class File
 *
 * Contains the class definition for the eGlooHTTPFile
 * 
 * Copyright 2011 eGloo, LLC
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
 * @copyright 2011 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @category System
 * @package HTTP
 * @subpackage Files
 * @version 1.0
 */

/**
 * eGlooHTTPFile
 *
 * $short_description
 *
 * $long_description
 *
 * @category System
 * @package HTTP
 * @subpackage Files
 */
class eGlooHTTPFile {

	/**
	 * @var string ID of input field that this file was submitted from
	 */
	protected $_input_field_id = null;

	/**
	 * Returns protected class member $_input_field_id
	 *
	 * @return string ID of input field that this file was submitted from
	 */
	public function getInputFieldID() {
		return $this->_input_field_id;
	}

	/**
	 * Sets protected class member $_input_field_id
	 *
	 * @param input_field_id string ID of input field that this file was submitted from
	 */
	public function setInputFieldID( $input_field_id ) {
		$this->_input_field_id = $input_field_id;
	}

	/**
	 * @var string Name of submitted file
	 */
	protected $_file_name = null;

	/**
	 * Returns protected class member $_file_name
	 *
	 * @return string Name of submitted file
	 */
	public function getFileName() {
		return $this->_file_name;
	}

	/**
	 * Sets protected class member $_file_name
	 *
	 * @param file_name string Name of submitted file
	 */
	public function setFileName( $file_name ) {
		$this->_file_name = $file_name;
	}

	/**
	 * @var string Type of file
	 */
	protected $_file_type = null;

	/**
	 * Returns protected class member $_file_type
	 *
	 * @return string Type of file
	 */
	public function getFileType() {
		return $this->_file_type;
	}

	/**
	 * Sets protected class member $_file_type
	 *
	 * @param file_type string Type of file
	 */
	public function setFileType( $file_type ) {
		$this->_file_type = $file_type;
	}

	/**
	 * @var string Temporary name of file
	 */
	protected $_temporary_file_name = null;

	/**
	 * Returns protected class member $_temporary_file_name
	 *
	 * @return string Temporary name of file
	 */
	public function getTemporaryFileName() {
		return $this->_temporary_file_name;
	}

	/**
	 * Sets protected class member $_temporary_file_name
	 *
	 * @param temporary_file_name string Temporary name of file
	 */
	public function setTemporaryFileName( $temporary_file_name ) {
		$this->_temporary_file_name = $temporary_file_name;
	}

	/**
	 * @var integer Error code (if any)
	 */
	protected $_error_code = null;

	/**
	 * Returns protected class member $_error_code
	 *
	 * @return integer Error code (if any)
	 */
	public function getErrorCode() {
		return $this->_error_code;
	}

	/**
	 * Sets protected class member $_error_code
	 *
	 * @param error_code integer Error code (if any)
	 */
	public function setErrorCode( $error_code ) {
		$this->_error_code = $error_code;
	}

	/**
	 * @var integer Size of file in bytes
	 */
	protected $_file_size = null;

	/**
	 * Returns protected class member $_file_size
	 *
	 * @return integer Size of file in bytes
	 */
	public function getFileSize() {
		return $this->_file_size;
	}

	/**
	 * Sets protected class member $_file_size
	 *
	 * @param file_size integer Size of file in bytes
	 */
	public function setFileSize( $file_size ) {
		$this->_file_size = $file_size;
	}

	public function __construct( $form_field_id ) {
		$form_field_id_chunks = explode( ' ', $form_field_id );

		if ( isset($form_field_id_chunks[0]) ) {
			$file_info_array = $_FILES[$form_field_id_chunks[0]];
			$field_name_chunk_count = count($form_field_id_chunks);

			$curNode = $file_info_array['name'];

			for( $i = 1; $i < $field_name_chunk_count; $i++ ) {
				$curNode = $curNode[$form_field_id_chunks[$i]];
			}

			$file_info_name = $curNode;

			$curNode = $file_info_array['type'];

			for( $i = 1; $i < $field_name_chunk_count; $i++ ) {
				$curNode = $curNode[$form_field_id_chunks[$i]];
			}

			$file_info_type = $curNode;

			$curNode = $file_info_array['tmp_name'];

			for( $i = 1; $i < $field_name_chunk_count; $i++ ) {
				$curNode = $curNode[$form_field_id_chunks[$i]];
			}

			$file_info_tmp_name = $curNode;

			$curNode = $file_info_array['error'];

			for( $i = 1; $i < $field_name_chunk_count; $i++ ) {
				$curNode = $curNode[$form_field_id_chunks[$i]];
			}

			$file_info_error = $curNode;

			$curNode = $file_info_array['size'];

			for( $i = 1; $i < $field_name_chunk_count; $i++ ) {
				$curNode = $curNode[$form_field_id_chunks[$i]];
			}

			$file_info_size = $curNode;
		}

		$this->setInputFieldID( $form_field_id );
		$this->setFileName( $file_info_name );
		$this->setFileType( $file_info_type );
		$this->setTemporaryFileName( $file_info_tmp_name );
		$this->setErrorCode( $file_info_error );
		$this->setFileSize( $file_info_size );
	}

	public function __destruct() {
		
	}

}

deprecate( __FILE__, '\eGloo\HTTP\File' );
