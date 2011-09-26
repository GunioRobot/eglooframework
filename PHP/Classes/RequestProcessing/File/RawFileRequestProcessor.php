<?php
/**
 * RawFileRequestProcessor Class File
 *
 * Contains the class definition for the RawFileRequestProcessor, a
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
 * RawFileRequestProcessor
 * 
 * Handles client requests to retrieve files from the server
 * 
 * @package RequestProcessing
 * @subpackage RequestProcessors
 */
class RawFileRequestProcessor extends RequestProcessor {

    /**
     * Concrete implementation of the abstract RequestProcessor method
     * processRequest().
     * 
     * This method handles processing of the incoming client request.  Its
     * primary function is to establish the deployment environment (dev, test,
     * production) and the current localization, and to then parse the correct
     * template(s) in order to output the requested file file.
     * 
     * Caching headers are formed to indicate the length of time the cache of
     * the file file is valid, as well as setting the vary on user-agent in
     * order to inform Squid of how it should be issuing cached output to
     * clients when short-circuiting Apache and the eGloo PHP framework.
     * 
     * @access public
     */
    public function processRequest() {
		eGlooLogger::writeLog( eGlooLogger::DEBUG, 'RawFileRequestProcessor: Entered processRequest()' );

		$file_name = $this->requestInfoBean->getGET( 'file_name' );
		eGlooLogger::writeLog( eGlooLogger::DEBUG, 'RawFileRequestProcessor: Looking up file ' . $file_name );

		$app_path = eGlooConfiguration::getApplicationsPath() . '/' . eGlooConfiguration::getApplicationPath() .
			'/InterfaceBundles/' . eGlooConfiguration::getUIBundleName() . '/Files';

		if ( file_exists( $app_path . '/' . $file_name ) ) {
			$fileMIMEType = '';

			try {
				$info = new finfo( FILEINFO_MIME, '/usr/share/file/magic' );
				$fileMIMEType = $info->file( $app_path . '/' . $file_name );
			} catch (Exception $e) {
				// Just ignore this for now
			}

			// This is some ghetto hacks and needs to be optimized out like loco
			if (strstr($file_name, '.htc')) {
				$fileMIMEType = 'text/x-component';
			}
/*
Date	Fri, 20 Nov 2009 16:08:31 GMT
Server	Apache/2.2.14 (Unix) DAV/2
Last-Modified	Tue, 17 Nov 2009 21:41:58 GMT
Etag	"196764a-17f41f-47897fedaf580"
Accept-Ranges	bytes
Content-Length	1569823
Keep-Alive	timeout=5, max=97
Connection	Keep-Alive
Content-Type	file/png
Cache-Control	max-age=86400
*/

			$output = file_get_contents( $app_path . '/' . $file_name );

			header( 'Content-type: ' . $fileMIMEType );
			echo $output;
		} else {
			header( eGlooHTTPRequest::getServerProtocol() . ' 404 Not Found ' );
		}

		eGlooLogger::writeLog( eGlooLogger::DEBUG, 'RawFileRequestProcessor: Exiting processRequest()' );
    }

}

