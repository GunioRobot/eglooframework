<?php
/**
 * eGlooDataStoreGenericFileContentDAO Class File
 *
 * Contains the class definition for the eGlooDataStoreGenericFileContentDAO
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
 * eGlooDataStoreGenericFileContentDAO
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class eGlooDataStoreGenericFileContentDAO extends GenericFileContentDAO {

	// Bucket Methods
	// TBD

	// File Methods
	public function copyFile( $fileContentDTO, $src_file_bucket = 'Default', $src_store_prefix = 'Local', $src_zone = 'Uploaded',
		$dest_file_bucket = 'Default', $dest_store_prefix = 'Local', $dest_zone = 'Master' ) {
			
	}

	public function deleteFile( $fileContentDTO, $file_bucket = 'Default', $store_prefix = 'Local', $zone = 'Master' ) {
		
	}

	public function getFile( $fileContentDTO, $file_bucket = 'Default', $store_prefix = 'Local', $zone = 'Master' ) {
		
	}

	public function getFileMeta( $fileContentDTO, $file_bucket = 'Default', $store_prefix = 'Local', $zone = 'Master' ) {
		
	}

	public function getFileStorePath( $fileContentDTO, $file_bucket = 'Default', $store_prefix = 'Local', $zone = 'Master' ) {
		
	}

	public function moveFile( $fileContentDTO, $src_file_bucket = 'Default', $src_store_prefix = 'Local', $src_zone = 'Uploaded',
		$dest_file_bucket = 'Default', $dest_store_prefix = 'Local', $dest_zone = 'Master' ) {
			
	}

	public function storeMasterFile( $fileContentDTO, $file_bucket = 'Default', $store_prefix = 'Local' ) {
		return $this->storeFile( $fileContentDTO, $file_bucket, $store_prefix, 'Master' );
	}

	public function storeUploadedFile( $fileContentDTO, $file_bucket = 'Default', $store_prefix = 'Local' ) {
		return $this->storeFile( $fileContentDTO, $file_bucket, $store_prefix, 'Uploaded' );
	}

	public function storeFile( $fileContentDTO, $file_bucket = 'Default', $store_prefix = 'Local', $zone = 'Uploaded' ) {
		$mimeType = $fileContentDTO->getFileMIMEType();
		$localID = $fileContentDTO->getFileLocalID();
		$mod = $fileContentDTO->getFileMod();
		// Going to refactor this out later...
		$category = 'Generic';

		$data_store_file_folder_path = eGlooConfiguration::getDataStorePath() . '/' . $store_prefix . '/' . $zone . '/' .
			$category . '/' . $file_bucket . '/' . $mod . '/';

		$extension = '';

		switch( $mimeType ) {
			case 'file/jpeg' :
				$extension = 'jpg';
				break;
			default :
				$extension = 'unknown';
				break;
		}

		$data_store_file_path = $data_store_file_folder_path . $localID . '.' . $extension;

		if ( !is_writable( $data_store_file_folder_path ) ) {
			try {
				$mode = 0777;
				$recursive = true;

				mkdir( $data_store_file_folder_path, $mode, $recursive );
			} catch (Exception $e){
				echo_r($e->getMessage());
			}
		}

		// die_r($fileContentDTO);
		// TODO make sure the file uploaded properly, no errors
		echo_r($fileContentDTO->getFilePath());
		echo_r($data_store_file_path);
		copy($fileContentDTO->getFilePath(), $data_store_file_path);

		return $data_store_file_path;
	}

	// Prefix Methods
	// TBD

}
