<?php
/**
 * MySQLiOOPQueryResponseResource Class File
 *
 * $file_block_description
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
 * @package $package
 * @subpackage $subpackage
 * @version 1.0
 */

/**
 * MySQLiOOPQueryResponseResource
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class MySQLiOOPQueryResponseResource extends QueryResponseResource {

	public $index = 0;
	public $length = null;

	public function getBooleanValue() {
		return (bool) $this->_rawResponseResource;
	}

	public function isBooleanValue() {
		return is_bool($this->_rawResponseResource);
	}

	public function resetIndex() {
		$this->index = 0;
	}

	public function fetchNextRowAssociative() {
		$retVal = null;
		
		if ($this->length === null) {
			$this->length = count($this->_rawResponseResource);
		}

		if ( !is_bool($this->_rawResponseResource) ) {
			if ($this->index < $this->length) {
				$retVal = $this->_rawResponseResource[$this->index];
			}

			$this->index += 1;
		} else {
			$retVal = $this->_rawResponseResource;
		}

		return $retVal;
	}

	public function fetchNextRowGenericDTO() {}

	public function fetchGenericDTOArray() {}

}

