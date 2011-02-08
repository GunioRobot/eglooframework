<?php
/**
 * FullPopulationImageContentStorageRoutine Class File
 *
 * Contains the class definition for the FullPopulationImageContentStorageRoutine
 * 
 * Copyright 2011 eGloo LLC
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
 * @copyright 2011 eGloo LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package $package
 * @subpackage $subpackage
 * @version 1.0
 */

/**
 * FullPopulationImageContentStorageRoutine
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class FullPopulationImageContentStorageRoutine extends StorageRoutine {

	public function storeContent( $imageDTO, $storage_method = 'egDataStore' ) {
		$contentDAOFactory = ContentDAOFactory::getInstance();

		if ( $storage_method === 'egDataStore' ) {
			// Store image in data store on FS
			$data_store_path = $this->storeContentIneGlooDataStore( $imageDTO );

			// Update image location entry in DB
			$imageContentDBDAO = $contentDAOFactory->getImageContentDAO( 'egPrimary' );
			$imageContentDBDAO->storeUploadedImage( $imageDTO );
		}

		// TODO Synchronize content across web servers

		$distribution_image_url = null;

		// Update CDN entry if using a CDN
		if ( eGlooConfiguration::getDeploymentType() == eGlooConfiguration::PRODUCTION && eGlooConfiguration::getUseCDN() ) {
			$imageContentCDNDAO = $contentDAOFactory->getImageContentDAO( 'egCDNPrimary' );

			$distribution_image_url = $imageContentCDNDAO->storeImage( $imageDTO );
		}

		if ( $distribution_image_url !== null ) {
			// Update image distribution url entry in DB
			$imageContentDBDAO = $contentDAOFactory->getImageContentDAO( 'egPrimary' );
			$imageContentDBDAO->setImageDistributionURL( $imageDTO, $distribution_image_url );
		}
	}

	private function storeContentIneGlooDataStore( $imageDTO ) {
		$data_store_path = null;

		$contentDAOFactory = ContentDAOFactory::getInstance();
		$imageContentDAO = $contentDAOFactory->getImageContentDAO();

		if ( !( $data_store_path = $imageContentDAO->storeUploadedImage( $imageDTO ) ) ) {
			throw new ErrorException( 'No valid storage path returned' );
		}

		return $data_store_path;
	}

}

