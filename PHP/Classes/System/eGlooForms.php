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
 * @package $package
 * @subpackage $subpackage
 * @version 1.0
 */

/**
 * eGlooForms
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class eGlooForms extends eGlooCombine {


	/**
	 * @var array List of supported commands and their options/required arguments
	 */
	protected static $_supported_commands = array(
		'list' => array(),
	);

	public function execute() {
		$retVal = null;

		switch( $this->_command ) {
			case 'list' :
				$retVal = $this->_list();
				break;
			default :
				break;
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
			echo 'Forms Processing Nodes:' . "\n";

			foreach( $form_nodes as $form_node_id => $form_node ) {
				echo "\t" . $form_node_id . "\n";
			}

			echo "\n";
		}
	}

	public function listFormAttributeSetNodes( $form_attribute_set_nodes ) {
		if ( !empty($form_attribute_set_nodes) ) {
			echo 'Forms Processing Attribute Set Nodes:' . "\n";

			foreach( $form_attribute_set_nodes as $form_attribute_set_node_id => $form_attribute_set_node ) {
				echo "\t" . $form_attribute_set_node_id . "\n";
			}

			echo "\n";
		}
	}

	public function commandRequirementsSatisfied() {
		$retVal = false;

		switch( $this->_command ) {
			case 'list' :
				$retVal = $this->listCommandRequirementsSatisfied();
				break;
			default :
				break;
		}

		return $retVal;
	}

	protected function listCommandRequirementsSatisfied() {
		$retVal = false;

		$retVal = true;

		return $retVal;
	}

}

