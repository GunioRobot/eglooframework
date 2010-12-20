<?php
/**
 * FormDirector Class File
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
 * FormDirector
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
final class FormDirector {

	/**
	 * Static Data Members
	 */
	private static $_singleton = null;

	private $_formNodes;

	private function __construct() {
		
	}

	public static function getInstance() {
		if (!self::$_singleton) {
			self::$_singleton = new FormDirector();
		}

		return self::$_singleton;
	}

	/**
	 * This method reads the forms xml file from disk into a document object model.
	 */
	protected function loadFormDirectors(){
		eGlooLogger::writeLog( eGlooLogger::DEBUG, "FormDirector: Processing XML", 'Forms' );

		$forms_xml_location = eGlooConfiguration::getApplicationsPath() . '/' . eGlooConfiguration::getApplicationPath() . '/XML/Forms.xml';

		eGlooLogger::writeLog( eGlooLogger::DEBUG, 'FormDirector: Loading ' . $forms_xml_location, 'Forms' );

		$formsXMLObject = simplexml_load_file( $forms_xml_location );

		if (!$formsXMLObject) {
			eGlooLogger::writeLog( eGlooLogger::EMERGENCY,
				'FormDirector: simplexml_load_file( "' . $forms_xml_location . '" ): ' . libxml_get_errors() );
		}

		$forms = array();

		foreach( $formsXMLObject->xpath( '/tns:Forms/Form' ) as $formNode ) {
			$formNodeID = isset($formNode['id']) ? (string) $formNode['id'] : NULL;

			if ( !$formNodeID || trim($formNodeID) === '' ) {
				throw new ErrorException('No ID specified in form node. Please review your Forms.xml');
			}

			$formNodeLocalized = isset( $formNode['localized'] ) ? strtolower( (string) $formNode['localized'] ) : NULL;

			if ( !$formNodeLocalized || trim($formNodeLocalized) === '' ) {
				throw new ErrorException('No localization setting specified in form node \'' . $formNodeID . '\'. Please review your Forms.xml');
			} else if ($formNodeLocalized === 'true') {
				$formNodeLocalized = true;
			} else if ($formNodeLocalized === 'false') {
				$formNodeLocalized = false;
			} else {
				throw new ErrorException('Invalid localization setting specified in form node \'' . $formNodeID . '\'. Please review your Forms.xml');
			}

			$formNodeValidated = isset( $formNode['validated'] ) ? strtolower( (string) $formNode['validated'] ) : NULL;

			if ( !$formNodeValidated || trim($formNodeValidated) === '' ) {
				throw new ErrorException('No validation setting specified in form node \'' . $formNodeID . '\'. Please review your Forms.xml');
			} else if ($formNodeValidated === 'true') {
				$formNodeValidated = true;
			} else if ($formNodeValidated === 'false') {
				$formNodeValidated = false;
			} else {
				throw new ErrorException('Invalid validation setting specified in form node \'' . $formNodeID . '\'. Please review your Forms.xml');
			}

			$formNodeSecure = isset( $formNode['secure'] ) ? strtolower( (string) $formNode['secure'] ) : NULL;

			if ( !$formNodeSecure || trim($formNodeSecure) === '' ) {
				throw new ErrorException('No secure setting specified in form node \'' . $formNodeID . '\'. Please review your Forms.xml');
			} else if ($formNodeSecure === 'true') {
				$formNodeSecure = true;
			} else if ($formNodeSecure === 'false') {
				$formNodeSecure = false;
			} else {
				throw new ErrorException('Invalid secure setting specified in form node \'' . $formNodeID . '\'. Please review your Forms.xml');
			}

			$formNodeDAO = isset($formNode['dao']) ? (string) $formNode['dao'] : NULL;

			if ( !$formNodeDAO || trim($formNodeDAO) === '' ) {
				throw new ErrorException('No DAO specified in form node \'' . $formNodeID . '\'. Please review your Forms.xml');
			}

			$formNodeDTO = isset($formNode['dto']) ? (string) $formNode['dto'] : NULL;

			if ( !$formNodeDTO || trim($formNodeDTO) === '' ) {
				throw new ErrorException('No DTO specified in form node \'' . $formNodeID . '\'. Please review your Forms.xml');
			}

			$formNodeValidator = isset($formNode['validator']) ? (string) $formNode['validator'] : NULL;

			if ( !$formNodeValidator || trim($formNodeValidator) === '' ) {
				throw new ErrorException('No validator specified in form node \'' . $formNodeID . '\'. Please review your Forms.xml');
			}

			$formNodeDataFormatter = isset($formNode['dataFormatter']) ? (string) $formNode['dataFormatter'] : NULL;

			if ( !$formNodeDataFormatter || trim($formNodeDataFormatter) === '' ) {
				throw new ErrorException('No data formatter specified in form node \'' . $formNodeID . '\'. Please review your Forms.xml');
			}

			$formNodeDisplayFormatter = isset($formNode['displayFormatter']) ? (string) $formNode['displayFormatter'] : NULL;

			if ( !$formNodeDisplayFormatter || trim($formNodeDisplayFormatter) === '' ) {
				throw new ErrorException('No display formatter specified in form node \'' . $formNodeID . '\'. Please review your Forms.xml');
			}

			$formNodes[$formNodeID] = array(	'formID' => $formNodeID,
												'localized' => $formNodeLocalized,
												'validated' => $formNodeValidated,
												'secure' => $formNodeSecure,
												'DAO' => $formNodeDAO,
												'DTO' => $formNodeDTO,
												'validator' => $formNodeValidator,
												'dataFormatter' => $formNodeDataFormatter,
												'displayFormatter' => $formNodeDisplayFormatter,
												'formFieldSets' => array(),
												'formFields' => array(),
												'CRUDInfo' => array()
											);

			foreach( $formNode->xpath( 'child::FormFieldSet' ) as $formFieldSet ) {
				$formFieldSetID = isset($formFieldSet['id']) ? (string) $formFieldSet['id'] : NULL;

				if ( !$formFieldSetID || trim($formFieldSetID) === '' ) {
					throw new ErrorException("No FormFieldSet ID specified in FormFieldSet: '" . $formFieldSet .
						"'.	 Please review your Forms.xml");
				}

				$formFieldSetNodeLocalized = isset( $formFieldSet['localized'] ) ? strtolower( (string) $formFieldSet['localized'] ) : NULL;

				if ( !$formFieldSetNodeLocalized || trim($formFieldSetNodeLocalized) === '' ) {
					throw new ErrorException('No localization setting specified in FormFieldSet \'' . $formFieldSetID . '\'. Please review your Forms.xml');
				} else if ($formFieldSetNodeLocalized === 'true') {
					$formFieldSetNodeLocalized = true;
				} else if ($formFieldSetNodeLocalized === 'false') {
					$formFieldSetNodeLocalized = false;
				} else {
					throw new ErrorException('Invalid localization setting specified in FormFieldSet \'' . $formFieldSetID . '\'. Please review your Forms.xml');
				}

				$formFieldSetNodeValidated = isset( $formFieldSet['validated'] ) ? strtolower( (string) $formFieldSet['validated'] ) : NULL;

				if ( !$formFieldSetNodeValidated || trim($formFieldSetNodeValidated) === '' ) {
					throw new ErrorException('No validation setting specified in form node \'' . $formFieldSetID . '\'. Please review your Forms.xml');
				} else if ($formFieldSetNodeValidated === 'true') {
					$formFieldSetNodeValidated = true;
				} else if ($formFieldSetNodeValidated === 'false') {
					$formFieldSetNodeValidated = false;
				} else {
					throw new ErrorException('Invalid validation setting specified in form node \'' . $formFieldSetID . '\'. Please review your Forms.xml');
				}

				$formFieldSetNodeSecure = isset( $formFieldSet['secure'] ) ? strtolower( (string) $formFieldSet['secure'] ) : NULL;

				if ( !$formFieldSetNodeSecure || trim($formFieldSetNodeSecure) === '' ) {
					throw new ErrorException('No secure setting specified in form node \'' . $formFieldSetID . '\'. Please review your Forms.xml');
				} else if ($formFieldSetNodeSecure === 'true') {
					$formFieldSetNodeSecure = true;
				} else if ($formFieldSetNodeSecure === 'false') {
					$formFieldSetNodeSecure = false;
				} else {
					throw new ErrorException('Invalid secure setting specified in form node \'' . $formFieldSetID . '\'. Please review your Forms.xml');
				}

				$formFieldSetNodeValidator = isset($formFieldSet['validator']) ? (string) $formFieldSet['validator'] : NULL;

				$newFormFieldSet = array(	'id' => $formFieldSetID,
											'localized' => $formFieldSetNodeLocalized,
											'validated' => $formFieldSetNodeValidated,
											'secure' => $formFieldSetNodeSecure,
											'validator' => $formFieldSetNodeValidator,
											'formFields' => array()
										);

				$formFieldSetFormFields = array();

				foreach( $formFieldSet->xpath( 'child::FormField' ) as $formField ) {
					$formFieldID = isset($formField['id']) ? (string) $formField['id'] : NULL;

					if ( !$formFieldID || trim($formFieldID) === '' ) {
						throw new ErrorException("No FormField ID specified in FormField: '" . $formField .
							"'.	 Please review your Forms.xml");
					}

					$formFieldType = isset($formField['type']) ? (string) $formField['type'] : NULL;

					if ( !$formFieldType || trim($formFieldType) === '' ) {
						throw new ErrorException('No FormField type specified in FormField: \'' . $formFieldID .
							'\'.	 Please review your Forms.xml');
					}

					$formFieldNodeLocalized = isset( $formField['localized'] ) ? strtolower( (string) $formField['localized'] ) : NULL;

					if ($formFieldNodeLocalized === 'true') {
						$formFieldNodeLocalized = true;
					} else if ($formFieldNodeLocalized === 'false') {
						$formFieldNodeLocalized = false;
					} else if ($formFieldType === 'hidden') {
						$formFieldNodeLocalized = false;
					} else if (isset($formFieldSetNodeLocalized)) {
						$formFieldNodeLocalized = $formFieldSetNodeLocalized;
					} else {
						$formFieldNodeLocalized = false;
					}

					$containerChildren = array();

					if ($formFieldType === 'container') {
						foreach( $formField->xpath( 'child::FormField' ) as $formFieldChild ) {
							$formFieldChildID = isset($formFieldChild['id']) ? (string) $formFieldChild['id'] : NULL;

							if ( !$formFieldChildID || trim($formFieldChildID) === '' ) {
								throw new ErrorException('No FormField ID specified in FormField Child: \'' . $formFieldChild .
									'\'.	 Please review your Forms.xml');
							}

							$formFieldChildType = isset($formFieldChild['type']) ? (string) $formFieldChild['type'] : NULL;

							if ( !$formFieldChildType || trim($formFieldChildType) === '' ) {
								throw new ErrorException("No FormField type specified in FormField Child: '" . $formFieldChildID .
									"'.	 Please review your Forms.xml");
							} else if ($formFieldChildType === 'container') {
								throw new ErrorException("eGloo does not currently allow container FormFields to have container children.  Please review your Forms.xml");
							}

							$formFieldChildNodeLocalized = isset( $formFieldChild['localized'] ) ? strtolower( (string) $formFieldChild['localized'] ) : NULL;

							if ($formFieldChildNodeLocalized === 'true') {
								$formFieldChildNodeLocalized = true;
							} else if ($formFieldChildNodeLocalized === 'false') {
								$formFieldChildNodeLocalized = false;
							} else if ($formFieldChildNodeLocalized === 'hidden') {
								$formFieldChildNodeLocalized = false;
							} else if (isset($formFieldNodeLocalized)) {
								$formFieldChildNodeLocalized = $formFieldNodeLocalized;
							} else {
								$formFieldChildNodeLocalized = false;
							}

							$childDisplayLabel = null;
							$childErrorMessage = null;
							$childErrorHandler = null;

							foreach( $formFieldChild->xpath( 'child::DisplayLabel' ) as $childDisplayLabelNode ) {
								$childDisplayLabel = (string) $childDisplayLabelNode;
							}

							foreach( $formFieldChild->xpath( 'child::ErrorMessage' ) as $childErrorMessageNode ) {
								$childErrorMessage = (string) $childErrorMessageNode;
							}

							foreach( $formFieldChild->xpath( 'child::ErrorHandler' ) as $childErrorHandlerNode ) {
								$childErrorHandler = (string) $childErrorHandlerNode;
							}

							$newChildFormField = array(	'id' => $formFieldChildID,
													'type' => $formFieldChildType,
													'localized' => $formFieldChildNodeLocalized,
													'displayLabel' => $childDisplayLabel,
													'errorMessage' => $childErrorMessage,
													'errorHandler' => $childErrorHandler,
												);

							$containerChildren[$formFieldChildID] = $newChildFormField;
						}
					}

					$displayLabel = null;
					$errorMessage = null;
					$errorHandler = null;

					foreach( $formField->xpath( 'child::DisplayLabel' ) as $displayLabelNode ) {
						$displayLabel = (string) $displayLabelNode;
					}

					foreach( $formField->xpath( 'child::ErrorMessage' ) as $errorMessageNode ) {
						$errorMessage = (string) $errorMessageNode;
					}

					foreach( $formField->xpath( 'child::ErrorHandler' ) as $errorHandlerNode ) {
						$errorHandler = (string) $errorHandlerNode;
					}

					$newFormField = array(	'id' => $formFieldID,
											'type' => $formFieldType,
											'localized' => $formFieldNodeLocalized,
											'displayLabel' => $displayLabel,
											'errorMessage' => $errorMessage,
											'errorHandler' => $errorHandler,
										);

					if (!empty($containerChildren)) {
						$newFormField['children'] = $containerChildren;
					}

					$formFieldSetFormFields[$formFieldID] = $newFormField;
				}
				
				$newFormFieldSet['formFields'] = $formFieldSetFormFields;
				$formNodes[$formNodeID]['formFieldSets'][$formFieldSetID] = $newFormFieldSet;
			}

			$this->_formNodes = $formNodes;
		}

		die_r($this->_formNodes);

		// $dispatchCacheRegionHandler = CacheManagementDirector::getCacheRegionHandler('Dispatches');
		// 
		// $dispatchCacheRegionHandler->storeObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'FormDirectorNodes',
		// 	$this->_formNodes, 'Dispatching', 0, true );
	}

	private function loadFormFieldNodeDefinition() {
		
	}

	public function buildForm( $form_name, $parameters = null ) {
		$retVal = null;

		$formNode = $this->getFormNodeDefinition( $form_name );

		return $retVal;
	}

	public function buildSubmittedForm( $form_name, $parameter_method ) {
		$retVal = null;

		$formNode = $this->getFormNodeDefinition( $form_name );

		return $retVal;
	}

	/**
	 * Validate and process a form build internally
	 * 
	 * @return Fully built and validated form object or false on error
	 */
	public function processForm( $form_name, $parameter_method ) {
		$retVal = false;

		$formNode = $this->getFormNodeDefinition( $form_name );

		// TODO valid / decrypt form

		return $retVal;
	}

	/**
	 * Validate and process a form passed in as an argument
	 * 
	 * @return Fully built and validated form object or false on error
	 */
	public function processSubmittedForm( $form_name, $parameter_method ) {
		$retVal = false;

		$formNode = $this->getFormNodeDefinition( $form_name );

		// TODO valid / decrypt form

		return $retVal;
	}

	private function getFormNodeDefinition( $form_name ) {
		$retVal = null;

		$dispatchCacheRegionHandler = CacheManagementDirector::getCacheRegionHandler('Dispatches');
		$nodeCacheID = eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'FormDirectorNodes';
		
		if ( ($this->_formNodes = $dispatchCacheRegionHandler->getObject( $nodeCacheID, 'Dispatching', true ) ) == null ) {
			eGlooLogger::writeLog( eGlooLogger::DEBUG, "FormDirector: Form Definition Nodes pulled from cache" );
			$this->loadFormDirectors();
			$dispatchCacheRegionHandler->storeObject( $nodeCacheID, $this->_formNodes, 'Dispatching', 0, true );
		} else {
			eGlooLogger::writeLog( eGlooLogger::DEBUG, "FormDirector: Form Definition Nodes pulled from cache" );
		}

		if (isset($this->_formNodes[$form_name])) {
			$retVal = $this->_formNodes[$form_name];
		} else {
			throw new FormDirectorException( 'Unknown Form Definition requested: \'' . $form_name . '\'' );
		}

		return $retVal;
	}

}
