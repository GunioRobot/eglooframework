<?php
/**
 * eGlooForms Class File
 *
 * Contains the class definition for the eGlooForms
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
 * @category System
 * @package CLI
 * @subpackage Combines
 * @version 1.0
 */

/**
 * eGlooForms
 *
 * $short_description
 *
 * $long_description
 *
 * @category System
 * @package CLI
 * @subpackage Combines
 */
class eGlooForms extends eGlooCombine {

	/**
	 * @var array List of supported commands and their options/required arguments
	 */
	protected static $_supported_commands = array(
		'info' => array(),
		'list' => array(),
	);

	public function execute() {
		$retVal = null;

		switch( $this->_command ) {
			case 'info' :
				$retVal = $this->info();
				break;
			case 'list' :
				$retVal = $this->_list();
				break;
			default :
				break;
		}

		return $retVal;
	}

	protected function info() {
		$retVal = false;

		if ( isset( $this->_command_arguments[0]) ) {
			$info_subject = $this->_command_arguments[0];

			$formDirector = FormDirector::getInstance();

			$formDefinitions = null;

			try {
				$formDefinitions = $formDirector->getParsedDefinitionsArrayFromXML();
			} catch ( FormDirectorException $e ) {
				// TODO better error handling.  For now this probably means the Forms.xml
				// file was not found locally.  Just print message and move on, since this is
				// just a listing command.
				echo $e->getMessage() . "\n";
			}

			if ( $formDefinitions !== null ) {
				$results = 0;
				$form_node_info = null;
				$form_attribute_set_info = null;

				if ( isset($formDefinitions['formNodes'][$info_subject]) ) {
					$form_node_info = $formDefinitions['formNodes'][$info_subject];
					$results++;
				}

				if ( isset($formDefinitions['formAttributeSetNodes'][$info_subject]) ) {
					$form_attribute_set_info = $formDefinitions['formAttributeSetNodes'][$info_subject];
					$results++;
				}

				$result_summary = 'Search for "' . $info_subject . '" has ' . $results;
				$result_summary .= $results === 1 ? ' result.' : ' results.';
				$result_summary .= "\n\n";

				echo $result_summary;

				if ( !empty($form_node_info) ) {
					echo 'Form: ' . $form_node_info['formID'] . "\n";

					echo 'FormFields: ' . count($form_node_info['formFields']) . "\n";

					foreach($form_node_info['formFields'] as $form_field_name => $form_field) {
						$output_string = "\t" . ($form_field['required'] === true ? '(R) ' : '');
						$output_string .= $form_field_name;

						$name_length = strlen($output_string);
						$tab_count = ($name_length / 8) > 1 ? ($name_length / 8) : 2;

						for( $i = 0; $i <= $tab_count; $i++ ) {
							$output_string .= "\t";
						}

						$output_string .= 'T=' . $form_field['type'];
						
						if ( $form_field['value'] !== null ) {
							$output_string .= ' V="' . $form_field['value'] . '"';
						}

						$output_string .= "\n";

						echo $output_string;
					}

					echo 'FormFieldSets: ' . count($form_node_info['formFieldSets']) . "\n";

					foreach($form_node_info['formFieldSets'] as $form_field_set_name => $form_field_set) {
						$output_string = "\t" . ($form_field_set['required'] === true ? '(R) ' : '');
						$output_string .= $form_field_set_name;

						$name_length = strlen($output_string);
						$tab_count = ($name_length / 8) > 1 ? ($name_length / 8) : 2;

						for( $i = 0; $i <= $tab_count; $i++ ) {
							$output_string .= "\t";
						}

						$output_string .= 'FC=' . count($form_field_set['formFields']);

						$output_string .= "\n";

						echo $output_string;
					}
				}

				if ( !empty($form_attribute_set_info) ) {
					echo 'Form Attribute Set: ' . $form_attribute_set_info['statementClass'] . "\n";
					// echo 'Statements Provided:' . "\n";
					echo 'Statements Provided: ';

					$count = 1;

					foreach($form_attribute_set_info['statements'] as $statement_name => $statement) {
						echo $statement_name;
						if ( $count < count($form_attribute_set_info['statements']) ) {
							echo ', ';
							$count++;
						}
					}

					echo "\n";
				}

				$retVal = true;
			}
		}

		return $retVal;
	}

	// PHP is dumb - 'list' should be a valid method name
	protected function _list() {
		$retVal = false;

		$formDirector = FormDirector::getInstance();

		$formDefinitions = null;

		try {
			$formDefinitions = $formDirector->getParsedDefinitionsArrayFromXML();
		} catch ( FormDirectorException $e ) {
			// TODO better error handling.  For now this probably means the Forms.xml
			// file was not found locally.  Just print message and move on, since this is
			// just a listing command.
			echo $e->getMessage() . "\n";
		}

		if ( $formDefinitions !== null ) {
			// TODO actually branch on arguments
			$this->listFormsAll( $formDefinitions );
			$retVal = true;
		}

		return $retVal;
	}

	public function listFormsAll( $formDefinitions ) {
		// For now, just this Forms.xml, don't include the framework proper or common
		$formNodes = $formDefinitions['formNodes'];
		$formAttributeSetNodes = $formDefinitions['formAttributeSetNodes'];

		$this->listFormNodes( $formNodes );
		$this->listFormAttributeSetNodes( $formAttributeSetNodes );
	}

	public function listFormNodes( $form_nodes ) {
		if ( !empty($form_nodes) ) {
			echo 'Forms Processing Nodes: ' . count($form_nodes) . "\n";

			foreach( $form_nodes as $form_node_id => $form_node ) {
				echo "\t" . $form_node_id . "\n";
			}

			echo "\n";
		}
	}

	public function listFormAttributeSetNodes( $form_attribute_set_nodes ) {
		if ( !empty($form_attribute_set_nodes) ) {
			echo 'Forms Processing Attribute Set Nodes: ' . count($form_attribute_set_nodes) . "\n";

			foreach( $form_attribute_set_nodes as $form_attribute_set_node_id => $form_attribute_set_node ) {
				echo "\t" . $form_attribute_set_node_id . "\n";
			}

			echo "\n";
		}
	}

	public function commandRequirementsSatisfied() {
		$retVal = false;

		switch( $this->_command ) {
			case 'info' :
				$retVal = $this->infoCommandRequirementsSatisfied();
				break;
			case 'list' :
				$retVal = $this->listCommandRequirementsSatisfied();
				break;
			default :
				break;
		}

		return $retVal;
	}

	protected function infoCommandRequirementsSatisfied() {
		$retVal = false;

		$retVal = true;

		return $retVal;
	}

	protected function listCommandRequirementsSatisfied() {
		$retVal = false;

		$retVal = true;

		return $retVal;
	}

	/**
	 * Return the help information for this class as a string
	 *
	 * @return string the help information for this class
	 * @author George Cooper
	 **/
	public static function getHelpString() {
		return 'eGloo Forms Help';
	}

}

