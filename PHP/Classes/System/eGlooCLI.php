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
			case 'check' :
				self::executeCheck( $arguments );
				break;
			case 'config' :
			case 'configuration' :
				self::executeConfiguration( $arguments );
				break;
			case 'cube' :
				self::executeCube( $arguments );
				break;
			case 'data' :
			case 'dp' :
				self::executeDataProcessing( $arguments );
				break;
			case 'form' :
				self::executeForm( $arguments );
				break;
			case 'framework' :
				self::executeFramework( $arguments );
				break;
			case 'global' :
				self::executeGlobal( $arguments );
				break;
			case 'help' :
				self::printCommandHelp( $arguments );
				break;
			case 'info' :
				self::executeInfo( $arguments );
				break;
			case 'install' :
				self::executeInstall( $arguments );
				break;
			case 'list' :
			case 'ls' :
				self::executeList( $arguments );
				break;
			case 'request' :
				self::executeRequest( $arguments );
				break;
			case 'run' :
				self::executeRun( $arguments );
				break;
			case 'search' :
				self::executeSearch( $arguments );
				break;
			case 'sim' :
			case 'simulate' :
				self::executeSimulate( $arguments );
				break;
			case 'status' :
				self::executeStatus( $arguments );
				break;
			case 'system' :
				self::executeSystem( $arguments );
				break;
			case 'test' :
				self::executeTest( $arguments );
				break;
			case 'ui' :
				self::executeUI( $arguments );
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

	public static function executeCheck( $arguments ) {
		echo 'Executing check functions' . "\n";
	}

	public static function executeConfiguration( $arguments ) {
		echo 'Executing configuration functions' . "\n";
	}

	public static function executeCube( $arguments ) {
		echo 'Executing cube functions' . "\n";
	}

	public static function executeForm( $arguments ) {
		echo 'Executing form functions' . "\n";
	}

	public static function executeFramework( $arguments ) {
		echo 'Executing framework functions' . "\n";
	}

	public static function executeGlobal( $arguments ) {
		echo 'Executing global functions' . "\n";
	}

	public static function executeInfo( $arguments ) {
		echo 'Executing info functions' . "\n";
	}

	public static function executeInstall( $arguments ) {
		echo 'Executing install functions' . "\n";
	}

	public static function executeList( $arguments ) {
		echo 'Executing list functions' . "\n";
	}

	public static function executeRequest( $arguments ) {
		echo 'Executing request functions' . "\n";
	}

	public static function executeRun( $arguments ) {
		echo 'Executing run functions' . "\n";
	}

	public static function executeSearch( $arguments ) {
		echo 'Executing search functions' . "\n";
	}

	public static function executeSimulate( $arguments ) {
		echo 'Executing simulate functions' . "\n";
	}

	public static function executeStatus( $arguments ) {
		echo 'Executing status functions' . "\n";
	}

	public static function executeSystem( $arguments ) {
		echo 'Executing system functions' . "\n";
	}

	public static function executeTest( $arguments ) {
		echo 'Executing test functions' . "\n";
	}

	public static function executeUI( $arguments ) {
		echo 'Executing UI functions' . "\n";
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
			case 'check' :
				self::printHelpInfoForCheckCommand();
				break;
			case 'config' :
			case 'configuration' :
				self::printHelpInfoForConfigurationCommand();
				break;
			case 'cube' :
				self::printHelpInfoForCubeCommand();
				break;
			case 'data' :
			case 'dp' :
				self::printHelpInfoForDataProcessingCommand();
				break;
			case 'form' :
				self::printHelpInfoForFormCommand();
				break;
			case 'framework' :
				self::printHelpInfoForFrameworkCommand();
				break;
			case 'global' :
				self::printHelpInfoForGlobalCommand();
				break;
			case 'info' :
				self::printHelpInfoForInfoCommand();
				break;
			case 'install' :
				self::printHelpInfoForInstallCommand();
				break;
			case 'list' :
			case 'ls' :
				self::printHelpInfoForListCommand();
				break;
			case 'request' :
				self::printHelpInfoForRequestCommand();
				break;
			case 'run' :
				self::printHelpInfoForRunCommand();
				break;
			case 'search' :
				self::printHelpInfoForSearchCommand();
				break;
			case 'sim' :
			case 'simulate' :
				self::printHelpInfoForSimulateCommand();
				break;
			case 'status' :
				self::printHelpInfoForStatusCommand();
				break;
			case 'system' :
				self::printHelpInfoForSystemCommand();
				break;
			case 'test' :
				self::printHelpInfoForTestCommand();
				break;
			case 'ui' :
				self::printHelpInfoForUICommand();
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

	public static function printHelpInfoForCheckCommand() {
		echo 'eGloo Check Help' ."\n";
	}

	public static function printHelpInfoForConfigurationCommand() {
		echo 'eGloo Configuration Help' ."\n";
	}

	public static function printHelpInfoForCubeCommand() {
		echo 'eGloo Cube Help' ."\n";
	}

	public static function printHelpInfoForDataProcessingCommand() {
		echo 'eGloo Data Processing Help' ."\n";
	}

	public static function printHelpInfoForFormCommand() {
		echo 'eGloo Form Help' ."\n";
	}

	public static function printHelpInfoForFrameworkCommand() {
		echo 'eGloo Framework Help' ."\n";
	}

	public static function printHelpInfoForGlobalCommand() {
		echo 'eGloo Global Help' ."\n";
	}

	public static function printHelpInfoForInfoCommand() {
		echo 'eGloo Info Help' ."\n";
	}

	public static function printHelpInfoForInstallCommand() {
		echo 'eGloo Install Help' ."\n";
	}

	public static function printHelpInfoForListCommand() {
		echo 'eGloo List Help' ."\n";
	}

	public static function printHelpInfoForRequestCommand() {
		echo 'eGloo Request Help' ."\n";
	}

	public static function printHelpInfoForRunCommand() {
		echo 'eGloo Run Help' ."\n";
	}

	public static function printHelpInfoForSearchCommand() {
		echo 'eGloo Search Help' ."\n";
	}

	public static function printHelpInfoForSimulateCommand() {
		echo 'eGloo Simulate Help' ."\n";
	}

	public static function printHelpInfoForStatusCommand() {
		echo 'eGloo Status Help' ."\n";
	}

	public static function printHelpInfoForSystemCommand() {
		echo 'eGloo System Help' ."\n";
	}

	public static function printHelpInfoForTestCommand() {
		echo 'eGloo Test Help' ."\n";
	}

	public static function printHelpInfoForUICommand() {
		echo 'eGloo UI Help' ."\n";
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

