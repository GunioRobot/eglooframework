<?php
/**
 * eGlooHelp Class File
 *
 * Contains the class definition for the eGlooHelp
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
 * @category $category
 * @package $package
 * @subpackage $subpackage
 * @version 1.0
 */

/**
 * eGlooHelp
 *
 * $short_description
 *
 * $long_description
 *
 * @category $category
 * @package $package
 * @subpackage $subpackage
 */
class eGlooHelp extends eGlooCombine {

	/**
	 * @var array List of supported commands and their options/required arguments
	 */
	protected static $_supported_commands = array(
		'_empty' => array(),
		'_zero_argument' => array(),
		'all' => array(),
	);

	public function execute() {
		$retVal = null;

		switch( $this->_command ) {
			case '_empty' :
				$retVal = $this->printCommandInfo();
				break;
			case '_zero_argument' :
				$retVal = $this->printHelpInfo();
				break;
			case 'all' :
				$retVal = $this->_list();
				break;
			default :
				break;
		}

		return $retVal;
	}

	protected function printCommandInfo() {
		$retVal = false;

		if ( isset( $this->_command_arguments[0]) ) {
			$info_subject = $this->_command_arguments[0];

			$combine_class = eGlooConfiguration::getCLICombineMapping( $info_subject );

			if ( class_exists($combine_class) ) {
				echo $combine_class::getHelpString() . "\n";
			} else {
				echo 'No help found for command "' . $info_subject . '"' . "\n";
			}

			$retVal = true;
		}

		return $retVal;
	}

	protected function printHelpInfo() {
		echo self::getHelpString() . "\n";
	}

	// PHP is dumb - 'list' should be a valid method name
	protected function _list() {
		$retVal = false;

		$combine_list = eGlooConfiguration::getCLICombineList();

		$longest = 0;

		foreach( $combine_list as $combine_id => $combine_class ) {
			if (strlen($combine_id) > $longest) {
				$longest = strlen($combine_id);
			}
		}

		foreach( $combine_list as $combine_id => $combine_class ) {
			if ( class_exists($combine_class) && $combine_class !== get_class($this) && $combine_class !== 'eGlooZalgo' ) {
				$tab_string = '';

				$name_length = $longest - strlen($combine_id);

				if ( ($name_length / 8) < 1 ) {
					$tab_count = 0;
				} else if ( ($name_length / 8) === 1 ) {
					$tab_count = 1;
				} else {
					$tab_count = ceil($name_length / 8);
				}

				for( $i = 0; $i <= $tab_count; $i++ ) {
					$tab_string .= "\t";
				}

				echo $combine_id . ':' . $tab_string . $combine_class::getHelpString() . "\n";
			}
		}

		$retVal = true;

		return $retVal;
	}

	public function commandRequirementsSatisfied() {
		$retVal = false;

		switch( $this->_command ) {
			// case 'info' :
			// 	$retVal = $this->infoCommandRequirementsSatisfied();
			// 	break;
			// case 'list' :
			// 	$retVal = $this->listCommandRequirementsSatisfied();
			// 	break;
			default :
				$retVal = true;
				break;
		}

		return $retVal;
	}

	/**
	 * Return the help information for this class as a string
	 *
	 * @return string the help information for this class
	 * @author George Cooper
	 **/
	public static function getHelpString() {
		$retVal = 'eGloo Help: Work in Progress' ."\n\n";
		$retVal .= 'Common Commands:' . "\n\n";
		$retVal .= 'See "egloo help <command>" for more information on a specific command';

		return $retVal;
	}

}

