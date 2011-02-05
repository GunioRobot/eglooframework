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
			$data_store_path = $this->storeContentIneGlooDataStore( $imageDTO );

			$imageContentDBDAO = $contentDAOFactory->getImageContentDAO( 'egPrimary' );

			$imageContentDBDAO->storeUploadedImage( $imageDTO );
		}

		// TODO get path on FS

		// TODO update image location entry in DB


		// TODO Synchronize content across web servers

		// TODO update CDN entry
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

