<?php
/**
 * ImageRawFileRequestProcessor Class File
 *
 * Contains the class definition for the ImageRawFileRequestProcessor, a
 * subclass of the RequestProcessor abstract class.
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
 * @package RequestProcessing
 * @version 1.0
 */

/**
 * ImageRawFileRequestProcessor
 * 
 * Handles client requests to retrieve image files from the server
 * 
 * @package RequestProcessing
 * @subpackage RequestProcessors
 */
class ImageRawFileRequestProcessor extends RequestProcessor {

    /**
     * Concrete implementation of the abstract RequestProcessor method
     * processRequest().
     * 
     * This method handles processing of the incoming client request.  Its
     * primary function is to establish the deployment environment (dev, test,
     * production) and the current localization, and to then parse the correct
     * template(s) in order to output the requested image file.
     * 
     * Caching headers are formed to indicate the length of time the cache of
     * the image file is valid, as well as setting the vary on user-agent in
     * order to inform Squid of how it should be issuing cached output to
     * clients when short-circuiting Apache and the eGloo PHP framework.
     * 
     * @access public
     */
    public function processRequest() {
		eGlooLogger::writeLog( eGlooLogger::DEBUG, 'ImageRawFileRequestProcessor: Entered processRequest()' );

		$file_name = $this->requestInfoBean->getGET( 'image_name' );
		eGlooLogger::writeLog( eGlooLogger::DEBUG, 'ImageRawFileRequestProcessor: Looking up image ' . $file_name );

		$app_path = eGlooConfiguration::getApplicationsPath() . '/' . eGlooConfiguration::getApplicationPath() .
			'/InterfaceBundles/' . eGlooConfiguration::getUIBundleName() . '/Images';

		eGlooLogger::writeLog( eGlooLogger::DEBUG, 'ImageRawFileRequestProcessor: Checking path ' . $app_path . '/' . $file_name );

		if ( file_exists( $app_path . '/' . $file_name ) ) {
			$imageMIMEType = '';

			try {
				$info = new finfo( FILEINFO_MIME, '/usr/share/file/magic' );
				$imageMIMEType = $info->file( $app_path . '/' . $file_name );
			} catch (Exception $e) {
				// Just ignore this for now
			}

			if ( file_exists($app_path . '/' . $file_name) && is_file($app_path . '/' . $file_name) ) {
				$output = file_get_contents( $app_path . '/' . $file_name );
				$length = strlen($output);

				header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
				header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
				header( 'Content-type: ' . $imageMIMEType );
				header( 'Content-Length: ' . $length);

				echo $output;
			} else {
				eGlooHTTPResponse::issueRaw404Response();
			}

			if (eGlooConfiguration::getDeployment() == eGlooConfiguration::PRODUCTION || eGlooConfiguration::getUseHotFileImageClustering() ) {
				$matches = array();
				preg_match('~^(.*)?/([^/]*)$~', $file_name, $matches);

				if (!empty($matches)) {
					$cached_file_path = $matches[1] . '/';
				} else {
					$cached_file_path = '';
				}

				if ( !is_writable( eGlooConfiguration::getWebRoot() . 'images/' . $cached_file_path ) ) {
					try {
						$mode = 0777;
						$recursive = true;

						mkdir( eGlooConfiguration::getWebRoot() . 'images/' . $cached_file_path, $mode, $recursive );
					} catch (Exception $e){
						// TODO figure out what to do here
					}
				}

				if ( !copy($app_path . '/' . $file_name, eGlooConfiguration::getWebRoot() . 'images/' . $file_name ) ) {
					throw new Exception( 'File copy failed from ' . $app_path . '/' . $file_name . ' to ' .
						eGlooConfiguration::getWebRoot() . 'images/' . $file_name );
				}
			}
		} else if ( ( $data_store_path = $this->getDataStorePath( $file_name ) ) !== null ) {
			$imageMIMEType = '';

			try {
				$info = new finfo( FILEINFO_MIME, '/usr/share/file/magic' );
				$imageMIMEType = $info->file( $data_store_path );
			} catch (Exception $e) {
				// Just ignore this for now
			}

			if ( file_exists($data_store_path) && is_file($data_store_path) ) {
				$output = file_get_contents( $data_store_path );
				$length = strlen($output);

				header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
				header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
				header( 'Content-type: ' . $imageMIMEType );
				header( 'Content-Length: ' . $length);

				echo $output;
			} else {
				eGlooHTTPResponse::issueRaw404Response();
			}

			if (eGlooConfiguration::getDeployment() == eGlooConfiguration::PRODUCTION || eGlooConfiguration::getUseHotFileImageClustering() ) {
				$matches = array();
				preg_match('~^(.*)?/([^/]*)$~', $file_name, $matches);

				if (!empty($matches)) {
					$cached_file_path = $matches[1] . '/';
				} else {
					$cached_file_path = '';
				}

				if ( !is_writable( eGlooConfiguration::getWebRoot() . 'images/' . $cached_file_path ) ) {
					try {
						$mode = 0777;
						$recursive = true;

						mkdir( eGlooConfiguration::getWebRoot() . 'images/' . $cached_file_path, $mode, $recursive );
					} catch (Exception $e){
						// TODO figure out what to do here
					}
				}

				if ( !copy($data_store_path, eGlooConfiguration::getWebRoot() . 'images/' . $file_name ) ) {
					throw new Exception( 'File copy failed from ' . $data_store_path . ' to ' .
						eGlooConfiguration::getWebRoot() . 'images/' . $file_name );
				}
			}
		} else {
			header( eGlooHTTPRequest::getServerProtocol() . ' 404 Not Found ' );
		}

		eGlooLogger::writeLog( eGlooLogger::DEBUG, 'ImageRawFileRequestProcessor: Exiting processRequest()' );
    }

