<?php
/**
 * CRUDDirector Class File
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
 * @category Plugins
 * @package Forms
 * @subpackage CRUD
 * @version 1.0
 */

/**
 * CRUDDirector
 *
 * $short_description
 *
 * $long_description
 *
 * @category Plugins
 * @package Forms
 * @subpackage CRUD
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
			if ( $this->pulledTrigger($form, $createTrigger) ) {
				$retVal = $this->processCreate( $form );
				$pulledTrigger = true;
				break;
			}
		}

		if ( !$pulledTrigger ) {
			$readTriggers = $form->getCRUDReadTriggers();

			foreach( $readTriggers as $readTrigger ) {
				if ( $this->pulledTrigger($form, $readTrigger) ) {
					$retVal = $this->processRead( $form );
					$pulledTrigger = true;
					break;
				}
			}
		}

		if ( !$pulledTrigger ) {
			$updateTriggers = $form->getCRUDUpdateTriggers();

			foreach( $updateTriggers as $updateTrigger ) {
				if ( $this->pulledTrigger($form, $updateTrigger) ) {
					$retVal = $this->processUpdate( $form );
					$pulledTrigger = true;
					break;
				}
			}
		}

		if ( !$pulledTrigger ) {
			$destroyTriggers = $form->getCRUDDestroyTriggers();

			foreach( $destroyTriggers as $destroyTrigger ) {
				if ( $this->pulledTrigger($form, $destroyTrigger) ) {
					$retVal = $this->processDestroy( $form );
					$pulledTrigger = true;
					break;
				}
			}
		}

		return $retVal;
	}

	public function processCreate( $form ) {
		$retVal = null;

		$formDAO = $this->buildDAO( $form );
		$formDTO = $this->buildDTO( $form );

		$retVal = $formDAO->CRUDCreate( $formDTO );

		return $retVal;
	}

	public function processRead( $form ) {
		$retVal = null;

		$formDAO = $this->buildDAO( $form );
		$formDTO = $this->buildDTO( $form );

		$retVal = $formDAO->CRUDRead( $formDTO );

		return $retVal;
	}

	public function processUpdate( $form ) {
		$retVal = null;

		$formDAO = $this->buildDAO( $form );
		$formDTO = $this->buildDTO( $form );

		$retVal = $formDAO->CRUDUpdate( $formDTO );

		return $retVal;
	}

	public function processDestroy( $form ) {
		$retVal = null;

		$formDAO = $this->buildDAO( $form );
		$formDTO = $this->buildDTO( $form );

		$retVal = $formDAO->CRUDDestroy( $formDTO );

		return $retVal;
	}

	private function buildDAO( $form ) {
		$retVal = null;

		$formDAOFactoryName = $form->getFormDAOFactory();
		$formDAOFactory = $formDAOFactoryName::getInstance();

		$formDAOName = $form->getFormDAO();
		$getDAOMethod = 'get' . $formDAOName;

		$formDAO = $formDAOFactory->$getDAOMethod( $form->getFormDAOConnectionName() );

		$retVal = $formDAO;

		return $retVal;
	}

	private function buildDTO( $form ) {
		$retVal = null;

		$formDTOName = $form->getFormDTO();

		// TODO initialize this bad boy
		$formDTO = new $formDTOName();
		$formDTO->initWithForm( $form );

		$retVal = $formDTO;

		return $retVal;
	}

	private function pulledTrigger( $form, $trigger ) {
		$retVal = false;

		if ( strtolower( $trigger['type'] ) === 'formfield' ) {
			// TODO this should do multilevel...
			if ( $form->issetFormField( $trigger['key'] ) ) { 
				$formField = $form->getFormField( $trigger['key'] );

				if ( $formField->getValue() === $trigger['value'] ) {
					$retVal = true;
				}
			}
		} else if ( strtolower( $trigger['type'] ) === 'post' ) {
			$requestInfoBean = RequestInfoBean::getInstance();

			if ( $requestInfoBean->issetPOST( $trigger['key'] ) ) {
				if ( $requestInfoBean->getPOST( $trigger['key'] ) === $trigger['value'] ) {
					$retVal = true;
				}
			} else if ( isset( $_POST[$trigger['key']] ) ) {
				if ( $_POST[$trigger['key']] === $trigger['value'] ) {
					$retVal = true;
				}
			}
		} else if ( strtolower( $trigger['type'] ) === 'get' ) {
			$requestInfoBean = RequestInfoBean::getInstance();

			if ( $requestInfoBean->issetGET( $trigger['key'] ) ) {
				if ( $requestInfoBean->getGET( $trigger['key'] ) === $trigger['value'] ) {
					$retVal = true;
				}
			} else if ( isset( $_GET[$trigger['key']] ) ) {
				if ( $_GET[$trigger['key']] === $trigger['value'] ) {
					$retVal = true;
				}
			}
		} else {
			// Invalid type specified...
		}

		return $retVal;
	}

}

deprecate( __FILE__, '\eGloo\Plugin\Form\CRUD\Director' );
