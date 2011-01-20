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
	public function copyFile( $fileContentDTO, $src_file_bucket = 'Default', $src_store_prefix = 'Local',
		$dest_file_bucket = 'Default', $dest_store_prefix = 'Local' ) {
			
	}

	public function deleteFile( $fileContentDTO, $file_bucket = 'Default', $store_prefix = 'Local' ) {
		
	}

	public function getFile( $fileContentDTO, $file_bucket = 'Default', $store_prefix = 'Local' ) {
		
	}

	public function getFileMeta( $fileContentDTO, $file_bucket = 'Default', $store_prefix = 'Local' ) {
		
	}

	public function moveFile( $fileContentDTO, $src_file_bucket = 'Default', $src_store_prefix = 'Local',
		$dest_file_bucket = 'Default', $dest_store_prefix = 'Local' ) {
			
	}

	public function storeFile( $fileContentDTO, $file_bucket = 'Default', $store_prefix = 'Local' ) {
		// $data_store_path = eGlooConfiguration::getDataStorePath();
		// $client_id = eGlooConfiguration::getCustomVariable('client_id');
		// 
		// $data_store_image_path = $data_store_path . '/client/images/' . $imageType . '/';
		// 
		// $full_data_store_image_path = $data_store_image_path . $imageTableID .
		// 	'_ref' . $referenceTableID . '_ins' . $imageInstanceID . '.' . $imageFileTypeExtension;
		// 
		// if ( !is_writable( $data_store_image_path ) ) {
		// 	try {
		// 		$mode = 0777;
		// 		$recursive = true;
		// 
		// 		mkdir( $data_store_image_path, $mode, $recursive );
		// 	} catch (Exception $e){
		// 		echo_r($e->getMessage());
		// 	}
		// }
		// 
		// copy($imagePath, $full_data_store_image_path);
		// 
		// return $full_data_store_image_path;
	}

	public function storeUploadedFile( $fileContentDTO, $file_bucket = 'Default', $store_prefix = 'Local' ) {
		$mimeType = $fileContentDTO->getFileMIMEType();
		$localID = $fileContentDTO->getFileLocalID();
		$mod = $fileContentDTO->getFileMod();
		// Going to refactor this out later...
		$category = 'Generic';

		$data_store_file_folder_path = eGlooConfiguration::getDataStorePath() . '/' . $store_prefix . '/Upload/' .
			$category . '/' . $file_bucket . '/' . $mod . '/';

		$extension = '';

		switch( $mimeType ) {
			case 'image/jpeg' :
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

		copy($fileContentDTO->getFilePath(), $data_store_file_path);

		return $data_store_file_path;
	}

	// Prefix Methods
	// TBD

}
