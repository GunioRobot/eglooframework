<?php
/**
 * ImageContentDirector Class File
 *
 * Contains the class definition for the ImageContentDirector
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
 * ImageContentDirector
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class ImageContentDirector extends ContentDirector {

	public function storeContent( $imageDTO, $storage_method = 'egDataStore', $storage_routine = 'FullPopulationImageContentStorageRoutine' ) {
		$storageRoutineObj = null;

		if ( $storage_routine === 'FullPopulationImageContentStorageRoutine' ) {
			$storageRoutineObj = new FullPopulationImageContentStorageRoutine();
		} else {
			throw new ErrorException( 'Unknown storage routine specified' );
		}

		$storageRoutineObj->storeContent( $imageDTO, $storage_method );
	}


	public function retrieveContent( $imageDTO, $storage_method = 'egDataStore', $retrieval_routine = 'FullPopulationImageContentRetrievalRoutine' ) {
		$retrievalRoutineObj = null;

		if ( $retrieval_routine === 'FullPopulationImageContentRetrievalRoutine' ) {
			$retrievalRoutineObj = new FullPopulationImageContentRetrievalRoutine();
		} else {
			throw new ErrorException( 'Unknown retrieval routine specified' );
		}

		$retrievalRoutineObj->retrieveContent( $imageDTO, $storage_method );
	}

}

