<?php
/**
 * ImageJPEG Class File
 *
 * Needs to be commented
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
 * @package Content
 * @version 1.0
 */

/**
 * ImageJPEG
 * 
 * Needs to be commented
 *
 * @package Content
 */
class ImageJPEG extends Image {
    
    const MIMETYPE = 'image/jpeg';
    
    private $responseCode;

    // Disable Caching
    private $cacheControl = 'Cache-Control: no-cache, must-revalidate';  // HTTP/1.1
    private $cacheExpireDate = 'Expires: Mon, 26 Jul 1997 05:00:00 GMT'; // Date in the past
    
    private $contentType = 'Content-type: application/pdf';              // We'll be outputting a PDF
    private $contentDisposition = 
        'Content-Disposition: attachment; filename="downloaded.pdf"';    // It will be called downloaded.pdf


    private $_objectContent;

    public function __construct( $imageID ) {
        $this->_objectContent = file_get_contents( './images/Randy1.jpg' );
    }

    public function getHeader() {
        header('Content-type: image/jpeg');
    }
    
    public function getContent() {
        return $this->_objectContent;
    }
    
}
 
?>
