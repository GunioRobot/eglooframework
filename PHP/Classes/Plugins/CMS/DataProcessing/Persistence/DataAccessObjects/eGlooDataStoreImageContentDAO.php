<?php
/**
 * eGlooDataStoreImageContentDAO Class File
 *
 * Contains the class definition for the eGlooDataStoreImageContentDAO
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
 * eGlooDataStoreImageContentDAO
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class eGlooDataStoreImageContentDAO extends ImageContentDAO {

	// Bucket Methods
	// TBD

	// Image Methods
	public function copyImage( $imageContentDTO, $src_image_bucket = 'Default', $src_store_prefix = 'Local', $src_zone = 'Upload',
		$dest_image_bucket = 'Default', $dest_store_prefix = 'Local', $dest_zone = 'Master' ) {
			
	}

	public function deleteImage( $imageContentDTO, $image_bucket = 'Default', $store_prefix = 'Local', $zone = 'Master' ) {
		
	}

	public function getImage( $imageContentDTO, $image_bucket = 'Default', $store_prefix = 'Local', $zone = 'Master' ) {
		
	}

	public function getImageMeta( $imageContentDTO, $image_bucket = 'Default', $store_prefix = 'Local', $zone = 'Master' ) {
		
	}

	public function getImageStorePath( $imageContentDTO, $image_bucket = 'Default', $store_prefix = 'Local', $zone = 'Master' ) {
		
	}

	public function moveImage( $imageContentDTO, $src_image_bucket = 'Default', $src_store_prefix = 'Local', $src_zone = 'Upload',
		$dest_image_bucket = 'Default', $dest_store_prefix = 'Local', $dest_zone = 'Master' ) {
			
	}

	public function storeMasterImage( $imageContentDTO, $image_bucket = 'Default', $store_prefix = 'Local' ) {
		return $this->storeImage( $imageContentDTO, $image_bucket, $store_prefix, 'Master' );
	}

	public function storeUploadedImage( $imageContentDTO, $image_bucket = 'Default', $store_prefix = 'Local' ) {
		return $this->storeImage( $imageContentDTO, $image_bucket, $store_prefix, 'Uploaded' );
	}

	public function storeImage( $imageContentDTO, $image_bucket = 'Default', $store_prefix = 'Local', $zone = 'Upload' ) {
		$mimeType = $imageContentDTO->getImageMIMEType();
		$localID = $imageContentDTO->getImageFileLocalID();
		$mod = $imageContentDTO->getImageFileMod();
		// Going to refactor this out later...
		$category = 'Images';

		$data_store_file_folder_path = eGlooConfiguration::getDataStorePath() . '/' . $store_prefix . '/' . $zone . '/' .
			$category . '/' . $image_bucket . '/' . $mod . '/';

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

		// die_r($imageContentDTO);
		// TODO make sure the file uploaded properly, no errors
		echo_r($imageContentDTO->getImageFilePath());
		echo_r($data_store_file_path);
		copy($imageContentDTO->getImageFilePath(), $data_store_file_path);

		return $data_store_file_path;

	}

	// Prefix Methods
	// TBD

}

