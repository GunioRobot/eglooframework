<?php
/**
 * CloudFrontGenericFileContentDAO Class File
 *
 * Contains the class definition for the CloudFrontGenericFileContentDAO
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
 * CloudFrontGenericFileContentDAO
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class CloudFrontGenericFileContentDAO extends GenericFileContentDAO implements ContentDistributionNetworkDAOInterface {

	// Here's a thought - 'Default' bucket == egCDNPrimary bucket

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
		
	}

	// Prefix Methods
	// TBD

}

