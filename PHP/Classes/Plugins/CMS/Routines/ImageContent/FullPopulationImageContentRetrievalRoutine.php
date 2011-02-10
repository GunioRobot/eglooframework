<?php
/**
 * FullPopulationImageContentRetrievalRoutine Class File
 *
 * Contains the class definition for the FullPopulationImageContentRetrievalRoutine
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
 * FullPopulationImageContentRetrievalRoutine
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class FullPopulationImageContentRetrievalRoutine extends RetrievalRoutine {

	public function retrieveContent( $imageDTO, $storage_method = 'egDataStore' ) {
		// Copied from storage pattern, reverse this or whatever
		// TODO get path on FS

		// TODO update image location entry in DB


		// TODO Synchronize content across web servers

		// TODO update CDN entry

		if ( $storage_method === 'egDataStore' ) {
			$this->retrieveContentFromeGlooDataStore( $imageDTO );
		}
	}

	private function retrieveContentFromeGlooDataStore( $imageDTO ) {
		
	}
}

