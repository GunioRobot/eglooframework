<?php
/**
 * ImageRawFileRequestProcessor Class File
 *
 * Contains the class definition for the ImageRawFileRequestProcessor, a
 * subclass of the RequestProcessor abstract class.
 * 
 * Copyright 2009 eGloo, LLC
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
 * @copyright 2009 eGloo, LLC
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

		$app_path = eGlooConfiguration::getApplicationsPath() . '/' . eGlooConfiguration::getApplicationName() .
			'/InterfaceBundles/' . eGlooConfiguration::getUIBundleName() . '/Images';

		if ( file_exists( $app_path . '/' . $file_name ) ) {
			$info = new finfo( FILEINFO_MIME, '/usr/share/file/magic' );
			$mimeType = $info->file( $app_path . '/' . $file_name );


/*
Date	Fri, 20 Nov 2009 16:08:31 GMT
Server	Apache/2.2.14 (Unix) DAV/2
Last-Modified	Tue, 17 Nov 2009 21:41:58 GMT
Etag	"196764a-17f41f-47897fedaf580"
Accept-Ranges	bytes
Content-Length	1569823
Keep-Alive	timeout=5, max=97
Connection	Keep-Alive
Content-Type	image/png
Cache-Control	max-age=86400
*/

			header( 'Content-type: ' . $imageMIMEType );
			echo file_get_contents( $app_path . '/' . $file_name );
		} else {
			header( $_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found ' );
		}

		eGlooLogger::writeLog( eGlooLogger::DEBUG, 'ImageRawFileRequestProcessor: Exiting processRequest()' );
    }

}

