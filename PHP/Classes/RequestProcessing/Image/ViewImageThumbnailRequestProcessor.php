<?php
/**
 * ViewImageThumbnailRequestProcessor Class File
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
 * @package RequestProcessing
 * @version 1.0
 */

/**
 * ViewImageThumbnailRequestProcessor
 * 
 * Needs to be commented
 * 
 * @package RequestProcessing
 * @subpackage RequestProcessors
 */
class ViewImageThumbnailRequestProcessor extends RequestProcessor {

    public function processRequest() {
        $userID = $_SESSION['USER_ID'];
        $profileID = $_SESSION['MAIN_PROFILE_ID'];

        $imageID = $this->requestInfoBean->getGET( 'imageID' );
        $imageMIMEType = str_replace( '_', '/', $this->requestInfoBean->getGET( 'imageMIMEType' ) );

        $imageDTO = new ImageDTO();

        $imageDTO->setImageFileHash( $imageID );
        $imageDTO->setImageMIMEType( $imageMIMEType );

        $daoFactory = AbstractDAOFactory::getInstance();
        $imageDAO = $daoFactory->getImageDAO();
        $imageDAO->getImage( $userID, $imageDTO );

        $magickWand = NewMagickWand();
        MagickReadImageBlob( $magickWand, $imageDTO->getImageContent() );

        $imageWidth = MagickGetImageWidth( $magickWand );
        $imageHeight = MagickGetImageHeight( $magickWand ); 

        $imageHeightMax = 175;
        $imageWidthMax = 200;

        if ( $imageHeight > $imageHeightMax || $imageWidth > $imageWidthMax ) {
            if ( ( ( $imageWidthMax / $imageWidth) * $imageHeight ) <= $imageHeightMax ) {
                $newHeight = ( ( $imageWidthMax / $imageWidth) * $imageHeight );
                $newWidth = $imageWidthMax;
            } else {
                $newHeight = $imageHeightMax;
                $newWidth = ( ( $imageHeightMax / $imageHeight) * $imageWidth );
            }
        } else {
            $newHeight = $imageHeight;
            $newWidth = $imageWidth;
        }

        MagickResizeImage( $magickWand, $newWidth, $newHeight, MW_LANCZOSFILTER, 0.65 );
        $output = MagickGetImageBlob( $magickWand );
        $outputMIMEType = MagickGetImageMimeType( $magickWand );

        header( 'Content-type: ' . $outputMIMEType );
        echo $output;
    }
}
?>