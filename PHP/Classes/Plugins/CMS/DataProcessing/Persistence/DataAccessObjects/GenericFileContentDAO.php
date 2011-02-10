<?php
/**
 * GenericFileContentDAO Class File
 *
 * Contains the class definition for the GenericFileContentDAO
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
 * @package $package
 * @subpackage $subpackage
 * @version 1.0
 */

/**
 * GenericFileContentDAO
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
abstract class GenericFileContentDAO extends AbstractDAO {

	// Bucket Methods
	// TBD

	// File Methods
	abstract public function copyFile( $fileContentDTO, $src_file_bucket = 'Default', $src_store_prefix = 'Local', $src_zone = 'Uploaded',
		$dest_file_bucket = 'Default', $dest_store_prefix = 'Local', $dest_zone = 'Master' );

	abstract public function deleteFile( $fileContentDTO, $file_bucket = 'Default', $store_prefix = 'Local', $zone = 'Master' );

	abstract public function getFile( $fileContentDTO, $file_bucket = 'Default', $store_prefix = 'Local', $zone = 'Master' );

	abstract public function getFileMeta( $fileContentDTO, $file_bucket = 'Default', $store_prefix = 'Local', $zone = 'Master' );

	abstract public function moveFile( $fileContentDTO, $src_file_bucket = 'Default', $src_store_prefix = 'Local', $src_zone = 'Uploaded',
		$dest_file_bucket = 'Default', $dest_store_prefix = 'Local', $dest_zone = 'Master' );

	abstract public function storeFile( $fileContentDTO, $file_bucket = 'Default', $store_prefix = 'Local', $zone = 'Uploaded' );

	// Prefix Methods
	// TBD

}
