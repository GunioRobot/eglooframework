<?php
/**
 * MySQLiOOPImageContentDAO Class File
 *
 * Contains the class definition for the MySQLiOOPImageContentDAO
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
 * MySQLiOOPImageContentDAO
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class MySQLiOOPImageContentDAO extends ImageContentDAO implements CDNManagementDBDAOInterface {

	// Bucket Methods
	// TBD

	// Image Methods
	public function copyImage( $imageContentDTO, $src_image_bucket = 'Default', $src_store_prefix = 'Local', $src_zone = 'Uploaded',
		$dest_image_bucket = 'Default', $dest_store_prefix = 'Local', $dest_zone = 'Master' ) {
			
	}

	public function deleteImage( $imageContentDTO, $image_bucket = 'Default', $store_prefix = 'Local', $zone = 'Master' ) {
		
	}

	public function getImage( $imageContentDTO, $image_bucket = 'Default', $store_prefix = 'Local', $zone = 'Master' ) {
		
	}

	public function getImageMeta( $imageContentDTO, $image_bucket = 'Default', $store_prefix = 'Local', $zone = 'Master' ) {
		
	}

	public function getImageStorePath( $imageContentDTO, $image_bucket = 'Default', $store_prefix = 'Local', $zone = 'Master' ) {
		// $retVal = null;
		// 
		// $data_store_file_folder_path = eGlooConfiguration::getDataStorePath() . '/' . $store_prefix . '/' . $zone . '/' .
		// 	$category . '/' . $image_bucket . '/' . $mod . '/';
		// 
		// return $retVal;
	}

	public function getUploadedImageStorePath( $imageContentDTO, $image_bucket = 'Default', $store_prefix = 'Local' ) {
		// $retVal = null;
		// 
		// $mimeType = $imageContentDTO->getImageMIMEType();
		// $localID = $imageContentDTO->getImageFileLocalID();
		// $mod = $imageContentDTO->getImageFileMod();
		// 
		// $data_store_file_folder_path = eGlooConfiguration::getDataStorePath() . '/' . $store_prefix . '/Upload/' .
		// 	$category . '/' . $image_bucket . '/' . $mod . '/';
		// 
		// return $retVal;
	}

	public function moveImage( $imageContentDTO, $src_image_bucket = 'Default', $src_store_prefix = 'Local', $src_zone = 'Uploaded',
		$dest_image_bucket = 'Default', $dest_store_prefix = 'Local', $dest_zone = 'Master' ) {
			
	}

	public function storeMasterImage( $imageContentDTO, $image_bucket = 'Default', $store_prefix = 'Local' ) {
		// return $this->storeImage( $imageContentDTO, $image_bucket, $store_prefix, 'Master' );
	}

	public function storeUploadedImage( $imageContentDTO, $image_bucket = 'Default', $store_prefix = 'Local' ) {
		// return $this->storeImage( $imageContentDTO, $image_bucket, $store_prefix, 'Uploaded' );
	}

	public function storeImage( $imageContentDTO, $image_bucket = 'Default', $store_prefix = 'Local', $zone = 'Uploaded' ) {
		// $mimeType = $imageContentDTO->getImageMIMEType();
		// $localID = $imageContentDTO->getImageFileLocalID();
		// $mod = $imageContentDTO->getImageFileMod();
		// // Going to refactor this out later...
		// $category = 'Images';
		// 
		// $external_path = $category . '/' . $image_bucket . '/' . $mod . '/';
		// 
		// $data_store_file_folder_path = eGlooConfiguration::getDataStorePath() . '/' . $store_prefix . '/' . $zone . '/';
		// 
		// if ( !is_writable( $data_store_file_folder_path . $external_path ) ) {
		// 	try {
		// 		$mode = 0777;
		// 		$recursive = true;
		// 
		// 		mkdir( $data_store_file_folder_path . $external_path, $mode, $recursive );
		// 	} catch (Exception $e){
		// 		echo_r($e->getMessage());
		// 	}
		// }
		// 
		// $extension = $this->getExtensionFromMIMEType( $mimeType );
		// 
		// $external_path .= $localID . '.' . $extension;
		// 
		// $data_store_file_path = $data_store_file_folder_path . $external_path;
		// 
		// 
		// // die_r($imageContentDTO);
		// // TODO make sure the file uploaded properly, no errors
		// // echo_r($imageContentDTO->getImageFilePath());
		// // echo_r($data_store_file_path);
		// copy($imageContentDTO->getImageFilePath(), $data_store_file_path);
		// 
		// $imageContentDTO->setImageBucket( $image_bucket );
		// $imageContentDTO->setImageStore( $store_prefix );
		// $imageContentDTO->setImageZone( $zone );
		// 
		// return strtolower($external_path);
	}

	// Prefix Methods
	// TBD

}

