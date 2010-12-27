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

		// Grab the cache handler specifically for this cache region.  We do this so that when we write to the cache for Form definitions
		// we can also write some information to the caching system to better keep track of what is cached for the Form processing system
		// and do more granulated inspection and cache clearing
		$dispatchCacheRegionHandler = CacheManagementDirector::getCacheRegionHandler('Dispatches');

		$formNodes = array();

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

				$formFieldSetLegend = null;
				$formFieldSetLegendLocalizationToken = null;

				foreach( $formFieldSet->xpath( 'child::Legend' ) as $legend ) {
					$formFieldSetLegend = (string) $legend;
					$formFieldSetLegendLocalizationToken = isset($legend['legendToken']) ? (string) $legend['legendToken'] : NULL;
				}

				$newFormFieldSet = array(	'id' => $formFieldSetID,
											// 'displayLocalized' => $formFieldSetNodeDisplayLocalized,
											'validated' => $formFieldSetNodeValidated,
											'secure' => $formFieldSetNodeSecure,
											'validator' => $formFieldSetNodeValidator,
											'legend' => $formFieldSetLegend,
											'legendToken' => $formFieldSetLegendLocalizationToken,
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

							if ( !$formFieldChildType || trim($formFieldChildType) === '' ) {
								throw new ErrorException("No FormField type specified in FormField Child: '" . $formFieldChildID .
									"'.	 Please review your Forms.xml");
							} else if ($formFieldChildType === 'container') {
								throw new ErrorException("eGloo does not currently allow container FormFields to have container children.  Please review your Forms.xml");
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

							foreach( $formFieldChild->xpath( 'child::CSSClasses' ) as $childCSSClassesNode ) {
								$childCSSClasses = (string) $childCSSClassesNode;
							}

							$newChildFormField = array(	'id' => $formFieldChildID,
													'type' => $formFieldChildType,
													// 'displayLocalized' => $formFieldChildNodeDisplayLocalized,
													'displayLabel' => $childDisplayLabel,
													'displayLabelToken' => $childDisplayLabelLocalizationToken,
													'errorMessage' => $childErrorMessage,
													'errorMessageToken' => $childErrorMessageLocalizationToken,
													'errorHandler' => $childErrorHandler,
													'prependHTML' => $childPrependHTML,
													'appendHTML' => $childAppendHTML,
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

					foreach( $formField->xpath( 'child::CSSClasses' ) as $childCSSClassesNode ) {
						$cssClasses = (string) $childCSSClassesNode;
					}

					$newFormField = array(	'id' => $formFieldID,
											'type' => $formFieldType,
											// 'displayLocalized' => $formFieldNodeDisplayLocalized,
											'displayLabel' => $displayLabel,
											'displayLabelToken' => $displayLabelLocalizationToken,
											'errorMessage' => $errorMessage,
											'errorMessageToken' => $errorMessageLocalizationToken,
											'errorHandler' => $errorHandler,
											'prependHTML' => $prependHTML,
											'appendHTML' => $appendHTML,
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
/////////









			foreach( $formNode->xpath( 'child::FormField' ) as $formField ) {
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

						if ( !$formFieldChildType || trim($formFieldChildType) === '' ) {
							throw new ErrorException("No FormField type specified in FormField Child: '" . $formFieldChildID .
								"'.	 Please review your Forms.xml");
						} else if ($formFieldChildType === 'container') {
							throw new ErrorException("eGloo does not currently allow container FormFields to have container children.  Please review your Forms.xml");
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

						foreach( $formFieldChild->xpath( 'child::CSSClasses' ) as $childCSSClassesNode ) {
							$childCSSClasses = (string) $childCSSClassesNode;
						}

						$newChildFormField = array(	'id' => $formFieldChildID,
												'type' => $formFieldChildType,
												// 'displayLocalized' => $formFieldChildNodeDisplayLocalized,
												'displayLabel' => $childDisplayLabel,
												'displayLabelToken' => $childDisplayLabelLocalizationToken,
												'errorMessage' => $childErrorMessage,
												'errorMessageToken' => $childErrorMessageLocalizationToken,
												'errorHandler' => $childErrorHandler,
												'prependHTML' => $childPrependHTML,
												'appendHTML' => $childAppendHTML,
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

				foreach( $formField->xpath( 'child::CSSClasses' ) as $childCSSClassesNode ) {
					$cssClasses = (string) $childCSSClassesNode;
				}

				$newFormField = array(	'id' => $formFieldID,
										'type' => $formFieldType,
										// 'displayLocalized' => $formFieldNodeDisplayLocalized,
										'displayLabel' => $displayLabel,
										'displayLabelToken' => $displayLabelLocalizationToken,
										'errorMessage' => $errorMessage,
										'errorMessageToken' => $errorMessageLocalizationToken,
										'errorHandler' => $errorHandler,
										'prependHTML' => $prependHTML,
										'appendHTML' => $appendHTML,
										'cssClasses' => $cssClasses,
									);

				if (!empty($containerChildren)) {
					$newFormField['children'] = $containerChildren;
				}

				$formNodes[$formNodeID]['formFields'][$formFieldID] = $newFormField;
			}

			$dispatchCacheRegionHandler->storeObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'FormDirectorNodes::' . $formNodeID,
				$formNodes[$formNodeID], 'Dispatching', 0, true );
			;
		}

		$this->_formNodes = $formNodes;

		// TODO implement FormAttributeSets for hot

		$dispatchCacheRegionHandler->storeObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'FormDirectorNodes',
			$this->_formNodes, 'Dispatching', 0, true );
	}

	private function loadFormFieldNodeDefinition() {
		
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
			$newFormObj = new SecureForm( $formNode['formID'] );
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

		$newFormObj->setFormDAO( $formNode['DAO'] );
		$newFormObj->setFormDTO( $formNode['DTO'] );

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

			if ( isset($formFieldSet['legend']) ) {
				$newFormFieldSetObj->setLegend( $formFieldSet['legend'] );
			}

			if ( isset($formFieldSet['legendToken']) ) {
				$newFormFieldSetObj->setLegendToken( $formFieldSet['legendToken'] );
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

			$newFormObj->addFormField( $formField['id'], $newFormFieldObj );
		}

		$retVal = $newFormObj;

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

		$formNode = $retVal;

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
