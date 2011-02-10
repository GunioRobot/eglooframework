<?php
/**
 * DistributedContentDAOInterface Interface File
 *
 * Contains the interface definition for the DistributedContentDAOInterface
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
 * DistributedContentDAOInterface
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
interface DistributedContentDAOInterface {

	/**
	 * Returns protected class member $_name
	 *
	 * @return string Name of CDN connection
	 */
	public function getName();

	/**
	 * Sets protected class member $_name
	 *
	 * @param name string Name of CDN connection
	 */
	public function setName( $name );

	/**
	 * Returns protected class member $_bucket
	 *
	 * @return string Bucket that this connection is using
	 */
	public function getBucket();

	/**
	 * Sets protected class member $_bucket
	 *
	 * @param bucket string Bucket that this connection is using
	 */
	public function setBucket( $bucket );

	/**
	 * Returns protected class member $_distribution_url
	 *
	 * @return string URL of the distribution this connection is for
	 */
	public function getDistributionUrl();

	/**
	 * Sets protected class member $_distribution_url
	 *
	 * @param distribution_url string URL of the distribution this connection is for
	 */
	public function setDistributionUrl( $distribution_url );

	/**
	 * Returns protected class member $_access_key_id
	 *
	 * @return string Access Key ID for this connection
	 */
	public function getAccessKeyID();

	/**
	 * Sets protected class member $_access_key_id
	 *
	 * @param access_key_id string Access Key ID for this connection
	 */
	public function setAccessKeyID( $access_key_id );

	/**
	 * Returns protected class member $_secret_access_key
	 *
	 * @return string Secret access key for this connection
	 */
	public function getSecretAccessKey();

	/**
	 * Sets protected class member $_secret_access_key
	 *
	 * @param secret_access_key string Secret access key for this connection
	 */
	public function setSecretAccessKey( $secret_access_key );

	// Distribution URL Methods
	public function getImageDistributionURL( $imageContentDTO, $connection_name = 'egCDNPrimary',
		$image_bucket = 'Default', $store_prefix = 'Local', $zone = 'Uploaded'  );

	public function setImageDistributionURL( $imageContentDTO, $distribution_image_url, $connection_name = 'egCDNPrimary',
		$image_bucket = 'Default', $store_prefix = 'Local', $zone = 'Uploaded'  );

}

