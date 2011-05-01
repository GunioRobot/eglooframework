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
 * @package $package
 * @subpackage $subpackage
 * @version 1.0
 */

/**
 * eGlooCLI
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class eGlooCLI {

	public static $verbose = false;

	public static function execute( $command, $arguments = null ) {
		switch( $command ) {
			case 'app' :
				self::executeApplication( $arguments );
				break;
			case 'bundle' :
				self::executeBundle( $arguments );
				break;
			case 'data' :
			case 'dp' :
				self::executeDataProcessing( $arguments );
				break;
			case 'form' :
				self::executeForm( $arguments );
				break;
			case 'help' :
				self::printCommandHelp( $arguments );
				break;
			case 'install' :
				self::executeInstall( $arguments );
				break;
			case 'request' :
				self::executeRequest( $arguments );
				break;
			case 'uninstall' :
				self::executeUninstall( $arguments );
				break;
			case 'upgrade' :
				self::executeUpgrade( $arguments );
				break;
			case 'version' :
				self::printVersionInfo( $arguments );
				break;
			default :
				echo 'Unknown command "' . $command . '" invoked.  Please try "egloo help" for more information.' ."\n";
				break;
		}
	}

	public static function executeApplication( $arguments ) {
		echo 'Executing application functions' . "\n";
	}

	public static function executeBundle( $arguments ) {
		echo 'Executing bundle functions' . "\n";
	}

	public static function executeDataProcessing( $arguments ) {
		echo 'Executing data processing functions' . "\n";
	}

	public static function executeForm( $arguments ) {
		echo 'Executing form functions' . "\n";
	}

	public static function executeInstall( $arguments ) {
		echo 'Executing install functions' . "\n";
	}

	public static function executeRequest( $arguments ) {
		echo 'Executing request functions' . "\n";
	}

	public static function executeUninstall( $arguments ) {
		echo 'Executing uninstall functions' . "\n";
	}

	public static function executeUpgrade( $arguments ) {
		echo 'Executing upgrade functions' . "\n";
	}

	public static function printCommandHelp( $arguments = null ) {
		if ( !empty($arguments) && isset($arguments[0]) ) {
			$command = array_shift($arguments);
		} else {
			$command = null;
		}

		// egloo help app, etc.
		switch( $command ) {
			case 'app' :
				self::printHelpInfoForApplicationCommand();
				break;
			case 'bundle' :
				self::printHelpInfoForBundleCommand();
				break;
			case 'data' :
			case 'dp' :
				self::printHelpInfoForDataProcessingCommand();
				break;
			case 'form' :
				self::printHelpInfoForFormCommand();
				break;
			case 'install' :
				self::printHelpInfoForInstallCommand();
				break;
			case 'request' :
				self::printHelpInfoForRequestCommand();
				break;
			case 'uninstall' :
				self::printHelpInfoForUninstallCommand();
				break;
			case 'upgrade' :
				self::printHelpInfoForUpgradeCommand();
				break;
			case null :
				self::printHelpInfo();
				break;
			default :
				echo 'No help found for command "' . $command . '"' . "\n";
				break;
		}
	}

	public static function printHelpInfo() {
		echo 'eGloo Help: Work in Progress' ."\n\n";

		echo 'Common Commands:' . "\n\n";

		echo 'See "egloo help <command>" for more information on a specific command' . "\n";
	}

	public static function printHelpInfoForApplicationCommand() {
		echo 'eGloo Application Help' ."\n";
	}

	public static function printHelpInfoForBundleCommand() {
		echo 'eGloo Bundle Help' ."\n";
	}

	public static function printHelpInfoForDataProcessingCommand() {
		echo 'eGloo Data Processing Help' ."\n";
	}

	public static function printHelpInfoForFormCommand() {
		echo 'eGloo Form Help' ."\n";
	}

	public static function printHelpInfoForInstallCommand() {
		echo 'eGloo Install Help' ."\n";
	}

	public static function printHelpInfoForRequestCommand() {
		echo 'eGloo Request Help' ."\n";
	}

	public static function printHelpInfoForUninstallCommand() {
		echo 'eGloo Uninstall Help' ."\n";
	}

	public static function printHelpInfoForUpgradeCommand() {
		echo 'eGloo Upgrade Help' ."\n";
	}

	public static function printUsageInfo() {
		echo 'eGloo Usage Info' ."\n";
	}

	public static function printVersionInfo() {
		// TODO grab this from somewhere more sensible
		echo 'Version 1.0 Developer Preview 2' ."\n";
	}

}

