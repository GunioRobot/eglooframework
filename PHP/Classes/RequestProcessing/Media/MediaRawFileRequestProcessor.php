<?php
/**
 * MediaRawFileRequestProcessor Class File
 *
 * Contains the class definition for the MediaRawFileRequestProcessor, a
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
 * MediaRawFileRequestProcessor
 * 
 * Handles client requests to retrieve media files from the server
 * 
 * @package RequestProcessing
 * @subpackage RequestProcessors
 */
class MediaRawFileRequestProcessor extends RequestProcessor {

    /**
     * Concrete implementation of the abstract RequestProcessor method
     * processRequest().
     * 
     * This method handles processing of the incoming client request.  Its
     * primary function is to establish the deployment environment (dev, test,
     * production) and the current localization, and to then parse the correct
     * template(s) in order to output the requested media file.
     * 
     * Caching headers are formed to indicate the length of time the cache of
     * the media file is valid, as well as setting the vary on user-agent in
     * order to inform Squid of how it should be issuing cached output to
     * clients when short-circuiting Apache and the eGloo PHP framework.
     * 
     * @access public
     */
    public function processRequest() {
		eGlooLogger::writeLog( eGlooLogger::DEBUG, 'MediaRawFileRequestProcessor: Entered processRequest()' );

		$file_name = $this->requestInfoBean->getGET( 'media_name' );
		eGlooLogger::writeLog( eGlooLogger::DEBUG, 'MediaRawFileRequestProcessor: Looking up media ' . $file_name );

		$app_path = eGlooConfiguration::getApplicationsPath() . '/' . eGlooConfiguration::getApplicationPath() .
			'/InterfaceBundles/' . eGlooConfiguration::getUIBundleName() .'/Media';

		if ( file_exists( $app_path . '/' . $file_name ) ) {
			$mediaMIMEType = '';

			try {
				$info = new finfo( FILEINFO_MIME, '/usr/share/file/magic' );
				$mediaMIMEType = $info->file( $app_path . '/' . $file_name );
			} catch (Exception $e) {
				// Just ignore this for now
			}

			$output = file_get_contents( $app_path . '/' . $file_name );
			$length = strlen($output);

			// header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
			// header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
			header( 'Content-type: ' . $mediaMIMEType );
			header( 'Content-Length: ' . $length);

			echo $output;

			if (eGlooConfiguration::getDeployment() == eGlooConfiguration::PRODUCTION ) {
				$matches = array();
				preg_match('~^(.*)?/([^/]*)$~', $file_name, $matches);

				if (!empty($matches)) {
					$cached_file_path = $matches[1] . '/';
				} else {
					$cached_file_path = '';
				}

				if ( !is_writable( eGlooConfiguration::getWebRoot() . 'media/' . $cached_file_path ) ) {
					try {
						$mode = 0777;
						$recursive = true;

						mkdir( eGlooConfiguration::getWebRoot() . 'media/' . $cached_file_path, $mode, $recursive );
					} catch (Exception $e){
						// TODO figure out what to do here
					}
				}

				if ( !copy($app_path . '/' . $file_name, eGlooConfiguration::getWebRoot() . 'media/' . $file_name ) ) {
					throw new Exception( 'File copy failed from ' . $app_path . '/' . $file_name . ' to ' .
						eGlooConfiguration::getWebRoot() . 'media/' . $file_name );
				}
			}
		} else {
			header( eGlooHTTPRequest::getServerProtocol() . ' 404 Not Found ' );
		}

		eGlooLogger::writeLog( eGlooLogger::DEBUG, 'MediaRawFileRequestProcessor: Exiting processRequest()' );
    }

}