	protected function getDataStorePath( $file_name ) {
		$retVal = null;

		$contentDAOFactory = ContentDAOFactory::getInstance();
		$imageContentDAO = $contentDAOFactory->getImageContentDAO();

		$matches = array();
		preg_match('~^(.*)?/([^/]*)$~', $file_name, $matches);

		$imageFileMod = null;
		$imageFileID = null;
		$imageBucket = null;
		$mime_type = null;

		if ( !empty($matches) && isset($matches[1]) && isset($matches[2]) ) {
			$fileModChunks = explode('/', $matches[1]);
			$imageBucket = ucfirst( array_shift( $fileModChunks ) );

			foreach( $fileModChunks as $key => $value ) {
				$fileModChunks[$key] = ucfirst($value);
			}

			$imageFileMod = implode('/', $fileModChunks);
			$imageFileName = $matches[2];

			$imageFileExtension = null;
			$imageFileID = null;

			$extension_match = array();
			preg_match( '~^([^.]+)\.([^.]+)$~', $imageFileName, $extension_match );

			if ( !empty($extension_match) && isset($extension_match[1]) && isset($extension_match[2]) ) {
				$imageFileID = $extension_match[1];
				$imageFileExtension = $extension_match[2];
			} else {
				// TODO throw relevant exception or something
			}
		} else {
			$imageFileExtension = null;
			$imageFileID = null;

			$extension_match = array();
			preg_match( '~^([^.]+)\.([^.]+)$~', $file_name, $extension_match );

			if ( !empty($extension_match) && isset($extension_match[1]) && isset($extension_match[2]) ) {
				$imageFileID = $extension_match[1];
				$imageFileExtension = $extension_match[2];
			} else {
				// TODO throw relevant exception or something
			}
		}

		$imageContentDBDAO = ContentDAOFactory::getInstance()->getImageContentDAO( 'egPrimary' );

		$mime_type = $imageContentDBDAO->getMIMETypeFromExtension( $imageFileExtension );

		$imageContentDTO = new ImageContentDTO();
		$imageContentDTO->setImageFileLocalID($imageFileID);
		$imageContentDTO->setImageFileMod($imageFileMod);
		$imageContentDTO->setImageBucket($imageBucket);
		$imageContentDTO->setImageMIMEType( $mime_type );

		$data_store_image_url = $imageContentDBDAO->getImageStorePath( $imageContentDTO );

		if ( $data_store_image_url !== null && is_string($data_store_image_url) && trim($data_store_image_url) !== '' ) {
			$retVal = eGlooConfiguration::getDataStorePath() . '/' .  $data_store_image_url;
		}

		return $retVal;
	}

}

