<?php
/**
 * CloudFrontImageContentDAO Class File
 *
 * Contains the class definition for the CloudFrontImageContentDAO
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
 * CloudFrontImageContentDAO
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class CloudFrontImageContentDAO extends ImageContentDAO implements ContentDistributionNetworkDAOInterface {

	// Here's a thought - 'Default' bucket == egCDNPrimary bucket

	/**
	 * @var string Name of CDN connection
	 */
	protected $_name = null;

	/**
	 * Returns protected class member $_name
	 *
	 * @return string Name of CDN connection
	 */
	public function getName() {
		return $this->_name;
	}

	/**
	 * Sets protected class member $_name
	 *
	 * @param name string Name of CDN connection
	 */
	public function setName( $name ) {
		$this->_name = $name;
	}

	/**
	 * @var string Bucket that this connection is using
	 */
	protected $_bucket = null;

	/**
	 * Returns protected class member $_bucket
	 *
	 * @return string Bucket that this connection is using
	 */
	public function getBucket() {
		return $this->_bucket;
	}

	/**
	 * Sets protected class member $_bucket
	 *
	 * @param bucket string Bucket that this connection is using
	 */
	public function setBucket( $bucket ) {
		$this->_bucket = $bucket;
	}

	/**
	 * @var string URL of the distribution this connection is for
	 */
	protected $_distribution_url = null;

	/**
	 * Returns protected class member $_distribution_url
	 *
	 * @return string URL of the distribution this connection is for
	 */
	public function getDistributionUrl() {
		return $this->_distribution_url;
	}

	/**
	 * Sets protected class member $_distribution_url
	 *
	 * @param distribution_url string URL of the distribution this connection is for
	 */
	public function setDistributionUrl( $distribution_url ) {
		$this->_distribution_url = $distribution_url;
	}

	/**
	 * @var array Array of headers to send to CloudFront for the content
	 */
	protected $_headers = null;

	/**
	 * Returns protected class member $_headers
	 *
	 * @return array Array of headers to send to CloudFront for the content
	 */
	public function getHeaders() {
		return $this->_headers;
	}

	/**
	 * Sets protected class member $_headers
	 *
	 * @param headers array Array of headers to send to CloudFront for the content
	 */
	public function setHeaders( $headers ) {
		$this->_headers = $headers;
	}


	/**
	 * @var string Access Key ID for this connection
	 */
	protected $_access_key_id = null;

	/**
	 * Returns protected class member $_access_key_id
	 *
	 * @return string Access Key ID for this connection
	 */
	public function getAccessKeyID() {
		return $this->_access_key_id;
	}

	/**
	 * Sets protected class member $_access_key_id
	 *
	 * @param access_key_id string Access Key ID for this connection
	 */
	public function setAccessKeyID( $access_key_id ) {
		$this->_access_key_id = $access_key_id;
	}

	/**
	 * @var string Secret access key for this connection
	 */
	protected $_secret_access_key = null;

	/**
	 * Returns protected class member $_secret_access_key
	 *
	 * @return string Secret access key for this connection
	 */
	public function getSecretAccessKey() {
		return $this->_secret_access_key;
	}

	/**
	 * Sets protected class member $_secret_access_key
	 *
	 * @param secret_access_key string Secret access key for this connection
	 */
	public function setSecretAccessKey( $secret_access_key ) {
		$this->_secret_access_key = $secret_access_key;
	}

	public function __construct( $connection_name = 'egCDNPrimary' ) {
		parent::__construct( $connection_name );

		$connection_info = eGlooConfiguration::getCDNConnectionInfo( $connection_name );

		$this->_name = $connection_info['name'];
		$this->_bucket = $connection_info['bucket'];
		$this->_distribution_url = $connection_info['distribution_url'];
		$this->_access_key_id = $connection_info['access_key_id'];
		$this->_secret_access_key = $connection_info['secret_access_key'];
	}

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

	public function moveImage( $imageContentDTO, $src_image_bucket = 'Default', $src_store_prefix = 'Local', $src_zone = 'Uploaded',
		$dest_image_bucket = 'Default', $dest_store_prefix = 'Local', $dest_zone = 'Master' ) {
			
	}

	public function storeImage( $imageContentDTO, $image_bucket = 'Default', $store_prefix = 'Local', $zone = 'Uploaded' ) {
		// Here's a thought - 'Default' bucket == egCDNPrimary bucket
		$retVal = null;

		$s3Obj = new S3( $this->_access_key_id,  $this->_secret_access_key );

		$contentDAOFactory = ContentDAOFactory::getInstance();

		$imageContentDBDAO = $contentDAOFactory->getImageContentDAO( 'egPrimary' );
		$current_distribution_url = $imageContentDBDAO->getImageDistributionURL( $imageContentDTO );

		if ( $current_distribution_url !== null ) {
			$s3Obj->deleteObject( $this->_bucket, $imageContentDTO->getImageURI() );
		}

		$data_store_file_path = eGlooConfiguration::getDataStorePath() . '/' . $imageContentDTO->getImageFilePath();

		$inputFile = $s3Obj->inputFile( $data_store_file_path );

		$request_headers = array(
		  'Cache-Control' => 'max-age=1209600, private, must-revalidate',
		  'Expires' => date('D, d M Y H:i:s T', strtotime('+2 hour')),
		);

		$result = $s3Obj->putObject( $inputFile, $this->_bucket, $imageContentDTO->getImageURI(), S3::ACL_PUBLIC_READ, array(), $request_headers );

		$retVal = $this->_distribution_url . '/' . $imageContentDTO->getImageURI();

		return $retVal;
	}

	// Prefix Methods
	// TBD

}

