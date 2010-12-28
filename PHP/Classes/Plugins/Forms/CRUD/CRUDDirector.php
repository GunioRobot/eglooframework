<?php
/**
 * CRUDDirector Class File
 *
 * $file_block_description
 * 
 * Copyright 2010 eGloo, LLC
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
 * @copyright 2010 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package $package
 * @subpackage $subpackage
 * @version 1.0
 */

/**
 * CRUDDirector
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class CRUDDirector {

	private static $_singleton = null;

	private function __construct() {
		
	}

	public static function getInstance() {
		if (!self::$_singleton) {
			self::$_singleton = new CRUDDirector();
		}

		return self::$_singleton;
	}

	public function processForm( $form ) {
		$retVal = false;

		$pulledTrigger = false;

		// Branch to CRUD action
		$createTriggers = $form->getCRUDCreateTriggers();

		foreach( $createTriggers as $createTrigger ) {
			if ( $this->pulledTrigger($createTrigger) ) {
				$retVal = $this->processCreate( $form );
				$pulledTrigger = true;
				break;
			}
		}

		if ( !$pulledTrigger ) {
			$readTriggers = $form->getCRUDReadTriggers();

			foreach( $readTriggers as $readTrigger ) {
				if ( $this->pulledTrigger($readTrigger) ) {
					$retVal = $this->processRead( $form );
					$pulledTrigger = true;
					break;
				}
			}
		}

		if ( !$pulledTrigger ) {
			$updateTriggers = $form->getCRUDUpdateTriggers();

			foreach( $updateTriggers as $updateTrigger ) {
				if ( $this->pulledTrigger($updateTrigger) ) {
					$retVal = $this->processUpdate( $form );
					$pulledTrigger = true;
					break;
				}
			}
		}

		if ( !$pulledTrigger ) {
			$destroyTriggers = $form->getCRUDDestroyTriggers();

			foreach( $destroyTriggers as $destroyTrigger ) {
				if ( $this->pulledTrigger($destroyTrigger) ) {
					$retVal = $this->processDestroy( $form );
					$pulledTrigger = true;
					break;
				}
			}
		}

		return $retVal;
	}

	public function processCreate( $form ) {
		echo_r('create');
		$formDAO = $form->getFormDAO();
		$formDTO = $form->getFormDTO();
	}

	public function processRead( $form ) {
		echo_r('read');
		$formDAO = $form->getFormDAO();
		$formDTO = $form->getFormDTO();
	}

	public function processUpdate( $form ) {
		echo_r('update');
		$formDAO = $form->getFormDAO();
		$formDTO = $form->getFormDTO();
	}

	public function processDestroy( $form ) {
		echo_r('destroy');
		$formDAO = $form->getFormDAO();
		$formDTO = $form->getFormDTO();
	}

	private function pulledTrigger( $trigger ) {
		$retVal = false;

		if ( strtolower( $trigger['type'] ) === 'formfield' ) {
			// if ($)
		} else if ( strtolower( $trigger['type'] ) === 'post' ) {
			
		} else if ( strtolower( $trigger['type'] ) === 'get' ) {
			
		} else {
			// Invalid type specified...
		}

		return $retVal;
	}

}

