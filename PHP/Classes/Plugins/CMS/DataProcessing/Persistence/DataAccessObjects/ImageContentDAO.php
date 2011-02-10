<?php
/**
 * ImageContentDAO Class File
 *
 * Contains the class definition for the ImageContentDAO
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
 * ImageContentDAO
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
abstract class ImageContentDAO extends AbstractDAO {

	public function CRUDCreate( $formDTO ) {
		
	}

	public function CRUDRead( $formDTO ) {
		
	}

	public function CRUDUpdate( $formDTO ) {
		
	}

	public function CRUDDestroy( $formDTO ) {
		
	}

	// Bucket Methods
	// TBD

	// Image Methods
	abstract public function copyImage( $imageContentDTO, $src_image_bucket = 'Default', $src_store_prefix = 'Local', $src_zone = 'Uploaded',
		$dest_image_bucket = 'Default', $dest_store_prefix = 'Local', $dest_zone = 'Master' );

	abstract public function deleteImage( $imageContentDTO, $image_bucket = 'Default', $store_prefix = 'Local', $zone = 'Master' );

	abstract public function getImage( $imageContentDTO, $image_bucket = 'Default', $store_prefix = 'Local', $zone = 'Master' );

	abstract public function getImageMeta( $imageContentDTO, $image_bucket = 'Default', $store_prefix = 'Local', $zone = 'Master' );

	abstract public function moveImage( $imageContentDTO, $src_image_bucket = 'Default', $src_store_prefix = 'Local', $src_zone = 'Uploaded',
		$dest_image_bucket = 'Default', $dest_store_prefix = 'Local', $dest_zone = 'Master' );

	abstract public function storeImage( $imageContentDTO, $image_bucket = 'Default', $store_prefix = 'Local', $zone = 'Uploaded' );

	// Prefix Methods
	// TBD

	// Utility Methods
	public function getExtensionFromMIMEType( $mimeType ) {
		$retVal = '';

		switch( $mimeType ) {
			case 'image/gif' :
				$retVal = 'gif';
				break;
			case 'image/jpeg' :
				$retVal = 'jpg';
				break;
			case 'image/png' :
				$retVal = 'png';
				break;
			default :
				$retVal = 'unknown';
				break;
		}

		return $retVal;
	}

	public function getMIMETypeFromExtension( $extension ) {
		$retVal = null;

		$extension = strtolower( $extension );

		switch( $extension ) {
			case 'gif' :
				$retVal = 'image/gif';
				break;
			case 'jpeg' :
				$retVal = 'image/jpeg';
				break;
			case 'jpg' :
				$retVal = 'image/jpeg';
				break;
			case 'png' :
				$retVal = 'image/png';
				break;
			default :
				$retVal = null;
				break;
		}

		return $retVal;
	}

}

