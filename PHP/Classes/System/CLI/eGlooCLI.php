<?php
/**
 * eGlooCLI Class File
 *
 * Contains the class definition for the eGlooCLI
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
 * @version 1.0
 */

/**
 * eGlooCLI
 *
 * $short_description
 *
 * $long_description
 *
 * @category System
 * @package CLI
 */
class eGlooCLI {

	public static $verbose = false;

	public static $script_arguments = array();

	public static $class_paths = null;

	// TODO make a default for this so it doesn't freak out
	public static function execute( $command = null, $arguments = null ) {
		$combine_class = eGlooConfiguration::getCLICombineMapping( $command );

		if ( class_exists($combine_class) ) {
			self::executeCombine( $combine_class, $arguments );
		} else {
			echo 'Unknown command "' . $command . '" invoked.  Please try "egloo help" for more information.' ."\n";
		}
	}

	public static function executeCombine( $combine_class, $arguments ) {
		$combineObj = $combine_class::getInstanceFromCLIArgumentArray( $arguments );

		if ( $combineObj !== null && $combineObj->isExecutable() ) {
			$combineObj->execute();
		} else {
			self::printHelpInfoForCombineCommand( $combine_class );
		}
	}

	public static function printHelpInfo() {
		echo 'eGloo Help: Work in Progress' ."\n\n";

		echo 'Common Commands:' . "\n\n";

		echo 'See "egloo help <command>" for more information on a specific command' . "\n";
	}

	public static function printHelpInfoForCombineCommand( $combine_class ) {
		echo $combine_class::getHelpString() . "\n";
	}

	public static function printUsageInfo() {
		// More later?  Change this later?  For now, this works
		self::printHelpInfo();
	}

	public static function printVersionInfo() {
		// TODO grab this from somewhere more sensible
		echo 'Version 1.0 Developer Preview 2' ."\n";
	}

	public static function setScriptArguments( $script_arguments ) {
		if ( is_array($script_arguments) ) {
			self::$script_arguments = $script_arguments;
		}
	}

	public static function getScriptArguments() {
		$retVal = null;

		$retVal = self::$script_arguments;

		return $retVal;
	}

	public static function getClassPaths() {
		$retVal = array();

		if ( self::$class_paths === null ) {
			foreach( self::$script_arguments as $argument_key => $argument_value ) {
				$matches = array();
				preg_match('/^--class-path=([a-zA-Z0-9\/_.~: -]+)$/', $argument_value, $matches);

				if ( !empty($matches) && isset($matches[1]) ) {
					$full_path = realpath( $matches[1] );

					if ( file_exists($full_path) && is_dir($full_path) && is_readable($full_path) ) {
						$retVal[] = $full_path;
					} else {
						echo 'Provided path "' . $full_path . '" is not a valid path.  Ignoring...' . "\n";
					}
				}
			}

			self::$class_paths = $retVal;
		} else {
			$retVal = self::$class_paths;
		}

		return $retVal;
	}

}

