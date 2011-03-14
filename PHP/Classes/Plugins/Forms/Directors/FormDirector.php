<?php
/**
 * FormDirector Class File
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
	private $_formAttributeSetNodes;

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
	protected function loadFormDefinitions(){
		eGlooLogger::writeLog( eGlooLogger::DEBUG, "FormDirector: Processing XML", 'Forms' );

		$xml_forms_locations = array();
		
		$application_forms_xml_location = eGlooConfiguration::getApplicationsPath() . '/' . eGlooConfiguration::getApplicationPath() . '/XML/Forms.xml';

		$framework_forms_xml_location = eGlooConfiguration::getFrameworkRootPath() . '/XML/Forms.xml'; 

		$xml_forms_locations[] = $framework_forms_xml_location;
		$xml_forms_locations[] = $application_forms_xml_location;

		$formNodes = array();
		$formAttributeSetNodes = array();

		foreach( $xml_forms_locations as $xml_forms_location ) {
			eGlooLogger::writeLog( eGlooLogger::DEBUG, 'FormDirector: Loading ' . $xml_forms_location, 'Forms' );

			$formsXMLObject = simplexml_load_file( $xml_forms_location );

			if (!$formsXMLObject) {
				eGlooLogger::writeLog( eGlooLogger::EMERGENCY,
					'FormDirector: simplexml_load_file( "' . $xml_forms_location . '" ): ' . libxml_get_errors() );
			}

			// Grab the cache handler specifically for this cache region.  We do this so that when we write to the cache for Form definitions
			// we can also write some information to the caching system to better keep track of what is cached for the Form processing system
			// and do more granulated inspection and cache clearing
			$dispatchCacheRegionHandler = CacheManagementDirector::getCacheRegionHandler('Dispatches');

			// FormAttributeSets
			foreach( $formsXMLObject->xpath( '/tns:Forms/FormAttributeSet' ) as $formAttributeSetNode ) {
				$formAttributeSetNodeID = isset($formAttributeSetNode['id']) ? (string) $formAttributeSetNode['id'] : NULL;

				if ( !$formAttributeSetNodeID || trim($formAttributeSetNodeID) === '' ) {
					throw new ErrorException('No ID specified in form node. Please review your Forms.xml');
				}

				$formAttributeSetNodeIDCamel = eGlooString::toCamelCase( $formAttributeSetNodeID );

				$formAttributeSetNodeDisplayLocalized = isset( $formAttributeSetNode['displayLocalized'] ) ? strtolower( (string) $formAttributeSetNode['displayLocalized'] ) : NULL;
				$formAttributeSetNodeDisplayLocalizer = isset( $formAttributeSetNode['displayLocalizer'] ) ? (string) $formAttributeSetNode['displayLocalizer'] : NULL;

				if ( !$formAttributeSetNodeDisplayLocalized || trim($formAttributeSetNodeDisplayLocalized) === '' ) {
					throw new ErrorException('No display localization setting specified in form node \'' . $formAttributeSetNodeID . '\'. Please review your Forms.xml');
				} else if ($formAttributeSetNodeDisplayLocalized === 'true') {
					$formAttributeSetNodeDisplayLocalized = true;
				} else if ($formAttributeSetNodeDisplayLocalized === 'false') {
					$formAttributeSetNodeDisplayLocalized = false;
				} else {
					throw new ErrorException('Invalid display localization setting specified in form node \'' . $formAttributeSetNodeID . '\'. Please review your Forms.xml');
				}

				// NOTE: If no DisplayLocalizer is specified, token replacement is based on DisplayLabels in Forms.xml
				// If no alternate DisplayLabel for a localization exists, the default is returned

				$formAttributeSetNodeInputLocalized = isset( $formAttributeSetNode['inputLocalized'] ) ? strtolower( (string) $formAttributeSetNode['inputLocalized'] ) : NULL;

				if ( !$formAttributeSetNodeInputLocalized || trim($formAttributeSetNodeInputLocalized) === '' ) {
					throw new ErrorException('No input localization setting specified in form node \'' . $formAttributeSetNodeID . '\'. Please review your Forms.xml');
				} else if ($formAttributeSetNodeInputLocalized === 'true') {
					$formAttributeSetNodeInputLocalized = true;
				} else if ($formAttributeSetNodeInputLocalized === 'false') {
					$formAttributeSetNodeInputLocalized = false;
				} else {
					throw new ErrorException('Invalid input localization setting specified in form node \'' . $formAttributeSetNodeID . '\'. Please review your Forms.xml');
				}

				$formAttributeSetNodeInputLocalizer = isset( $formAttributeSetNode['inputLocalizer'] ) ? (string) $formAttributeSetNode['inputLocalizer'] : NULL;

				// NOTE: If the Form states it needs InputLocalization support, but doesn't specify an InputLocalizer, it's in error
				if ( $formAttributeSetNodeDisplayLocalized && (!$formAttributeSetNodeInputLocalizer || trim($formAttributeSetNodeInputLocalizer) === '') ) {
					throw new ErrorException('No input localizer specified in form node \'' . $formAttributeSetNodeID . '\'. Please review your Forms.xml');
				}

				$formAttributeSetNodeValidated = isset( $formAttributeSetNode['validated'] ) ? strtolower( (string) $formAttributeSetNode['validated'] ) : NULL;

				if ( !$formAttributeSetNodeValidated || trim($formAttributeSetNodeValidated) === '' ) {
					throw new ErrorException('No validation setting specified in form node \'' . $formAttributeSetNodeID . '\'. Please review your Forms.xml');
				} else if ($formAttributeSetNodeValidated === 'true') {
					$formAttributeSetNodeValidated = true;
				} else if ($formAttributeSetNodeValidated === 'false') {
					$formAttributeSetNodeValidated = false;
				} else {
					throw new ErrorException('Invalid validation setting specified in form node \'' . $formAttributeSetNodeID . '\'. Please review your Forms.xml');
				}

				$formAttributeSetNodeSecure = isset( $formAttributeSetNode['secure'] ) ? strtolower( (string) $formAttributeSetNode['secure'] ) : NULL;

				if ( !$formAttributeSetNodeSecure || trim($formAttributeSetNodeSecure) === '' ) {
					throw new ErrorException('No secure setting specified in form node \'' . $formAttributeSetNodeID . '\'. Please review your Forms.xml');
				} else if ($formAttributeSetNodeSecure === 'true') {
					$formAttributeSetNodeSecure = true;
				} else if ($formAttributeSetNodeSecure === 'false') {
					$formAttributeSetNodeSecure = false;
				} else {
					throw new ErrorException('Invalid secure setting specified in form node \'' . $formAttributeSetNodeID . '\'. Please review your Forms.xml');
				}

				$formAttributeSetNodeDAOConnectionName = isset($formAttributeSetNode['daoConnectionName']) ? (string) $formAttributeSetNode['daoConnectionName'] : NULL;

				if ( !$formAttributeSetNodeDAOConnectionName || trim($formAttributeSetNodeDAOConnectionName) === '' ) {
					throw new ErrorException('No DAOConnectionName specified in form node \'' . $formAttributeSetNodeID . '\'. Please review your Forms.xml');
				}

				$formAttributeSetNodeDAOFactory = isset($formAttributeSetNode['daoFactory']) ? (string) $formAttributeSetNode['daoFactory'] : NULL;

				if ( !$formAttributeSetNodeDAOFactory || trim($formAttributeSetNodeDAOFactory) === '' ) {
					throw new ErrorException('No DAOFactory specified in form node \'' . $formAttributeSetNodeID . '\'. Please review your Forms.xml');
				}

				$formAttributeSetNodeDAO = isset($formAttributeSetNode['dao']) ? (string) $formAttributeSetNode['dao'] : NULL;

				if ( !$formAttributeSetNodeDAO || trim($formAttributeSetNodeDAO) === '' ) {
					throw new ErrorException('No DAO specified in form node \'' . $formAttributeSetNodeID . '\'. Please review your Forms.xml');
				}

				$formAttributeSetNodeDTO = isset($formAttributeSetNode['dto']) ? (string) $formAttributeSetNode['dto'] : NULL;

				if ( !$formAttributeSetNodeDTO || trim($formAttributeSetNodeDTO) === '' ) {
					throw new ErrorException('No DTO specified in form node \'' . $formAttributeSetNodeID . '\'. Please review your Forms.xml');
				}

				$formAttributeSetNodeValidator = isset($formAttributeSetNode['validator']) ? (string) $formAttributeSetNode['validator'] : NULL;

				if ( !$formAttributeSetNodeValidator || trim($formAttributeSetNodeValidator) === '' ) {
					throw new ErrorException('No validator specified in form node \'' . $formAttributeSetNodeID . '\'. Please review your Forms.xml');
				}

				$formAttributeSetNodeDataFormatter = isset($formAttributeSetNode['dataFormatter']) ? (string) $formAttributeSetNode['dataFormatter'] : NULL;

				if ( !$formAttributeSetNodeDataFormatter || trim($formAttributeSetNodeDataFormatter) === '' ) {
					throw new ErrorException('No data formatter specified in form node \'' . $formAttributeSetNodeID . '\'. Please review your Forms.xml');
				}

				$formAttributeSetNodeDisplayFormatter = isset($formAttributeSetNode['displayFormatter']) ? (string) $formAttributeSetNode['displayFormatter'] : NULL;

				if ( !$formAttributeSetNodeDisplayFormatter || trim($formAttributeSetNodeDisplayFormatter) === '' ) {
					throw new ErrorException('No display formatter specified in form node \'' . $formAttributeSetNodeID . '\'. Please review your Forms.xml');
				}

				$formAttributeSetEncoding = isset($formAttributeSetNode['encoding']) ? (string) $formAttributeSetNode['encoding'] : NULL;

				$prependHTML = null;
				$appendHTML = null;
				$cssClasses = null;

				$formLegend = null;
				$formLegendLocalizationToken = null;

				foreach( $formAttributeSetNode->xpath( 'child::Legend' ) as $legend ) {
					$formLegend = (string) $legend;
					$formLegendLocalizationToken = isset($legend['legendToken']) ? (string) $legend['legendToken'] : NULL;
				}

				foreach( $formAttributeSetNode->xpath( 'child::PrependHTML' ) as $childPrependHTMLNode ) {
					$prependHTML = (string) $childPrependHTMLNode;
				}

				foreach( $formAttributeSetNode->xpath( 'child::AppendHTML' ) as $childAppendHTMLNode ) {
					$appendHTML = (string) $childAppendHTMLNode;
				}

				foreach( $formAttributeSetNode->xpath( 'child::CSSClasses' ) as $childCSSClassesNode ) {
					$cssClasses = (string) $childCSSClassesNode;
				}

				$formAttributeSetNodes[$formAttributeSetNodeID] = array(	'formID' => $formAttributeSetNodeID,
													'legend' => $formLegend,
													'legendToken' => $formLegendLocalizationToken,
													'displayLocalized' => $formAttributeSetNodeDisplayLocalized,
													'displayLocalizer' => $formAttributeSetNodeDisplayLocalizer,
													'inputLocalized' => $formAttributeSetNodeInputLocalized,
													'inputLocalizer' => $formAttributeSetNodeInputLocalizer,
													'validated' => $formAttributeSetNodeValidated,
													'secure' => $formAttributeSetNodeSecure,
													'daoConnectionName' => $formAttributeSetNodeDAOConnectionName,
													'daoFactory' => $formAttributeSetNodeDAOFactory,
													'DAO' => $formAttributeSetNodeDAO,
													'DTO' => $formAttributeSetNodeDTO,
													'validator' => $formAttributeSetNodeValidator,
													'dataFormatter' => $formAttributeSetNodeDataFormatter,
													'displayFormatter' => $formAttributeSetNodeDisplayFormatter,
													'formFieldSets' => array(),
													'formFields' => array(),
													'CRUDInfo' => array(),
													'prependHTML' => $prependHTML,
													'appendHTML' => $appendHTML,
													'cssClasses' => $cssClasses,
													'encoding' => $formAttributeSetEncoding,
												);

				foreach( $formAttributeSetNode->xpath( 'child::FormFieldSet' ) as $formFieldSet ) {
					$formFieldSetID = isset($formFieldSet['id']) ? (string) $formFieldSet['id'] : NULL;

					if ( !$formFieldSetID || trim($formFieldSetID) === '' ) {
						throw new ErrorException("No FormFieldSet ID specified in FormFieldSet: '" . $formFieldSet .
							"'.	 Please review your Forms.xml");
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
					$formFieldSetNodeRequired = isset($formFieldSet['required']) ? (string) $formFieldSet['required'] : NULL;

					if ( $formFieldSetNodeRequired && $formFieldSetNodeRequired === 'true' ) {
						$formFieldSetNodeRequired = true;
					} else {
						$formFieldSetNodeRequired = false;
					}

					$formFieldSetLegend = null;
					$formFieldSetLegendLocalizationToken = null;

					foreach( $formFieldSet->xpath( 'child::Legend' ) as $legend ) {
						$formFieldSetLegend = (string) $legend;
						$formFieldSetLegendLocalizationToken = isset($legend['legendToken']) ? (string) $legend['legendToken'] : NULL;
					}

					$formFieldSetErrorMessage = null;
					$formFieldSetErrorMessageLocalizationToken = null;

					foreach( $formFieldSet->xpath( 'child::ErrorMessage' ) as $legend ) {
						$formFieldSetErrorMessage = (string) $legend;
						$formFieldSetErrorMessageLocalizationToken = isset($legend['localizationToken']) ? (string) $legend['localizationToken'] : NULL;
					}

					$formFieldSetErrorHandler = null;

					foreach( $formFieldSet->xpath( 'child::ErrorHandler' ) as $errorHandlerNode ) {
						$formFieldSetErrorHandler = (string) $errorHandlerNode;
					}

					$newFormFieldSet = array(	'id' => $formFieldSetID,
												// 'displayLocalized' => $formFieldSetNodeDisplayLocalized,
												'validated' => $formFieldSetNodeValidated,
												'secure' => $formFieldSetNodeSecure,
												'validator' => $formFieldSetNodeValidator,
												'required' => $formFieldSetNodeRequired,
												'legend' => $formFieldSetLegend,
												'legendToken' => $formFieldSetLegendLocalizationToken,
												'formFields' => array(),
												'errorMessage' => $formFieldSetErrorMessage,
												'errorMessageToken' => $formFieldSetErrorMessageLocalizationToken,
												'errorHandler' => $formFieldSetErrorHandler
											);

					$formFieldSetFormFields = array();

					foreach( $formFieldSet->xpath( 'child::FormField' ) as $formField ) {
						$formFieldID = isset($formField['id']) ? (string) $formField['id'] : NULL;

						if ( !$formFieldID || trim($formFieldID) === '' ) {
							throw new ErrorException("No FormField ID specified in FormField: '" . $formField .
								"'.	 Please review your Forms.xml");
						}

						$formFieldType = isset($formField['type']) ? (string) $formField['type'] : NULL;
						$formFieldValue = isset($formField['value']) ? (string) $formField['value'] : NULL;
						$formFieldRequired =  isset($formField['required']) ? (string) $formField['required'] : NULL;
						$formFieldValueSeeder = isset($formField['seeder']) ? (string) $formField['seeder'] : NULL;

						if ( !$formFieldType || trim($formFieldType) === '' ) {
							throw new ErrorException('No FormField type specified in FormField: \'' . $formFieldID .
								'\'.	 Please review your Forms.xml');
						}

						if ( $formFieldRequired && $formFieldRequired === 'true' ) {
							$formFieldRequired = true;
						} else {
							$formFieldRequired = false;
						}

						$containerChildren = array();

						// Let's process the children of any FormField that fancies itself a container
						if ($formFieldType === 'container') {
							foreach( $formField->xpath( 'child::FormField' ) as $formFieldChild ) {
								$formFieldChildID = isset($formFieldChild['id']) ? (string) $formFieldChild['id'] : NULL;

								if ( !$formFieldChildID || trim($formFieldChildID) === '' ) {
									throw new ErrorException('No FormField ID specified in FormField Child: \'' . $formFieldChild .
										'\'.	 Please review your Forms.xml');
								}

								$formFieldChildType = isset($formFieldChild['type']) ? (string) $formFieldChild['type'] : NULL;
								$formFieldChildValue = isset($formFieldChild['value']) ? (string) $formFieldChild['value'] : NULL;
								$formFieldChildRequired =  isset($formFieldChild['required']) ? (string) $formFieldChild['required'] : NULL;
								$formFieldChildValueSeeder = isset($formFieldChild['seeder']) ? (string) $formFieldChild['seeder'] : NULL;

								if ( !$formFieldChildType || trim($formFieldChildType) === '' ) {
									throw new ErrorException("No FormField type specified in FormField Child: '" . $formFieldChildID .
										"'.	 Please review your Forms.xml");
								} else if ($formFieldChildType === 'container') {
									throw new ErrorException("eGloo does not currently allow container FormFields to have container children.  Please review your Forms.xml");
								}

								if ( $formFieldChildRequired && $formFieldChildRequired === 'true' ) {
									$formFieldChildRequired = true;
								} else {
									$formFieldChildRequired = false;
								}

								$childDisplayLabel = null;
								$childErrorMessage = null;
								$childErrorHandler = null;
								$childPrependHTML = null;
								$childAppendHTML = null;
								$childLabelPrependHTML = null;
								$childLabelAppendHTML = null;
								$childInputPrependHTML = null;
								$childInputAppendHTML = null;
								$childCSSClasses = null;

								$childDisplayLabelLocalizationToken = null;
								$childErrorMessageLocalizationToken = null;

								foreach( $formFieldChild->xpath( 'child::DisplayLabel' ) as $childDisplayLabelNode ) {
									$childDisplayLabel = (string) $childDisplayLabelNode;
									$childDisplayLabelLocalizationToken = isset($childDisplayLabelNode['localizationToken']) ? 
										(string) $childDisplayLabelNode['localizationToken'] : NULL;
								}

								foreach( $formFieldChild->xpath( 'child::ErrorMessage' ) as $childErrorMessageNode ) {
									$childErrorMessage = (string) $childErrorMessageNode;
									$childErrorMessageLocalizationToken = isset($childErrorMessageNode['localizationToken']) ? 
										(string) $childErrorMessageNode['localizationToken'] : NULL;
								}

								foreach( $formFieldChild->xpath( 'child::ErrorHandler' ) as $childErrorHandlerNode ) {
									$childErrorHandler = (string) $childErrorHandlerNode;
								}

								foreach( $formFieldChild->xpath( 'child::PrependHTML' ) as $childPrependHTMLNode ) {
									$childPrependHTML = (string) $childPrependHTMLNode;
								}

								foreach( $formFieldChild->xpath( 'child::AppendHTML' ) as $childAppendHTMLNode ) {
									$childAppendHTML = (string) $childAppendHTMLNode;
								}

								foreach( $formFieldChild->xpath( 'child::LabelPrependHTML' ) as $childLabelPrependHTMLNode ) {
									$childLabelPrependHTML = (string) $childLabelPrependHTMLNode;
								}

								foreach( $formFieldChild->xpath( 'child::LabelAppendHTML' ) as $childLabelAppendHTMLNode ) {
									$childLabelAppendHTML = (string) $childLabelAppendHTMLNode;
								}

								foreach( $formFieldChild->xpath( 'child::InputPrependHTML' ) as $childInputPrependHTMLNode ) {
									$childInputPrependHTML = (string) $childInputPrependHTMLNode;
								}

								foreach( $formFieldChild->xpath( 'child::InputAppendHTML' ) as $childInputAppendHTMLNode ) {
									$childInputAppendHTML = (string) $childInputAppendHTMLNode;
								}

								foreach( $formFieldChild->xpath( 'child::CSSClasses' ) as $childCSSClassesNode ) {
									$childCSSClasses = (string) $childCSSClassesNode;
								}

								$newChildFormField = array(	'id' => $formFieldChildID,
														'type' => $formFieldChildType,
														'value' => $formFieldChildValue,
														'required' => $formFieldChildRequired,
														'seeder' => $formFieldChildValueSeeder,
														// 'displayLocalized' => $formFieldChildNodeDisplayLocalized,
														'displayLabel' => $childDisplayLabel,
														'displayLabelToken' => $childDisplayLabelLocalizationToken,
														'errorMessage' => $childErrorMessage,
														'errorMessageToken' => $childErrorMessageLocalizationToken,
														'errorHandler' => $childErrorHandler,
														'prependHTML' => $childPrependHTML,
														'appendHTML' => $childAppendHTML,
														'labelPrependHTML' => $childLabelPrependHTML,
														'labelAppendHTML' => $childLabelAppendHTML,
														'inputPrependHTML' => $childInputPrependHTML,
														'inputAppendHTML' => $childInputAppendHTML,
														'cssClasses' => $childCSSClasses,
													);

								$containerChildren[$formFieldChildID] = $newChildFormField;
							}
						}

						$displayLabel = null;
						$errorMessage = null;
						$errorHandler = null;
						$prependHTML = null;
						$appendHTML = null;
						$labelPrependHTML = null;
						$labelAppendHTML = null;
						$inputPrependHTML = null;
						$inputAppendHTML = null;
						$cssClasses = null;

						$displayLabelLocalizationToken = null;
						$errorMessageLocalizationToken = null;

						foreach( $formField->xpath( 'child::DisplayLabel' ) as $displayLabelNode ) {
							$displayLabel = (string) $displayLabelNode;
							$displayLabelLocalizationToken = isset($displayLabelNode['localizationToken']) ? 
								(string) $displayLabelNode['localizationToken'] : NULL;
						}

						foreach( $formField->xpath( 'child::ErrorMessage' ) as $errorMessageNode ) {
							$errorMessage = (string) $errorMessageNode;
							$errorMessageLocalizationToken = isset($errorMessageNode['localizationToken']) ? 
								(string) $errorMessageNode['localizationToken'] : NULL;
						}

						foreach( $formField->xpath( 'child::ErrorHandler' ) as $errorHandlerNode ) {
							$errorHandler = (string) $errorHandlerNode;
						}

						foreach( $formField->xpath( 'child::PrependHTML' ) as $prependHTMLNode ) {
							$prependHTML = (string) $prependHTMLNode;
						}

						foreach( $formField->xpath( 'child::AppendHTML' ) as $appendHTMLNode ) {
							$appendHTML = (string) $appendHTMLNode;
						}

						foreach( $formField->xpath( 'child::LabelPrependHTML' ) as $labelPrependHTMLNode ) {
							$labelPrependHTML = (string) $labelPrependHTMLNode;
						}

						foreach( $formField->xpath( 'child::LabelAppendHTML' ) as $labelAppendHTMLNode ) {
							$labelAppendHTML = (string) $labelAppendHTMLNode;
						}

						foreach( $formField->xpath( 'child::InputPrependHTML' ) as $inputPrependHTMLNode ) {
							$inputPrependHTML = (string) $inputPrependHTMLNode;
						}

						foreach( $formField->xpath( 'child::InputAppendHTML' ) as $inputAppendHTMLNode ) {
							$inputAppendHTML = (string) $inputAppendHTMLNode;
						}

						foreach( $formField->xpath( 'child::CSSClasses' ) as $childCSSClassesNode ) {
							$cssClasses = (string) $childCSSClassesNode;
						}

						$newFormField = array(	'id' => $formFieldID,
												'type' => $formFieldType,
												'value' => $formFieldValue,
												'required' => $formFieldRequired,
												'seeder' => $formFieldValueSeeder,
												// 'displayLocalized' => $formFieldNodeDisplayLocalized,
												'displayLabel' => $displayLabel,
												'displayLabelToken' => $displayLabelLocalizationToken,
												'errorMessage' => $errorMessage,
												'errorMessageToken' => $errorMessageLocalizationToken,
												'errorHandler' => $errorHandler,
												'prependHTML' => $prependHTML,
												'appendHTML' => $appendHTML,
												'labelPrependHTML' => $labelPrependHTML,
												'labelAppendHTML' => $labelAppendHTML,
												'inputPrependHTML' => $inputPrependHTML,
												'inputAppendHTML' => $inputAppendHTML,
												'cssClasses' => $cssClasses,
											);

						if (!empty($containerChildren)) {
							$newFormField['children'] = $containerChildren;
						}

						$formFieldSetFormFields[$formFieldID] = $newFormField;
					}

					$newFormFieldSet['formFields'] = $formFieldSetFormFields;
					$formAttributeSetNodes[$formAttributeSetNodeID]['formFieldSets'][$formFieldSetID] = $newFormFieldSet;
				}

				foreach( $formAttributeSetNode->xpath( 'child::FormField' ) as $formField ) {
					$formFieldID = isset($formField['id']) ? (string) $formField['id'] : NULL;

					if ( !$formFieldID || trim($formFieldID) === '' ) {
						throw new ErrorException("No FormField ID specified in FormField: '" . $formField .
							"'.	 Please review your Forms.xml");
					}

					$formFieldType = isset($formField['type']) ? (string) $formField['type'] : NULL;
					$formFieldValue = isset($formField['value']) ? (string) $formField['value'] : NULL;
					$formFieldRequired =  isset($formField['required']) ? (string) $formField['required'] : NULL;
					$formFieldValueSeeder = isset($formField['seeder']) ? (string) $formField['seeder'] : NULL;

					if ( $formFieldRequired && $formFieldRequired === 'true' ) {
						$formFieldRequired = true;
					} else {
						$formFieldRequired = false;
					}

					if ( !$formFieldType || trim($formFieldType) === '' ) {
						throw new ErrorException('No FormField type specified in FormField: \'' . $formFieldID .
							'\'.	 Please review your Forms.xml');
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
							$formFieldChildValue = isset($formFieldChild['value']) ? (string) $formFieldChild['value'] : NULL;
							$formFieldChildRequired =  isset($formFieldChild['required']) ? (string) $formFieldChild['required'] : NULL;
							$formFieldChildValueSeeder = isset($formFieldChild['seeder']) ? (string) $formFieldChild['seeder'] : NULL;

							if ( !$formFieldChildType || trim($formFieldChildType) === '' ) {
								throw new ErrorException("No FormField type specified in FormField Child: '" . $formFieldChildID .
									"'.	 Please review your Forms.xml");
							} else if ($formFieldChildType === 'container') {
								throw new ErrorException("eGloo does not currently allow container FormFields to have container children.  Please review your Forms.xml");
							}

							if ( $formFieldChildRequired && $formFieldChildRequired === 'true' ) {
								$formFieldChildRequired = true;
							} else {
								$formFieldChildRequired = false;
							}

							$childDisplayLabel = null;
							$childErrorMessage = null;
							$childErrorHandler = null;
							$childPrependHTML = null;
							$childAppendHTML = null;
							$childLabelPrependHTML = null;
							$childLabelAppendHTML = null;
							$childInputPrependHTML = null;
							$childInputAppendHTML = null;
							$childCSSClasses = null;

							$childDisplayLabelLocalizationToken = null;
							$childErrorMessageLocalizationToken = null;

							foreach( $formFieldChild->xpath( 'child::DisplayLabel' ) as $childDisplayLabelNode ) {
								$childDisplayLabel = (string) $childDisplayLabelNode;
								$childDisplayLabelLocalizationToken = isset($childDisplayLabelNode['localizationToken']) ? 
									(string) $childDisplayLabelNode['localizationToken'] : NULL;
							}

							foreach( $formFieldChild->xpath( 'child::ErrorMessage' ) as $childErrorMessageNode ) {
								$childErrorMessage = (string) $childErrorMessageNode;
								$childErrorMessageLocalizationToken = isset($childErrorMessageNode['localizationToken']) ? 
									(string) $childErrorMessageNode['localizationToken'] : NULL;
							}

							foreach( $formFieldChild->xpath( 'child::ErrorHandler' ) as $childErrorHandlerNode ) {
								$childErrorHandler = (string) $childErrorHandlerNode;
							}

							foreach( $formFieldChild->xpath( 'child::PrependHTML' ) as $childPrependHTMLNode ) {
								$childPrependHTML = (string) $childPrependHTMLNode;
							}

							foreach( $formFieldChild->xpath( 'child::AppendHTML' ) as $childAppendHTMLNode ) {
								$childAppendHTML = (string) $childAppendHTMLNode;
							}

							foreach( $formFieldChild->xpath( 'child::LabelPrependHTML' ) as $childLabelPrependHTMLNode ) {
								$childLabelPrependHTML = (string) $childLabelPrependHTMLNode;
							}

							foreach( $formFieldChild->xpath( 'child::LabelAppendHTML' ) as $childLabelAppendHTMLNode ) {
								$childLabelAppendHTML = (string) $childLabelAppendHTMLNode;
							}

							foreach( $formFieldChild->xpath( 'child::InputPrependHTML' ) as $childInputPrependHTMLNode ) {
								$childInputPrependHTML = (string) $childInputPrependHTMLNode;
							}

							foreach( $formFieldChild->xpath( 'child::InputAppendHTML' ) as $childInputAppendHTMLNode ) {
								$childInputAppendHTML = (string) $childInputAppendHTMLNode;
							}

							foreach( $formFieldChild->xpath( 'child::CSSClasses' ) as $childCSSClassesNode ) {
								$childCSSClasses = (string) $childCSSClassesNode;
							}

							$newChildFormField = array(	'id' => $formFieldChildID,
													'type' => $formFieldChildType,
													'value' => $formFieldChildValue,
													'required' => $formFieldChildRequired,
													'seeder' => $formFieldChildValueSeeder,
													// 'displayLocalized' => $formFieldChildNodeDisplayLocalized,
													'displayLabel' => $childDisplayLabel,
													'displayLabelToken' => $childDisplayLabelLocalizationToken,
													'errorMessage' => $childErrorMessage,
													'errorMessageToken' => $childErrorMessageLocalizationToken,
													'errorHandler' => $childErrorHandler,
													'prependHTML' => $childPrependHTML,
													'appendHTML' => $childAppendHTML,
													'labelPrependHTML' => $childLabelPrependHTML,
													'labelAppendHTML' => $childLabelAppendHTML,
													'inputPrependHTML' => $childInputPrependHTML,
													'inputAppendHTML' => $childInputAppendHTML,
													'cssClasses' => $childCSSClasses,
												);

							$containerChildren[$formFieldChildID] = $newChildFormField;
						}
					}

					$displayLabel = null;
					$errorMessage = null;
					$errorHandler = null;
					$prependHTML = null;
					$appendHTML = null;
					$labelPrependHTML = null;
					$labelAppendHTML = null;
					$inputPrependHTML = null;
					$inputAppendHTML = null;
					$cssClasses = null;

					$displayLabelLocalizationToken = null;
					$errorMessageLocalizationToken = null;

					foreach( $formField->xpath( 'child::DisplayLabel' ) as $displayLabelNode ) {
						$displayLabel = (string) $displayLabelNode;
						$displayLabelLocalizationToken = isset($displayLabelNode['localizationToken']) ? 
							(string) $displayLabelNode['localizationToken'] : NULL;
					}

					foreach( $formField->xpath( 'child::ErrorMessage' ) as $errorMessageNode ) {
						$errorMessage = (string) $errorMessageNode;
						$errorMessageLocalizationToken = isset($errorMessageNode['localizationToken']) ? 
							(string) $errorMessageNode['localizationToken'] : NULL;
					}

					foreach( $formField->xpath( 'child::ErrorHandler' ) as $errorHandlerNode ) {
						$errorHandler = (string) $errorHandlerNode;
					}

					foreach( $formField->xpath( 'child::PrependHTML' ) as $childPrependHTMLNode ) {
						$prependHTML = (string) $childPrependHTMLNode;
					}

					foreach( $formField->xpath( 'child::AppendHTML' ) as $childAppendHTMLNode ) {
						$appendHTML = (string) $childAppendHTMLNode;
					}

					foreach( $formField->xpath( 'child::LabelPrependHTML' ) as $labelPrependHTMLNode ) {
						$labelPrependHTML = (string) $labelPrependHTMLNode;
					}

					foreach( $formField->xpath( 'child::LabelAppendHTML' ) as $labelAppendHTMLNode ) {
						$labelAppendHTML = (string) $labelAppendHTMLNode;
					}

					foreach( $formField->xpath( 'child::InputPrependHTML' ) as $inputPrependHTMLNode ) {
						$inputPrependHTML = (string) $inputPrependHTMLNode;
					}

					foreach( $formField->xpath( 'child::InputAppendHTML' ) as $inputAppendHTMLNode ) {
						$inputAppendHTML = (string) $inputAppendHTMLNode;
					}

					foreach( $formField->xpath( 'child::CSSClasses' ) as $childCSSClassesNode ) {
						$cssClasses = (string) $childCSSClassesNode;
					}

					$newFormField = array(	'id' => $formFieldID,
											'type' => $formFieldType,
											'value' => $formFieldValue,
											'required' => $formFieldRequired,
											'seeder' => $formFieldValueSeeder,
											// 'displayLocalized' => $formFieldNodeDisplayLocalized,
											'displayLabel' => $displayLabel,
											'displayLabelToken' => $displayLabelLocalizationToken,
											'errorMessage' => $errorMessage,
											'errorMessageToken' => $errorMessageLocalizationToken,
											'errorHandler' => $errorHandler,
											'prependHTML' => $prependHTML,
											'appendHTML' => $appendHTML,
											'labelPrependHTML' => $labelPrependHTML,
											'labelAppendHTML' => $labelAppendHTML,
											'inputPrependHTML' => $inputPrependHTML,
											'inputAppendHTML' => $inputAppendHTML,
											'cssClasses' => $cssClasses,
										);

					if (!empty($containerChildren)) {
						$newFormField['children'] = $containerChildren;
					}

					$formAttributeSetNodes[$formAttributeSetNodeID]['formFields'][$formFieldID] = $newFormField;
				}

				$dispatchCacheRegionHandler->storeObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'FormDirectorAttributeSetNodes::' . $formAttributeSetNodeID,
					$formAttributeSetNodes[$formAttributeSetNodeID], 'Dispatching', 0, true );
			}

			foreach( $formsXMLObject->xpath( '/tns:Forms/Form' ) as $formNode ) {
				$formNodeID = isset($formNode['id']) ? (string) $formNode['id'] : NULL;

				if ( !$formNodeID || trim($formNodeID) === '' ) {
					throw new ErrorException('No ID specified in form node. Please review your Forms.xml');
				}

				$formNodeDisplayLocalized = isset( $formNode['displayLocalized'] ) ? strtolower( (string) $formNode['displayLocalized'] ) : NULL;
				$formNodeDisplayLocalizer = isset( $formNode['displayLocalizer'] ) ? (string) $formNode['displayLocalizer'] : NULL;

				if ( !$formNodeDisplayLocalized || trim($formNodeDisplayLocalized) === '' ) {
					throw new ErrorException('No display localization setting specified in form node \'' . $formNodeID . '\'. Please review your Forms.xml');
				} else if ($formNodeDisplayLocalized === 'true') {
					$formNodeDisplayLocalized = true;
				} else if ($formNodeDisplayLocalized === 'false') {
					$formNodeDisplayLocalized = false;
				} else {
					throw new ErrorException('Invalid display localization setting specified in form node \'' . $formNodeID . '\'. Please review your Forms.xml');
				}

				// NOTE: If no DisplayLocalizer is specified, token replacement is based on DisplayLabels in Forms.xml
				// If no alternate DisplayLabel for a localization exists, the default is returned

				$formNodeInputLocalized = isset( $formNode['inputLocalized'] ) ? strtolower( (string) $formNode['inputLocalized'] ) : NULL;

				if ( !$formNodeInputLocalized || trim($formNodeInputLocalized) === '' ) {
					throw new ErrorException('No input localization setting specified in form node \'' . $formNodeID . '\'. Please review your Forms.xml');
				} else if ($formNodeInputLocalized === 'true') {
					$formNodeInputLocalized = true;
				} else if ($formNodeInputLocalized === 'false') {
					$formNodeInputLocalized = false;
				} else {
					throw new ErrorException('Invalid input localization setting specified in form node \'' . $formNodeID . '\'. Please review your Forms.xml');
				}

				$formNodeInputLocalizer = isset( $formNode['inputLocalizer'] ) ? (string) $formNode['inputLocalizer'] : NULL;

				// NOTE: If the Form states it needs InputLocalization support, but doesn't specify an InputLocalizer, it's in error
				if ( $formNodeDisplayLocalized && (!$formNodeInputLocalizer || trim($formNodeInputLocalizer) === '') ) {
					throw new ErrorException('No input localizer specified in form node \'' . $formNodeID . '\'. Please review your Forms.xml');
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

				$formNodeDAOConnectionName = isset($formNode['daoConnectionName']) ? (string) $formNode['daoConnectionName'] : NULL;

				if ( !$formNodeDAOConnectionName || trim($formNodeDAOConnectionName) === '' ) {
					throw new ErrorException('No DAOConnectionName specified in form node \'' . $formNodeID . '\'. Please review your Forms.xml');
				}

				$formNodeDAOFactory = isset($formNode['daoFactory']) ? (string) $formNode['daoFactory'] : NULL;

				if ( !$formNodeDAOFactory || trim($formNodeDAOFactory) === '' ) {
					throw new ErrorException('No DAOFactory specified in form node \'' . $formNodeID . '\'. Please review your Forms.xml');
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

				$formNodeAction = isset($formNode['action']) && trim((string) $formNode['action']) !== '' ? (string) $formNode['action'] : NULL;
				$formNodeEncoding = isset($formNode['encoding']) && trim((string) $formNode['encoding']) !== '' ? (string) $formNode['encoding'] : NULL;
				$formNodeMethod = isset($formNode['method']) && trim((string) $formNode['method']) !== '' ? (string) $formNode['method'] : 'post';

				$prependHTML = null;
				$appendHTML = null;
				$cssClasses = null;

				$formLegend = null;
				$formLegendLocalizationToken = null;

				foreach( $formNode->xpath( 'child::Legend' ) as $legend ) {
					$formLegend = (string) $legend;
					$formLegendLocalizationToken = isset($legend['legendToken']) ? (string) $legend['legendToken'] : NULL;
				}

				foreach( $formNode->xpath( 'child::PrependHTML' ) as $childPrependHTMLNode ) {
					$prependHTML = (string) $childPrependHTMLNode;
				}

				foreach( $formNode->xpath( 'child::AppendHTML' ) as $childAppendHTMLNode ) {
					$appendHTML = (string) $childAppendHTMLNode;
				}

				foreach( $formNode->xpath( 'child::CSSClasses' ) as $childCSSClassesNode ) {
					$cssClasses = (string) $childCSSClassesNode;
				}

				$formNodes[$formNodeID] = array(	'formID' => $formNodeID,
													'legend' => $formLegend,
													'legendToken' => $formLegendLocalizationToken,
													'displayLocalized' => $formNodeDisplayLocalized,
													'displayLocalizer' => $formNodeDisplayLocalizer,
													'inputLocalized' => $formNodeInputLocalized,
													'inputLocalizer' => $formNodeInputLocalizer,
													'validated' => $formNodeValidated,
													'secure' => $formNodeSecure,
													'daoConnectionName' => $formNodeDAOConnectionName,
													'daoFactory' => $formNodeDAOFactory,
													'DAO' => $formNodeDAO,
													'DTO' => $formNodeDTO,
													'validator' => $formNodeValidator,
													'dataFormatter' => $formNodeDataFormatter,
													'displayFormatter' => $formNodeDisplayFormatter,
													'formFieldSets' => array(),
													'formFields' => array(),
													'CRUDInfo' => array(),
													'prependHTML' => $prependHTML,
													'appendHTML' => $appendHTML,
													'cssClasses' => $cssClasses,
													'action' => $formNodeAction,
													'encoding' => $formNodeEncoding,
													'method' => $formNodeMethod
												);

				foreach( $formNode->xpath( 'child::FormFieldSet' ) as $formFieldSet ) {
					$formFieldSetID = isset($formFieldSet['id']) ? (string) $formFieldSet['id'] : NULL;

					if ( !$formFieldSetID || trim($formFieldSetID) === '' ) {
						throw new ErrorException("No FormFieldSet ID specified in FormFieldSet: '" . $formFieldSet .
							"'.	 Please review your Forms.xml");
					}

					// TODO: Add this back in when we support injection of FormAttributeSets where the Form localizer might not know how to localize the components
					// of the attribute set.  Uh, and this is only half complete.  Needs the localizer name (class)
					//
					// $formFieldSetNodeDisplayLocalized = isset( $formFieldSet['displayLocalized'] ) ? strtolower( (string) $formFieldSet['displayLocalized'] ) : NULL;
					// 
					// if ( !$formFieldSetNodeDisplayLocalized || trim($formFieldSetNodeDisplayLocalized) === '' ) {
					// 	throw new ErrorException('No localization setting specified in FormFieldSet \'' . $formFieldSetID . '\'. Please review your Forms.xml');
					// } else if ($formFieldSetNodeDisplayLocalized === 'true') {
					// 	$formFieldSetNodeDisplayLocalized = true;
					// } else if ($formFieldSetNodeDisplayLocalized === 'false') {
					// 	$formFieldSetNodeDisplayLocalized = false;
					// } else {
					// 	throw new ErrorException('Invalid localization setting specified in FormFieldSet \'' . $formFieldSetID . '\'. Please review your Forms.xml');
					// }

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
					$formFieldSetNodeRequired = isset($formFieldSet['required']) ? (string) $formFieldSet['required'] : NULL;

					if ( $formFieldSetNodeRequired && $formFieldSetNodeRequired === 'true' ) {
						$formFieldSetNodeRequired = true;
					} else {
						$formFieldSetNodeRequired = false;
					}

					$formFieldSetLegend = null;
					$formFieldSetLegendLocalizationToken = null;

					foreach( $formFieldSet->xpath( 'child::Legend' ) as $legend ) {
						$formFieldSetLegend = (string) $legend;
						$formFieldSetLegendLocalizationToken = isset($legend['legendToken']) ? (string) $legend['legendToken'] : NULL;
					}

					$formFieldSetErrorMessage = null;
					$formFieldSetErrorMessageLocalizationToken = null;

					foreach( $formFieldSet->xpath( 'child::ErrorMessage' ) as $legend ) {
						$formFieldSetErrorMessage = (string) $legend;
						$formFieldSetErrorMessageLocalizationToken = isset($legend['localizationToken']) ? (string) $legend['localizationToken'] : NULL;
					}

					$formFieldSetErrorHandler = null;

					foreach( $formFieldSet->xpath( 'child::ErrorHandler' ) as $errorHandlerNode ) {
						$formFieldSetErrorHandler = (string) $errorHandlerNode;
					}

					$newFormFieldSet = array(	'id' => $formFieldSetID,
												// 'displayLocalized' => $formFieldSetNodeDisplayLocalized,
												'validated' => $formFieldSetNodeValidated,
												'secure' => $formFieldSetNodeSecure,
												'validator' => $formFieldSetNodeValidator,
												'required' => $formFieldSetNodeRequired,
												'legend' => $formFieldSetLegend,
												'legendToken' => $formFieldSetLegendLocalizationToken,
												'formFields' => array(),
												'errorMessage' => $formFieldSetErrorMessage,
												'errorMessageToken' => $formFieldSetErrorMessageLocalizationToken,
												'errorHandler' => $formFieldSetErrorHandler
											);

					$formFieldSetFormFields = array();

					foreach( $formFieldSet->xpath( 'child::FormField' ) as $formField ) {
						$formFieldID = isset($formField['id']) ? (string) $formField['id'] : NULL;

						if ( !$formFieldID || trim($formFieldID) === '' ) {
							throw new ErrorException("No FormField ID specified in FormField: '" . $formField .
								"'.	 Please review your Forms.xml");
						}

						$formFieldType = isset($formField['type']) ? (string) $formField['type'] : NULL;
						$formFieldValue = isset($formField['value']) ? (string) $formField['value'] : NULL;
						$formFieldRequired =  isset($formField['required']) ? (string) $formField['required'] : NULL;
						$formFieldValueSeeder = isset($formField['seeder']) ? (string) $formField['seeder'] : NULL;

						if ( !$formFieldType || trim($formFieldType) === '' ) {
							throw new ErrorException('No FormField type specified in FormField: \'' . $formFieldID .
								'\'.	 Please review your Forms.xml');
						}

						if ( $formFieldRequired && $formFieldRequired === 'true' ) {
							$formFieldRequired = true;
						} else {
							$formFieldRequired = false;
						}

						// TODO: Add this back in when we support injection of FormAttributeSets where the Form localizer might not know how to localize the components
						// of the attribute set.  Uh, and this is only half complete.  Needs the localizer name (class)
						//
						// $formFieldNodeDisplayLocalized = isset( $formField['displayLocalized'] ) ? strtolower( (string) $formField['displayLocalized'] ) : NULL;
						// 
						// if ($formFieldNodeDisplayLocalized === 'true') {
						// 	$formFieldNodeDisplayLocalized = true;
						// } else if ($formFieldNodeDisplayLocalized === 'false') {
						// 	$formFieldNodeDisplayLocalized = false;
						// } else if ($formFieldType === 'hidden') {
						// 	$formFieldNodeDisplayLocalized = false;
						// } else if (isset($formFieldSetNodeDisplayLocalized)) {
						// 	$formFieldNodeDisplayLocalized = $formFieldSetNodeDisplayLocalized;
						// } else {
						// 	$formFieldNodeDisplayLocalized = false;
						// }

						$containerChildren = array();

						// Let's process the children of any FormField that fancies itself a container
						if ($formFieldType === 'container') {
							foreach( $formField->xpath( 'child::FormField' ) as $formFieldChild ) {
								$formFieldChildID = isset($formFieldChild['id']) ? (string) $formFieldChild['id'] : NULL;

								if ( !$formFieldChildID || trim($formFieldChildID) === '' ) {
									throw new ErrorException('No FormField ID specified in FormField Child: \'' . $formFieldChild .
										'\'.	 Please review your Forms.xml');
								}

								$formFieldChildType = isset($formFieldChild['type']) ? (string) $formFieldChild['type'] : NULL;
								$formFieldChildValue = isset($formFieldChild['value']) ? (string) $formFieldChild['value'] : NULL;
								$formFieldChildRequired =  isset($formFieldChild['required']) ? (string) $formFieldChild['required'] : NULL;
								$formFieldChildValueSeeder = isset($formFieldChild['seeder']) ? (string) $formFieldChild['seeder'] : NULL;

								if ( !$formFieldChildType || trim($formFieldChildType) === '' ) {
									throw new ErrorException("No FormField type specified in FormField Child: '" . $formFieldChildID .
										"'.	 Please review your Forms.xml");
								} else if ($formFieldChildType === 'container') {
									throw new ErrorException("eGloo does not currently allow container FormFields to have container children.  Please review your Forms.xml");
								}

								if ( $formFieldChildRequired && $formFieldChildRequired === 'true' ) {
									$formFieldChildRequired = true;
								} else {
									$formFieldChildRequired = false;
								}

								// TODO: Add this back in when we support injection of FormAttributeSets where the Form localizer might not know how to localize the components
								// of the attribute set.  Uh, and this is only half complete.  Needs the localizer name (class)
								//
								// $formFieldChildNodeDisplayLocalized = isset( $formFieldChild['displayLocalized'] ) ? strtolower( (string) $formFieldChild['displayLocalized'] ) : NULL;
								// 
								// if ($formFieldChildNodeDisplayLocalized === 'true') {
								// 	$formFieldChildNodeDisplayLocalized = true;
								// } else if ($formFieldChildNodeDisplayLocalized === 'false') {
								// 	$formFieldChildNodeDisplayLocalized = false;
								// } else if ($formFieldChildNodeDisplayLocalized === 'hidden') {
								// 	$formFieldChildNodeDisplayLocalized = false;
								// } else if (isset($formFieldNodeDisplayLocalized)) {
								// 	$formFieldChildNodeDisplayLocalized = $formFieldNodeDisplayLocalized;
								// } else {
								// 	$formFieldChildNodeDisplayLocalized = false;
								// }

								$childDisplayLabel = null;
								$childErrorMessage = null;
								$childErrorHandler = null;
								$childPrependHTML = null;
								$childAppendHTML = null;
								$childLabelPrependHTML = null;
								$childLabelAppendHTML = null;
								$childInputPrependHTML = null;
								$childInputAppendHTML = null;
								$childCSSClasses = null;

								$childDisplayLabelLocalizationToken = null;
								$childErrorMessageLocalizationToken = null;

								foreach( $formFieldChild->xpath( 'child::DisplayLabel' ) as $childDisplayLabelNode ) {
									$childDisplayLabel = (string) $childDisplayLabelNode;
									$childDisplayLabelLocalizationToken = isset($childDisplayLabelNode['localizationToken']) ? 
										(string) $childDisplayLabelNode['localizationToken'] : NULL;
								}

								foreach( $formFieldChild->xpath( 'child::ErrorMessage' ) as $childErrorMessageNode ) {
									$childErrorMessage = (string) $childErrorMessageNode;
									$childErrorMessageLocalizationToken = isset($childErrorMessageNode['localizationToken']) ? 
										(string) $childErrorMessageNode['localizationToken'] : NULL;
								}

								foreach( $formFieldChild->xpath( 'child::ErrorHandler' ) as $childErrorHandlerNode ) {
									$childErrorHandler = (string) $childErrorHandlerNode;
								}

								foreach( $formFieldChild->xpath( 'child::PrependHTML' ) as $childPrependHTMLNode ) {
									$childPrependHTML = (string) $childPrependHTMLNode;
								}

								foreach( $formFieldChild->xpath( 'child::AppendHTML' ) as $childAppendHTMLNode ) {
									$childAppendHTML = (string) $childAppendHTMLNode;
								}

								foreach( $formFieldChild->xpath( 'child::LabelPrependHTML' ) as $childLabelPrependHTMLNode ) {
									$childLabelPrependHTML = (string) $childLabelPrependHTMLNode;
								}

								foreach( $formFieldChild->xpath( 'child::LabelAppendHTML' ) as $childLabelAppendHTMLNode ) {
									$childLabelAppendHTML = (string) $childLabelAppendHTMLNode;
								}

								foreach( $formFieldChild->xpath( 'child::InputPrependHTML' ) as $childInputPrependHTMLNode ) {
									$childInputPrependHTML = (string) $childInputPrependHTMLNode;
								}

								foreach( $formFieldChild->xpath( 'child::InputAppendHTML' ) as $childInputAppendHTMLNode ) {
									$childInputAppendHTML = (string) $childInputAppendHTMLNode;
								}

								foreach( $formFieldChild->xpath( 'child::CSSClasses' ) as $childCSSClassesNode ) {
									$childCSSClasses = (string) $childCSSClassesNode;
								}

								$newChildFormField = array(	'id' => $formFieldChildID,
														'type' => $formFieldChildType,
														'value' => $formFieldChildValue,
														'required' => $formFieldChildRequired,
														'seeder' => $formFieldChildValueSeeder,
														// 'displayLocalized' => $formFieldChildNodeDisplayLocalized,
														'displayLabel' => $childDisplayLabel,
														'displayLabelToken' => $childDisplayLabelLocalizationToken,
														'errorMessage' => $childErrorMessage,
														'errorMessageToken' => $childErrorMessageLocalizationToken,
														'errorHandler' => $childErrorHandler,
														'prependHTML' => $childPrependHTML,
														'appendHTML' => $childAppendHTML,
														'labelPrependHTML' => $childLabelPrependHTML,
														'labelAppendHTML' => $childLabelAppendHTML,
														'inputPrependHTML' => $childInputPrependHTML,
														'inputAppendHTML' => $childInputAppendHTML,
														'cssClasses' => $childCSSClasses,
													);

								$containerChildren[$formFieldChildID] = $newChildFormField;
							}
						}

						$displayLabel = null;
						$errorMessage = null;
						$errorHandler = null;
						$prependHTML = null;
						$appendHTML = null;
						$labelPrependHTML = null;
						$labelAppendHTML = null;
						$inputPrependHTML = null;
						$inputAppendHTML = null;
						$cssClasses = null;

						$displayLabelLocalizationToken = null;
						$errorMessageLocalizationToken = null;

						foreach( $formField->xpath( 'child::DisplayLabel' ) as $displayLabelNode ) {
							$displayLabel = (string) $displayLabelNode;
							$displayLabelLocalizationToken = isset($displayLabelNode['localizationToken']) ? 
								(string) $displayLabelNode['localizationToken'] : NULL;
						}

						foreach( $formField->xpath( 'child::ErrorMessage' ) as $errorMessageNode ) {
							$errorMessage = (string) $errorMessageNode;
							$errorMessageLocalizationToken = isset($errorMessageNode['localizationToken']) ? 
								(string) $errorMessageNode['localizationToken'] : NULL;
						}

						foreach( $formField->xpath( 'child::ErrorHandler' ) as $errorHandlerNode ) {
							$errorHandler = (string) $errorHandlerNode;
						}

						foreach( $formField->xpath( 'child::PrependHTML' ) as $prependHTMLNode ) {
							$prependHTML = (string) $prependHTMLNode;
						}

						foreach( $formField->xpath( 'child::AppendHTML' ) as $appendHTMLNode ) {
							$appendHTML = (string) $appendHTMLNode;
						}

						foreach( $formField->xpath( 'child::LabelPrependHTML' ) as $labelPrependHTMLNode ) {
							$labelPrependHTML = (string) $labelPrependHTMLNode;
						}

						foreach( $formField->xpath( 'child::LabelAppendHTML' ) as $labelAppendHTMLNode ) {
							$labelAppendHTML = (string) $labelAppendHTMLNode;
						}

						foreach( $formField->xpath( 'child::InputPrependHTML' ) as $inputPrependHTMLNode ) {
							$inputPrependHTML = (string) $inputPrependHTMLNode;
						}

						foreach( $formField->xpath( 'child::InputAppendHTML' ) as $inputAppendHTMLNode ) {
							$inputAppendHTML = (string) $inputAppendHTMLNode;
						}

						foreach( $formField->xpath( 'child::CSSClasses' ) as $childCSSClassesNode ) {
							$cssClasses = (string) $childCSSClassesNode;
						}

						$newFormField = array(	'id' => $formFieldID,
												'type' => $formFieldType,
												'value' => $formFieldValue,
												'required' => $formFieldRequired,
												'seeder' => $formFieldValueSeeder,
												// 'displayLocalized' => $formFieldNodeDisplayLocalized,
												'displayLabel' => $displayLabel,
												'displayLabelToken' => $displayLabelLocalizationToken,
												'errorMessage' => $errorMessage,
												'errorMessageToken' => $errorMessageLocalizationToken,
												'errorHandler' => $errorHandler,
												'prependHTML' => $prependHTML,
												'appendHTML' => $appendHTML,
												'labelPrependHTML' => $labelPrependHTML,
												'labelAppendHTML' => $labelAppendHTML,
												'inputPrependHTML' => $inputPrependHTML,
												'inputAppendHTML' => $inputAppendHTML,
												'cssClasses' => $cssClasses,
											);

						if (!empty($containerChildren)) {
							$newFormField['children'] = $containerChildren;
						}

						$formFieldSetFormFields[$formFieldID] = $newFormField;
					}

					$newFormFieldSet['formFields'] = $formFieldSetFormFields;
					$formNodes[$formNodeID]['formFieldSets'][$formFieldSetID] = $newFormFieldSet;
				}

				// FormField Nodes
				foreach( $formNode->xpath( 'child::FormField' ) as $formField ) {
					$formFieldID = isset($formField['id']) ? (string) $formField['id'] : NULL;

					if ( !$formFieldID || trim($formFieldID) === '' ) {
						throw new ErrorException("No FormField ID specified in FormField: '" . $formField .
							"'.	 Please review your Forms.xml");
					}

					$formFieldType = isset($formField['type']) ? (string) $formField['type'] : NULL;
					$formFieldValue = isset($formField['value']) ? (string) $formField['value'] : NULL;
					$formFieldRequired =  isset($formField['required']) ? (string) $formField['required'] : NULL;
					$formFieldValueSeeder = isset($formField['seeder']) ? (string) $formField['seeder'] : NULL;

					if ( !$formFieldType || trim($formFieldType) === '' ) {
						throw new ErrorException('No FormField type specified in FormField: \'' . $formFieldID .
							'\'.	 Please review your Forms.xml');
					}

					if ( $formFieldRequired && $formFieldRequired === 'true' ) {
						$formFieldRequired = true;
					} else {
						$formFieldRequired = false;
					}

					// TODO: Add this back in when we support injection of FormAttributeSets where the Form localizer might not know how to localize the components
					// of the attribute set.  Uh, and this is only half complete.  Needs the localizer name (class)
					//
					// $formFieldNodeDisplayLocalized = isset( $formField['displayLocalized'] ) ? strtolower( (string) $formField['displayLocalized'] ) : NULL;
					// 
					// if ($formFieldNodeDisplayLocalized === 'true') {
					// 	$formFieldNodeDisplayLocalized = true;
					// } else if ($formFieldNodeDisplayLocalized === 'false') {
					// 	$formFieldNodeDisplayLocalized = false;
					// } else if ($formFieldType === 'hidden') {
					// 	$formFieldNodeDisplayLocalized = false;
					// } else if (isset($formFieldSetNodeDisplayLocalized)) {
					// 	$formFieldNodeDisplayLocalized = $formFieldSetNodeDisplayLocalized;
					// } else {
					// 	$formFieldNodeDisplayLocalized = false;
					// }

					$containerChildren = array();

					if ($formFieldType === 'container') {
						foreach( $formField->xpath( 'child::FormField' ) as $formFieldChild ) {
							$formFieldChildID = isset($formFieldChild['id']) ? (string) $formFieldChild['id'] : NULL;

							if ( !$formFieldChildID || trim($formFieldChildID) === '' ) {
								throw new ErrorException('No FormField ID specified in FormField Child: \'' . $formFieldChild .
									'\'.	 Please review your Forms.xml');
							}

							$formFieldChildType = isset($formFieldChild['type']) ? (string) $formFieldChild['type'] : NULL;
							$formFieldChildValue = isset($formFieldChild['value']) ? (string) $formFieldChild['value'] : NULL;
							$formFieldChildRequired =  isset($formFieldChild['required']) ? (string) $formFieldChild['required'] : NULL;
							$formFieldChildValueSeeder = isset($formFieldChild['seeder']) ? (string) $formFieldChild['seeder'] : NULL;

							if ( !$formFieldChildType || trim($formFieldChildType) === '' ) {
								throw new ErrorException("No FormField type specified in FormField Child: '" . $formFieldChildID .
									"'.	 Please review your Forms.xml");
							} else if ($formFieldChildType === 'container') {
								throw new ErrorException("eGloo does not currently allow container FormFields to have container children.  Please review your Forms.xml");
							}

							if ( $formFieldChildRequired && $formFieldChildRequired === 'true' ) {
								$formFieldChildRequired = true;
							} else {
								$formFieldChildRequired = false;
							}

							// TODO: Add this back in when we support injection of FormAttributeSets where the Form localizer might not know how to localize the components
							// of the attribute set.  Uh, and this is only half complete.  Needs the localizer name (class)
							//
							// $formFieldChildNodeDisplayLocalized = isset( $formFieldChild['displayLocalized'] ) ? strtolower( (string) $formFieldChild['displayLocalized'] ) : NULL;
							// 
							// if ($formFieldChildNodeDisplayLocalized === 'true') {
							// 	$formFieldChildNodeDisplayLocalized = true;
							// } else if ($formFieldChildNodeDisplayLocalized === 'false') {
							// 	$formFieldChildNodeDisplayLocalized = false;
							// } else if ($formFieldChildNodeDisplayLocalized === 'hidden') {
							// 	$formFieldChildNodeDisplayLocalized = false;
							// } else if (isset($formFieldNodeDisplayLocalized)) {
							// 	$formFieldChildNodeDisplayLocalized = $formFieldNodeDisplayLocalized;
							// } else {
							// 	$formFieldChildNodeDisplayLocalized = false;
							// }

							$childDisplayLabel = null;
							$childErrorMessage = null;
							$childErrorHandler = null;
							$childPrependHTML = null;
							$childAppendHTML = null;
							$childLabelPrependHTML = null;
							$childLabelAppendHTML = null;
							$childInputPrependHTML = null;
							$childInputAppendHTML = null;
							$childCSSClasses = null;

							$childDisplayLabelLocalizationToken = null;
							$childErrorMessageLocalizationToken = null;

							foreach( $formFieldChild->xpath( 'child::DisplayLabel' ) as $childDisplayLabelNode ) {
								$childDisplayLabel = (string) $childDisplayLabelNode;
								$childDisplayLabelLocalizationToken = isset($childDisplayLabelNode['localizationToken']) ? 
									(string) $childDisplayLabelNode['localizationToken'] : NULL;
							}

							foreach( $formFieldChild->xpath( 'child::ErrorMessage' ) as $childErrorMessageNode ) {
								$childErrorMessage = (string) $childErrorMessageNode;
								$childErrorMessageLocalizationToken = isset($childErrorMessageNode['localizationToken']) ? 
									(string) $childErrorMessageNode['localizationToken'] : NULL;
							}

							foreach( $formFieldChild->xpath( 'child::ErrorHandler' ) as $childErrorHandlerNode ) {
								$childErrorHandler = (string) $childErrorHandlerNode;
							}

							foreach( $formFieldChild->xpath( 'child::PrependHTML' ) as $childPrependHTMLNode ) {
								$childPrependHTML = (string) $childPrependHTMLNode;
							}

							foreach( $formFieldChild->xpath( 'child::AppendHTML' ) as $childAppendHTMLNode ) {
								$childAppendHTML = (string) $childAppendHTMLNode;
							}

							foreach( $formFieldChild->xpath( 'child::LabelPrependHTML' ) as $childLabelPrependHTMLNode ) {
								$childLabelPrependHTML = (string) $childLabelPrependHTMLNode;
							}

							foreach( $formFieldChild->xpath( 'child::LabelAppendHTML' ) as $childLabelAppendHTMLNode ) {
								$childLabelAppendHTML = (string) $childLabelAppendHTMLNode;
							}

							foreach( $formFieldChild->xpath( 'child::InputPrependHTML' ) as $childInputPrependHTMLNode ) {
								$childInputPrependHTML = (string) $childInputPrependHTMLNode;
							}

							foreach( $formFieldChild->xpath( 'child::InputAppendHTML' ) as $childInputAppendHTMLNode ) {
								$childInputAppendHTML = (string) $childInputAppendHTMLNode;
							}

							foreach( $formFieldChild->xpath( 'child::CSSClasses' ) as $childCSSClassesNode ) {
								$childCSSClasses = (string) $childCSSClassesNode;
							}

							$newChildFormField = array(	'id' => $formFieldChildID,
													'type' => $formFieldChildType,
													'value' => $formFieldChildValue,
													'required' => $formFieldChildRequired,
													'seeder' => $formFieldChildValueSeeder,
													// 'displayLocalized' => $formFieldChildNodeDisplayLocalized,
													'displayLabel' => $childDisplayLabel,
													'displayLabelToken' => $childDisplayLabelLocalizationToken,
													'errorMessage' => $childErrorMessage,
													'errorMessageToken' => $childErrorMessageLocalizationToken,
													'errorHandler' => $childErrorHandler,
													'prependHTML' => $childPrependHTML,
													'appendHTML' => $childAppendHTML,
													'labelPrependHTML' => $childLabelPrependHTML,
													'labelAppendHTML' => $childLabelAppendHTML,
													'inputPrependHTML' => $childInputPrependHTML,
													'inputAppendHTML' => $childInputAppendHTML,
													'cssClasses' => $childCSSClasses,
												);

							$containerChildren[$formFieldChildID] = $newChildFormField;
						}
					}

					$displayLabel = null;
					$errorMessage = null;
					$errorHandler = null;
					$prependHTML = null;
					$appendHTML = null;
					$labelPrependHTML = null;
					$labelAppendHTML = null;
					$inputPrependHTML = null;
					$inputAppendHTML = null;
					$cssClasses = null;

					$displayLabelLocalizationToken = null;
					$errorMessageLocalizationToken = null;

					foreach( $formField->xpath( 'child::DisplayLabel' ) as $displayLabelNode ) {
						$displayLabel = (string) $displayLabelNode;
						$displayLabelLocalizationToken = isset($displayLabelNode['localizationToken']) ? 
							(string) $displayLabelNode['localizationToken'] : NULL;
					}

					foreach( $formField->xpath( 'child::ErrorMessage' ) as $errorMessageNode ) {
						$errorMessage = (string) $errorMessageNode;
						$errorMessageLocalizationToken = isset($errorMessageNode['localizationToken']) ? 
							(string) $errorMessageNode['localizationToken'] : NULL;
					}

					foreach( $formField->xpath( 'child::ErrorHandler' ) as $errorHandlerNode ) {
						$errorHandler = (string) $errorHandlerNode;
					}

					foreach( $formField->xpath( 'child::PrependHTML' ) as $childPrependHTMLNode ) {
						$prependHTML = (string) $childPrependHTMLNode;
					}

					foreach( $formField->xpath( 'child::AppendHTML' ) as $childAppendHTMLNode ) {
						$appendHTML = (string) $childAppendHTMLNode;
					}

					foreach( $formField->xpath( 'child::LabelPrependHTML' ) as $labelPrependHTMLNode ) {
						$labelPrependHTML = (string) $labelPrependHTMLNode;
					}

					foreach( $formField->xpath( 'child::LabelAppendHTML' ) as $labelAppendHTMLNode ) {
						$labelAppendHTML = (string) $labelAppendHTMLNode;
					}

					foreach( $formField->xpath( 'child::InputPrependHTML' ) as $inputPrependHTMLNode ) {
						$inputPrependHTML = (string) $inputPrependHTMLNode;
					}

					foreach( $formField->xpath( 'child::InputAppendHTML' ) as $inputAppendHTMLNode ) {
						$inputAppendHTML = (string) $inputAppendHTMLNode;
					}

					foreach( $formField->xpath( 'child::CSSClasses' ) as $childCSSClassesNode ) {
						$cssClasses = (string) $childCSSClassesNode;
					}

					$newFormField = array(	'id' => $formFieldID,
											'type' => $formFieldType,
											'value' => $formFieldValue,
											'required' => $formFieldRequired,
											'seeder' => $formFieldValueSeeder,
											// 'displayLocalized' => $formFieldNodeDisplayLocalized,
											'displayLabel' => $displayLabel,
											'displayLabelToken' => $displayLabelLocalizationToken,
											'errorMessage' => $errorMessage,
											'errorMessageToken' => $errorMessageLocalizationToken,
											'errorHandler' => $errorHandler,
											'prependHTML' => $prependHTML,
											'appendHTML' => $appendHTML,
											'labelPrependHTML' => $labelPrependHTML,
											'labelAppendHTML' => $labelAppendHTML,
											'inputPrependHTML' => $inputPrependHTML,
											'inputAppendHTML' => $inputAppendHTML,
											'cssClasses' => $cssClasses,
										);

					if (!empty($containerChildren)) {
						$newFormField['children'] = $containerChildren;
					}

					$formNodes[$formNodeID]['formFields'][$formFieldID] = $newFormField;
				}

				foreach( $formNode->xpath( 'child::CRUD' ) as $crudNode ) {
					$formNodes[$formNodeID]['CRUD'] = array();

					foreach( $crudNode->xpath( 'child::Create' ) as $createNode ) {
						$createTriggers = array();

						foreach( $createNode->xpath( 'child::Trigger' ) as $triggerNode ) {
							$newTrigger = array();

							$newTrigger['type'] = isset($triggerNode['triggerType']) ? (string) $triggerNode['triggerType'] : NULL;
							$newTrigger['key'] = isset($triggerNode['triggerKey']) ? (string) $triggerNode['triggerKey'] : NULL;
							$newTrigger['value'] = isset($triggerNode['triggerValue']) ? (string) $triggerNode['triggerValue'] : NULL;

							$createTriggers[] = $newTrigger;
						}

						$formNodes[$formNodeID]['CRUD']['create'] = $createTriggers;
					}

					foreach( $crudNode->xpath( 'child::Read' ) as $readNode ) {
						$readTriggers = array();

						foreach( $readNode->xpath( 'child::Trigger' ) as $triggerNode ) {
							$newTrigger = array();

							$newTrigger['type'] = isset($triggerNode['triggerType']) ? (string) $triggerNode['triggerType'] : NULL;
							$newTrigger['key'] = isset($triggerNode['triggerKey']) ? (string) $triggerNode['triggerKey'] : NULL;
							$newTrigger['value'] = isset($triggerNode['triggerValue']) ? (string) $triggerNode['triggerValue'] : NULL;

							$readTriggers[] = $newTrigger;
						}

						$formNodes[$formNodeID]['CRUD']['read'] = $readTriggers;
					}

					foreach( $crudNode->xpath( 'child::Update' ) as $updateNode ) {
						$updateTriggers = array();

						foreach( $updateNode->xpath( 'child::Trigger' ) as $triggerNode ) {
							$newTrigger = array();

							$newTrigger['type'] = isset($triggerNode['triggerType']) ? (string) $triggerNode['triggerType'] : NULL;
							$newTrigger['key'] = isset($triggerNode['triggerKey']) ? (string) $triggerNode['triggerKey'] : NULL;
							$newTrigger['value'] = isset($triggerNode['triggerValue']) ? (string) $triggerNode['triggerValue'] : NULL;

							$updateTriggers[] = $newTrigger;
						}

						$formNodes[$formNodeID]['CRUD']['update'] = $updateTriggers;
					}

					foreach( $crudNode->xpath( 'child::Destroy' ) as $destroyNode ) {
						$destroyTriggers = array();

						foreach( $destroyNode->xpath( 'child::Trigger' ) as $triggerNode ) {
							$newTrigger = array();

							$newTrigger['type'] = isset($triggerNode['triggerType']) ? (string) $triggerNode['triggerType'] : NULL;
							$newTrigger['key'] = isset($triggerNode['triggerKey']) ? (string) $triggerNode['triggerKey'] : NULL;
							$newTrigger['value'] = isset($triggerNode['triggerValue']) ? (string) $triggerNode['triggerValue'] : NULL;

							$destroyTriggers[] = $newTrigger;
						}

						$formNodes[$formNodeID]['CRUD']['destroy'] = $destroyTriggers;
					}
				}

				$dispatchCacheRegionHandler->storeObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'FormDirectorNodes::' . $formNodeID,
					$formNodes[$formNodeID], 'Dispatching', 0, true );
			}

		}

		$this->_formNodes = $formNodes;
		$this->_formAttributeSetNodes = $formAttributeSetNodes;

		$dispatchCacheRegionHandler->storeObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'FormDirectorNodes',
			$this->_formNodes, 'Dispatching', 0, true );

		$dispatchCacheRegionHandler->storeObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'FormDirectorAttributeSetNodes',
			$this->_formAttributeSetNodes, 'Dispatching', 0, true );
	}

	public function buildForm( $form_name, $parameters = null ) {
		$retVal = null;

		$formNode = $this->getFormNodeDefinition( $form_name );

		$newFormObj = null;

		if ( isset($formNode['secure']) && $formNode['secure'] ) {
			$newFormObj = new SecureForm( $formNode['formID'] );
		} else if ( isset($formNode['validated']) && $formNode['validated'] ) {
			$newFormObj = new ValidatedForm( $formNode['formID'] );
			$newFormObj->setValidator( $formNode['validator'] );
		} else {
			$newFormObj = new Form( $formNode['formID'] );
		}

		// TODO "valid for" length of time in validation
		// Not just checksum
		$newFormObj->setDataFormatter( $formNode['dataFormatter'] );
		$newFormObj->setDisplayFormatter( $formNode['displayFormatter'] );

		$newFormObj->setDisplayLocalized( $formNode['displayLocalized'] );
		$newFormObj->setDisplayLocalizer( $formNode['displayLocalizer'] );

		$newFormObj->setInputLocalized( $formNode['inputLocalized'] );
		$newFormObj->setInputLocalizer( $formNode['inputLocalizer'] );

		$newFormObj->setAppendHTML( $formNode['appendHTML'] );
		$newFormObj->setPrependHTML( $formNode['prependHTML'] );
		$newFormObj->setCSSClasses( $formNode['cssClasses']);

		$newFormObj->setFormLegend( $formNode['legend'] );
		$newFormObj->setFormLegendToken( $formNode['legendToken'] );

		$newFormObj->setFormDAOConnectionName( $formNode['daoConnectionName'] );
		$newFormObj->setFormDAOFactory( $formNode['daoFactory'] );
		$newFormObj->setFormDAO( $formNode['DAO'] );
		$newFormObj->setFormDTO( $formNode['DTO'] );

		$newFormObj->setAction( $formNode['action'] );
		$newFormObj->setEncoding( $formNode['encoding'] );
		$newFormObj->setMethod( $formNode['method'] );

		foreach( $formNode['formFieldSets'] as $formFieldSet ) {
			$newFormFieldSetObj = null;

			if ( isset($formFieldSet['secure']) && $formFieldSet['secure'] ) {
				$newFormFieldSetObj = new SecureFormFieldSet( $formFieldSet['id'] );
			} else if ( isset($formFieldSet['validated']) && $formFieldSet['validated'] ) {
				$newFormFieldSetObj = new ValidatedFormFieldSet( $formFieldSet['id'] );

				// If this FormFieldSet has its own validator, attach it.  The Form validator
				// gets invoked regardless
				if ( isset($formFieldSet['validator']) ) {
					$newFormFieldSetObj->setValidator( $formFieldSet['validator'] );
				}
			} else {
				$newFormFieldSetObj = new FormFieldSet( $formFieldSet['id'] );
			}

			if ( isset($formFieldSet['required']) ) {
				$newFormFieldSetObj->setIsRequired( $formFieldSet['required'] );
			}

			if ( isset($formFieldSet['legend']) ) {
				$newFormFieldSetObj->setLegend( $formFieldSet['legend'] );
			}

			if ( isset($formFieldSet['legendToken']) ) {
				$newFormFieldSetObj->setLegendToken( $formFieldSet['legendToken'] );
			}

			if ( isset($formFieldSet['errorMessage']) ) {
				$newFormFieldSetObj->setErrorMessage( $formFieldSet['errorMessage'] );
			}

			if ( isset($formFieldSet['errorMessageToken']) ) {
				$newFormFieldSetObj->setErrorMessageToken( $formFieldSet['errorMessageToken'] );
			}

			if ( isset($formFieldSet['errorHandler']) ) {
				$newFormFieldSetObj->setErrorHandler( $formFieldSet['errorHandler'] );
			}

			foreach( $formFieldSet['formFields'] as $formField ) {
				$newFormFieldObj = null;

				if ( isset($formField['secure']) && $formField['secure'] ) {
					$newFormFieldObj = new SecureFormField( $formField['id'] );
				} else if ( isset($formField['validated']) && $formField['validated'] ) {
					$newFormFieldObj = new ValidatedFormField( $formField['id'] );
				} else {
					$newFormFieldObj = new FormField( $formField['id'] );
				}

				$newFormFieldObj->setFormFieldType( $formField['type'] );
				$newFormFieldObj->setIsRequired( $formField['required'] );

				if ( isset($formField['value']) ) {
					$newFormFieldObj->setDefaultValue( $formField['value'] );
					$newFormFieldObj->setValue( $formField['value'] );
				}

				if ( isset($formField['seeder']) ) {
					$newFormFieldObj->setValueSeederName( $formField['seeder'] );
				}

				$newFormFieldObj->setDisplayLabel( $formField['displayLabel'] );
				$newFormFieldObj->setDisplayLabelToken( $formField['displayLabelToken'] );
				$newFormFieldObj->setErrorMessage( $formField['errorMessage'] );
				$newFormFieldObj->setErrorMessageToken( $formField['errorMessageToken'] );
				$newFormFieldObj->setErrorHandler( $formField['errorHandler'] );
				$newFormFieldObj->setPrependHTML( $formField['prependHTML'] );
				$newFormFieldObj->setAppendHTML( $formField['appendHTML'] );
				$newFormFieldObj->setLabelPrependHTML( $formField['labelPrependHTML'] );
				$newFormFieldObj->setLabelAppendHTML( $formField['labelAppendHTML'] );
				$newFormFieldObj->setInputPrependHTML( $formField['inputPrependHTML'] );
				$newFormFieldObj->setInputAppendHTML( $formField['inputAppendHTML'] );
				$newFormFieldObj->setCSSClasses( $formField['cssClasses'] );

				if ( $formField['type'] === 'container' ) {
					foreach( $formField['children'] as $containerChild ) {
						$newChildFormFieldObj = null;

						if ( isset($containerChild['secure']) && $containerChild['secure'] ) {
							$newChildFormFieldObj = new SecureFormField( $containerChild['id'] );
						} else if ( isset($containerChild['validated']) && $containerChild['validated'] ) {
							$newChildFormFieldObj = new ValidatedFormField( $containerChild['id'] );
						} else {
							$newChildFormFieldObj = new FormField( $containerChild['id'] );
						}

						$newChildFormFieldObj->setFormFieldType( $containerChild['type'] );
						$newChildFormFieldObj->setIsRequired( $containerChild['required'] );

						if ( isset($containerChild['value']) ) {
							$newChildFormFieldObj->setDefaultValue( $containerChild['value'] );
							$newChildFormFieldObj->setValue( $containerChild['value'] );
						}

						if ( isset($containerChild['seeder']) ) {
							$newChildFormFieldObj->setValueSeederName( $containerChild['seeder'] );
						}

						$newChildFormFieldObj->setDisplayLabel( $containerChild['displayLabel'] );
						$newChildFormFieldObj->setDisplayLabelToken( $containerChild['displayLabelToken'] );
						$newChildFormFieldObj->setErrorMessage( $containerChild['errorMessage'] );
						$newChildFormFieldObj->setErrorMessageToken( $containerChild['errorMessageToken'] );
						$newChildFormFieldObj->setErrorHandler( $containerChild['errorHandler'] );
						$newChildFormFieldObj->setPrependHTML( $containerChild['prependHTML'] );
						$newChildFormFieldObj->setAppendHTML( $containerChild['appendHTML'] );
						$newChildFormFieldObj->setLabelPrependHTML( $containerChild['labelPrependHTML'] );
						$newChildFormFieldObj->setLabelAppendHTML( $containerChild['labelAppendHTML'] );
						$newChildFormFieldObj->setInputPrependHTML( $containerChild['inputPrependHTML'] );
						$newChildFormFieldObj->setInputAppendHTML( $containerChild['inputAppendHTML'] );
						$newChildFormFieldObj->setCSSClasses( $containerChild['cssClasses'] );

						$newFormFieldObj->addFormField( $containerChild['id'], $newChildFormFieldObj );
					}
				}

				$newFormFieldSetObj->addFormField( $formField['id'], $newFormFieldObj );
			}

			$newFormObj->addFormFieldSet( $formFieldSet['id'], $newFormFieldSetObj );
		}

		foreach( $formNode['formFields'] as $formField ) {
			$newFormFieldObj = null;

			if ( isset($formField['secure']) && $formField['secure'] ) {
				$newFormFieldObj = new SecureFormField( $formField['id'] );
			} else if ( isset($formField['validated']) && $formField['validated'] ) {
				$newFormFieldObj = new ValidatedFormField( $formField['id'] );
			} else {
				$newFormFieldObj = new FormField( $formField['id'] );
			}

			$newFormFieldObj = null;

			if ( isset($formField['secure']) && $formField['secure'] ) {
				$newFormFieldObj = new SecureFormField( $formField['id'] );
			} else if ( isset($formField['validated']) && $formField['validated'] ) {
				$newFormFieldObj = new ValidatedFormField( $formField['id'] );
			} else {
				$newFormFieldObj = new FormField( $formField['id'] );
			}

			$newFormFieldObj->setFormFieldType( $formField['type'] );
			$newFormFieldObj->setIsRequired( $formField['required'] );

			if ( isset($formField['value']) ) {
				$newFormFieldObj->setDefaultValue( $formField['value'] );
				$newFormFieldObj->setValue( $formField['value'] );
			}

			if ( isset($formField['seeder']) ) {
				$newFormFieldObj->setValueSeederName( $formField['seeder'] );
			}

			$newFormFieldObj->setDisplayLabel( $formField['displayLabel'] );
			$newFormFieldObj->setDisplayLabelToken( $formField['displayLabelToken'] );
			$newFormFieldObj->setErrorMessage( $formField['errorMessage'] );
			$newFormFieldObj->setErrorMessageToken( $formField['errorMessageToken'] );
			$newFormFieldObj->setErrorHandler( $formField['errorHandler'] );
			$newFormFieldObj->setPrependHTML( $formField['prependHTML'] );
			$newFormFieldObj->setAppendHTML( $formField['appendHTML'] );
			$newFormFieldObj->setLabelPrependHTML( $formField['labelPrependHTML'] );
			$newFormFieldObj->setLabelAppendHTML( $formField['labelAppendHTML'] );
			$newFormFieldObj->setInputPrependHTML( $formField['inputPrependHTML'] );
			$newFormFieldObj->setInputAppendHTML( $formField['inputAppendHTML'] );
			$newFormFieldObj->setCSSClasses( $formField['cssClasses'] );

			if ( $formField['type'] === 'container' ) {
				foreach( $formField['children'] as $containerChild ) {
					$newChildFormFieldObj = null;

					if ( isset($containerChild['secure']) && $containerChild['secure'] ) {
						$newChildFormFieldObj = new SecureFormField( $containerChild['id'] );
					} else if ( isset($containerChild['validated']) && $containerChild['validated'] ) {
						$newChildFormFieldObj = new ValidatedFormField( $containerChild['id'] );
					} else {
						$newChildFormFieldObj = new FormField( $containerChild['id'] );
					}

					$newChildFormFieldObj->setFormFieldType( $containerChild['type'] );
					$newChildFormFieldObj->setIsRequired( $containerChild['required'] );

					if ( isset($containerChild['value']) ) {
						$newChildFormFieldObj->setDefaultValue( $containerChild['value'] );
						$newChildFormFieldObj->setValue( $containerChild['value'] );
					}

					if ( isset($containerChild['seeder']) ) {
						$newChildFormFieldObj->setValueSeederName( $containerChild['seeder'] );
					}

					$newChildFormFieldObj->setDisplayLabel( $containerChild['displayLabel'] );
					$newChildFormFieldObj->setDisplayLabelToken( $containerChild['displayLabelToken'] );
					$newChildFormFieldObj->setErrorMessage( $containerChild['errorMessage'] );
					$newChildFormFieldObj->setErrorMessageToken( $containerChild['errorMessageToken'] );
					$newChildFormFieldObj->setErrorHandler( $containerChild['errorHandler'] );
					$newChildFormFieldObj->setPrependHTML( $containerChild['prependHTML'] );
					$newChildFormFieldObj->setAppendHTML( $containerChild['appendHTML'] );
					$newChildFormFieldObj->setLabelPrependHTML( $containerChild['labelPrependHTML'] );
					$newChildFormFieldObj->setLabelAppendHTML( $containerChild['labelAppendHTML'] );
					$newChildFormFieldObj->setInputPrependHTML( $containerChild['inputPrependHTML'] );
					$newChildFormFieldObj->setInputAppendHTML( $containerChild['inputAppendHTML'] );
					$newChildFormFieldObj->setCSSClasses( $containerChild['cssClasses'] );

					$newFormFieldObj->addFormField( $containerChild['id'], $newChildFormFieldObj );
				}
			}

			$newFormObj->addFormField( $formField['id'], $newFormFieldObj );
		}

		// CRUD
		$crudInfo = $formNode['CRUD'];

		if ( !empty($crudInfo['create']) || !empty($crudInfo['read']) || !empty($crudInfo['update']) || !empty($crudInfo['destroy']) ) {
			$newFormObj->setIsCRUDable( true );
		}

		$newFormObj->setCRUDCreateTriggers( $crudInfo['create'] );
		$newFormObj->setCRUDReadTriggers( $crudInfo['read'] );
		$newFormObj->setCRUDUpdateTriggers( $crudInfo['update'] );
		$newFormObj->setCRUDDestroyTriggers( $crudInfo['destroy'] );

		$retVal = $newFormObj;

		return $retVal;
	}

	// TODO FINISH THIS -- DO NOT USE CURRENTLY
	public function buildFormAttributeSet( $form_attribute_set_name, $parameters = null ) {
		$retVal = null;

		$formAttributeSetNode = $this->getFormAttributeSetNodeDefinition( $form_attribute_set_name );

		$newFormAttributeSetObj = null;

		if ( isset($formAttributeSetNode['secure']) && $formAttributeSetNode['secure'] ) {
			$newFormAttributeSetObj = new SecureFormAttributeSet( $formAttributeSetNode['formID'] );
		} else if ( isset($formAttributeSetNode['validated']) && $formAttributeSetNode['validated'] ) {
			$newFormAttributeSetObj = new ValidatedFormAttributeSet( $formAttributeSetNode['formID'] );
			$newFormAttributeSetObj->setValidator( $formAttributeSetNode['validator'] );
		} else {
			$newFormAttributeSetObj = new FormAttributeSet( $formAttributeSetNode['formID'] );
		}

		// TODO "valid for" length of time in validation
		// Not just checksum
		$newFormAttributeSetObj->setDataFormatter( $formAttributeSetNode['dataFormatter'] );
		$newFormAttributeSetObj->setDisplayFormatter( $formAttributeSetNode['displayFormatter'] );

		$newFormAttributeSetObj->setDisplayLocalized( $formAttributeSetNode['displayLocalized'] );
		$newFormAttributeSetObj->setDisplayLocalizer( $formAttributeSetNode['displayLocalizer'] );

		$newFormAttributeSetObj->setInputLocalized( $formAttributeSetNode['inputLocalized'] );
		$newFormAttributeSetObj->setInputLocalizer( $formAttributeSetNode['inputLocalizer'] );

		$newFormAttributeSetObj->setAppendHTML( $formAttributeSetNode['appendHTML'] );
		$newFormAttributeSetObj->setPrependHTML( $formAttributeSetNode['prependHTML'] );
		$newFormAttributeSetObj->setCSSClasses( $formAttributeSetNode['cssClasses']);

		$newFormAttributeSetObj->setDAOConnectionName( $formAttributeSetNode['daoConnectionName'] );
		$newFormAttributeSetObj->setDAOFactory( $formAttributeSetNode['daoFactory'] );
		$newFormAttributeSetObj->setDAO( $formAttributeSetNode['DAO'] );
		$newFormAttributeSetObj->setDTO( $formAttributeSetNode['DTO'] );

		$newFormAttributeSetObj->setEncoding( $formAttributeSetNode['encoding'] );

		foreach( $formAttributeSetNode['formFieldSets'] as $formFieldSet ) {
			$newFormFieldSetObj = null;

			if ( isset($formFieldSet['secure']) && $formFieldSet['secure'] ) {
				$newFormFieldSetObj = new SecureFormFieldSet( $formFieldSet['id'] );
			} else if ( isset($formFieldSet['validated']) && $formFieldSet['validated'] ) {
				$newFormFieldSetObj = new ValidatedFormFieldSet( $formFieldSet['id'] );

				// If this FormFieldSet has its own validator, attach it.  The Form validator
				// gets invoked regardless
				if ( isset($formFieldSet['validator']) ) {
					$newFormFieldSetObj->setValidator( $formFieldSet['validator'] );
				}
			} else {
				$newFormFieldSetObj = new FormFieldSet( $formFieldSet['id'] );
			}

			if ( isset($formFieldSet['required']) ) {
				$newFormFieldSetObj->setIsRequired( $formFieldSet['required'] );
			}

			if ( isset($formFieldSet['legend']) ) {
				$newFormFieldSetObj->setLegend( $formFieldSet['legend'] );
			}

			if ( isset($formFieldSet['legendToken']) ) {
				$newFormFieldSetObj->setLegendToken( $formFieldSet['legendToken'] );
			}

			if ( isset($formFieldSet['errorMessage']) ) {
				$newFormFieldSetObj->setErrorMessage( $formFieldSet['errorMessage'] );
			}

			if ( isset($formFieldSet['errorMessageToken']) ) {
				$newFormFieldSetObj->setErrorMessageToken( $formFieldSet['errorMessageToken'] );
			}

			if ( isset($formFieldSet['errorHandler']) ) {
				$newFormFieldSetObj->setErrorHandler( $formFieldSet['errorHandler'] );
			}

			foreach( $formFieldSet['formFields'] as $formField ) {
				$newFormFieldObj = null;

				if ( isset($formField['secure']) && $formField['secure'] ) {
					$newFormFieldObj = new SecureFormField( $formField['id'] );
				} else if ( isset($formField['validated']) && $formField['validated'] ) {
					$newFormFieldObj = new ValidatedFormField( $formField['id'] );
				} else {
					$newFormFieldObj = new FormField( $formField['id'] );
				}

				$newFormFieldObj->setFormFieldType( $formField['type'] );
				$newFormFieldObj->setIsRequired( $formField['required'] );

				if ( isset($formField['value']) ) {
					$newFormFieldObj->setDefaultValue( $formField['value'] );
					$newFormFieldObj->setValue( $formField['value'] );
				}

				$newFormFieldObj->setDisplayLabel( $formField['displayLabel'] );
				$newFormFieldObj->setDisplayLabelToken( $formField['displayLabelToken'] );
				$newFormFieldObj->setErrorMessage( $formField['errorMessage'] );
				$newFormFieldObj->setErrorMessageToken( $formField['errorMessageToken'] );
				$newFormFieldObj->setErrorHandler( $formField['errorHandler'] );
				$newFormFieldObj->setPrependHTML( $formField['prependHTML'] );
				$newFormFieldObj->setAppendHTML( $formField['appendHTML'] );
				$newFormFieldObj->setCSSClasses( $formField['cssClasses'] );

				if ( $formField['type'] === 'container' ) {
					foreach( $formField['children'] as $containerChild ) {
						$newChildFormFieldObj = null;

						if ( isset($containerChild['secure']) && $containerChild['secure'] ) {
							$newChildFormFieldObj = new SecureFormField( $containerChild['id'] );
						} else if ( isset($containerChild['validated']) && $containerChild['validated'] ) {
							$newChildFormFieldObj = new ValidatedFormField( $containerChild['id'] );
						} else {
							$newChildFormFieldObj = new FormField( $containerChild['id'] );
						}

						$newChildFormFieldObj->setFormFieldType( $containerChild['type'] );
						$newChildFormFieldObj->setIsRequired( $containerChild['required'] );

						if ( isset($containerChild['value']) ) {
							$newChildFormFieldObj->setDefaultValue( $containerChild['value'] );
							$newChildFormFieldObj->setValue( $containerChild['value'] );
						}

						$newChildFormFieldObj->setDisplayLabel( $containerChild['displayLabel'] );
						$newChildFormFieldObj->setDisplayLabelToken( $containerChild['displayLabelToken'] );
						$newChildFormFieldObj->setErrorMessage( $containerChild['errorMessage'] );
						$newChildFormFieldObj->setErrorMessageToken( $containerChild['errorMessageToken'] );
						$newChildFormFieldObj->setErrorHandler( $containerChild['errorHandler'] );
						$newChildFormFieldObj->setPrependHTML( $containerChild['prependHTML'] );
						$newChildFormFieldObj->setAppendHTML( $containerChild['appendHTML'] );
						$newChildFormFieldObj->setCSSClasses( $containerChild['cssClasses'] );

						$newFormFieldObj->addFormField( $containerChild['id'], $newChildFormFieldObj );
					}
				}

				$newFormFieldSetObj->addFormField( $formField['id'], $newFormFieldObj );
			}

			$newFormAttributeSetObj->addFormFieldSet( $formFieldSet['id'], $newFormFieldSetObj );
		}

		foreach( $formAttributeSetNode['formFields'] as $formField ) {
			$newFormFieldObj = null;

			if ( isset($formField['secure']) && $formField['secure'] ) {
				$newFormFieldObj = new SecureFormField( $formField['id'] );
			} else if ( isset($formField['validated']) && $formField['validated'] ) {
				$newFormFieldObj = new ValidatedFormField( $formField['id'] );
			} else {
				$newFormFieldObj = new FormField( $formField['id'] );
			}

			$newFormFieldObj = null;

			if ( isset($formField['secure']) && $formField['secure'] ) {
				$newFormFieldObj = new SecureFormField( $formField['id'] );
			} else if ( isset($formField['validated']) && $formField['validated'] ) {
				$newFormFieldObj = new ValidatedFormField( $formField['id'] );
			} else {
				$newFormFieldObj = new FormField( $formField['id'] );
			}

			$newFormFieldObj->setFormFieldType( $formField['type'] );
			$newFormFieldObj->setIsRequired( $formField['required'] );

			if ( isset($formField['value']) ) {
				$newFormFieldObj->setDefaultValue( $formField['value'] );
				$newFormFieldObj->setValue( $formField['value'] );
			}

			$newFormFieldObj->setDisplayLabel( $formField['displayLabel'] );
			$newFormFieldObj->setDisplayLabelToken( $formField['displayLabelToken'] );
			$newFormFieldObj->setErrorMessage( $formField['errorMessage'] );
			$newFormFieldObj->setErrorMessageToken( $formField['errorMessageToken'] );
			$newFormFieldObj->setErrorHandler( $formField['errorHandler'] );
			$newFormFieldObj->setPrependHTML( $formField['prependHTML'] );
			$newFormFieldObj->setAppendHTML( $formField['appendHTML'] );
			$newFormFieldObj->setCSSClasses( $formField['cssClasses'] );

			if ( $formField['type'] === 'container' ) {
				foreach( $formField['children'] as $containerChild ) {
					$newChildFormFieldObj = null;

					if ( isset($containerChild['secure']) && $containerChild['secure'] ) {
						$newChildFormFieldObj = new SecureFormField( $containerChild['id'] );
					} else if ( isset($containerChild['validated']) && $containerChild['validated'] ) {
						$newChildFormFieldObj = new ValidatedFormField( $containerChild['id'] );
					} else {
						$newChildFormFieldObj = new FormField( $containerChild['id'] );
					}

					$newChildFormFieldObj->setFormFieldType( $containerChild['type'] );
					$newChildFormFieldObj->setIsRequired( $containerChild['required'] );

					if ( isset($containerChild['value']) ) {
						$newChildFormFieldObj->setDefaultValue( $containerChild['value'] );
						$newChildFormFieldObj->setValue( $containerChild['value'] );
					}

					$newChildFormFieldObj->setDisplayLabel( $containerChild['displayLabel'] );
					$newChildFormFieldObj->setDisplayLabelToken( $containerChild['displayLabelToken'] );
					$newChildFormFieldObj->setErrorMessage( $containerChild['errorMessage'] );
					$newChildFormFieldObj->setErrorMessageToken( $containerChild['errorMessageToken'] );
					$newChildFormFieldObj->setErrorHandler( $containerChild['errorHandler'] );
					$newChildFormFieldObj->setPrependHTML( $containerChild['prependHTML'] );
					$newChildFormFieldObj->setAppendHTML( $containerChild['appendHTML'] );
					$newChildFormFieldObj->setCSSClasses( $containerChild['cssClasses'] );

					$newFormFieldObj->addFormField( $containerChild['id'], $newChildFormFieldObj );
				}
			}

			$newFormAttributeSetObj->addFormField( $formField['id'], $newFormFieldObj );
		}

		$retVal = $newFormAttributeSetObj;

		return $retVal;
	}

	public function buildSubmittedForm( $form_name, $form_array ) {
		$retVal = null;

		$retVal = $this->buildForm( $form_name );

		foreach( $retVal->getFormFieldSets() as $formFieldSet ) {
			if ( isset($form_array['formFieldSets'][$formFieldSet->getID()]) ) {
				$formFieldSetArray = $form_array['formFieldSets'][$formFieldSet->getID()];
			} else {
				continue;
			}

			foreach( $formFieldSet->getFormFields() as $formField ) {
				$formFieldValue = isset($formFieldSetArray['formFields'][$formField->getID()]) ?
					$formFieldSetArray['formFields'][$formField->getID()] : null;

				if ( !is_array($formFieldValue) && $formField->getFormFieldType() !== 'file' ) {
					$formField->setValue($formFieldValue);
				} else if ( !is_array($formFieldValue) && $formField->getFormFieldType() === 'file' ) {
					$formField->setValue( new eGlooHTTPFile( $retVal->getFormID() . ' formFieldSets ' . $formFieldSet->getID() . ' formFields ' . $formField->getID() ) );
				} else if ( !isset( $formFieldValue['formFields'] ) ) {
					// TODO ... this
					// Not a container field, so process (probably a select)
				} else {
					foreach( $formField->getFormFields() as $childFormField ) {
						$childFormFieldValue = isset($formFieldValue['formFields'][$childFormField->getID()]) ?
							$formFieldValue['formFields'][$childFormField->getID()] : null;

						// TODO support file inputs here
						$childFormField->setValue($childFormFieldValue);
					}
				}
			}
		}

		foreach($retVal->getFormFields() as $formField) {
			$formFieldValue = isset($form_array['formFields'][$formField->getID()]) ?
				$form_array['formFields'][$formField->getID()] : null;

			if ( !is_array($formFieldValue) && $formField->getFormFieldType() !== 'file' ) {
				$formField->setValue($formFieldValue);
			} else if ( !is_array($formFieldValue) && $formField->getFormFieldType() === 'file' ) {
				$formField->setValue( new eGlooHTTPFile( $retVal->getFormID() . ' formFields ' . $formField->getID() ) );
			} else if ( !isset( $formFieldValue['formFields'] ) ) {
				// TODO ... this
				// Not a container field, so process (probably a select)
			} else {
				foreach( $formField->getFormFields() as $childFormField ) {
					$childFormFieldValue = isset($formFieldValue['formFields'][$childFormField->getID()]) ?
						$formFieldValue['formFields'][$childFormField->getID()] : null;

					// TODO support file inputs here
					$childFormField->setValue($childFormFieldValue);
				}
			}
		}

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

		$formNode = $retVal;

		return $retVal;
	}

	/**
	 * Validate and process a form passed in as an argument
	 * 
	 * @return Fully built and validated form object or false on error
	 */
	public function processSubmittedForm( $formObj ) {
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
			$this->loadFormDefinitions();
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

	private function getFormAttributeSetNodeDefinition( $form_attribute_set_name ) {
		$retVal = null;

		$dispatchCacheRegionHandler = CacheManagementDirector::getCacheRegionHandler('Dispatches');
		$nodeCacheID = eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'FormDirectorAttributeSetNodes';

		if ( ($this->_formAttributeSetNodes = $dispatchCacheRegionHandler->getObject( $nodeCacheID, 'Dispatching', true ) ) == null ) {
			eGlooLogger::writeLog( eGlooLogger::DEBUG, "FormDirector: FormAttributeSet Definition Nodes pulled from cache" );
			$this->loadFormDefinitions();
			$dispatchCacheRegionHandler->storeObject( $nodeCacheID, $this->_formAttributeSetNodes, 'Dispatching', 0, true );
		} else {
			eGlooLogger::writeLog( eGlooLogger::DEBUG, "FormDirector: FormAttributeSet Definition Nodes pulled from cache" );
		}

		if (isset($this->_formAttributeSetNodes[$form_attribute_set_name])) {
			$retVal = $this->_formAttributeSetNodes[$form_attribute_set_name];
		} else {
			throw new FormDirectorException( 'Unknown FormAttributeSet Definition requested: \'' . $form_attribute_set_name . '\'' );
		}

		return $retVal;
	}

}
