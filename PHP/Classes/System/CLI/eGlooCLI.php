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
		switch( $command ) {
			case 'app' :
			case 'application' :
				self::executeApplication( $arguments );
				break;
			case 'bundle' :
				self::executeBundle( $arguments );
				break;
			case 'cache' :
				self::executeCache( $arguments );
				break;
			case 'check' :
				self::executeCheck( $arguments );
				break;
			case 'config' :
			case 'configuration' :
				self::executeConfiguration( $arguments );
				break;
			case 'cube' :
			case 'cubes' :
				self::executeCubes( $arguments );
				break;
			case 'daemon' :
			case 'daemons' :
			case 'daemonmaster' :
			case 'dm' :
				self::executeDaemonMaster( $arguments );
				break;
			case 'data' :
			case 'dp' :
				self::executeDataProcessing( $arguments );
				break;
			case 'form' :
			case 'forms' :
				self::executeForms( $arguments );
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
			case 'log' :
				self::executeLog( $arguments );
				break;
			case 'net' :
			case 'network' :
				self::executeNetwork( $arguments );
				break;
			case 'req' :
			case 'request' :
			case 'requests' :
				self::executeRequest( $arguments );
				break;
			case 'run' :
			case 'runtime' :
				self::executeRun( $arguments );
				break;
			case 'search' :
				self::executeSearch( $arguments );
				break;
			case 'sim' :
			case 'simulate' :
				self::executeSimulate( $arguments );
				break;
			case 'stat' :
			case 'status' :
				self::executeStatus( $arguments );
				break;
			case 'sys' :
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
			case 'web' :
			case 'webroot' :
				self::executeWebRoot( $arguments );
				break;
			case 'zalgo' :
				self::executeZalgo( $arguments );
				break;
			default :
				echo 'Unknown command "' . $command . '" invoked.  Please try "egloo help" for more information.' ."\n";
				break;
		}
	}

	public static function executeApplication( $arguments ) {
		echo 'Executing application functions' . "\n";
		$applicationObj = eGlooApplication::getInstanceFromCLIArgumentArray( $arguments );
	}

	public static function executeBundle( $arguments ) {
		echo 'Executing bundle functions' . "\n";
		// $application = eGlooApplication::getAppliationFromCLIArgumentArray( $arguments );
		// $bundle = $application->getBundle( $bundle_name );
		$bundleObj = eGlooBundle::getInstanceFromCLIArgumentArray( $arguments );
	}

	public static function executeCache( $arguments ) {
		echo 'Executing cache functions' . "\n";
		$cacheObj = eGlooCache::getInstanceFromCLIArgumentArray( $arguments );
	}

	public static function executeCheck( $arguments ) {
		echo 'Executing check functions' . "\n";
	}

	public static function executeConfiguration( $arguments ) {
		echo 'Executing configuration functions' . "\n";
	}

	public static function executeCubes( $arguments ) {
		echo 'Executing cubes functions' . "\n";
	}

	public static function executeDaemonMaster( $arguments ) {
		$daemonMasterObj = eGlooDaemonMaster::getInstanceFromCLIArgumentArray( $arguments );

		if ( $daemonMasterObj !== null && $daemonMasterObj->isExecutable() ) {
			$daemonMasterObj->execute();
		} else {
			self::printHelpInfoForDaemonMasterCommand();
		}
	}

	public static function executeDataProcessing( $arguments ) {
		$dataProcessingObj = eGlooDataProcessing::getInstanceFromCLIArgumentArray( $arguments );

		if ( $dataProcessingObj !== null && $dataProcessingObj->isExecutable() ) {
			$dataProcessingObj->execute();
		} else {
			self::printHelpInfoForDataProcessingCommand();
		}
	}

	public static function executeForms( $arguments ) {
		$formsObj = eGlooForms::getInstanceFromCLIArgumentArray( $arguments );

		if ( $formsObj !== null && $formsObj->isExecutable() ) {
			$formsObj->execute();
		} else {
			self::printHelpInfoForFormsCommand();
		}
	}

	public static function executeFramework( $arguments ) {
		echo 'Executing framework functions' . "\n";
		$frameworkObj = eGlooFramework::getInstanceFromCLIArgumentArray( $arguments );
	}

	public static function executeGlobal( $arguments ) {
		echo 'Executing global functions' . "\n";
	}

	public static function executeInfo( $arguments ) {
		echo 'Executing info functions' . "\n";
	}

	public static function executeInstall( $arguments ) {
		echo 'Executing install functions' . "\n";
		$installObj = eGlooInstall::getInstanceFromCLIArgumentArray( $arguments );
	}

	public static function executeList( $arguments ) {
		echo 'Executing list functions' . "\n";
	}

	public static function executeLog( $arguments ) {
		echo 'Executing log functions' . "\n";
		$logObj = eGlooLog::getInstanceFromCLIArgumentArray( $arguments );
	}

	public static function executeNetwork( $arguments ) {
		echo 'Executing network functions' . "\n";
		$networkObj = eGlooNetwork::getInstanceFromCLIArgumentArray( $arguments );
		$networkObj->execute();
	}

	public static function executeRequest( $arguments ) {
		$requestObj = eGlooRequestLibrary::getInstanceFromCLIArgumentArray( $arguments );

		if ( $requestObj !== null && $requestObj->isExecutable() ) {
			$requestObj->execute();
		} else {
			self::printHelpInfoForRequestCommand();
		}

		
	}

	public static function executeRun( $arguments ) {
		echo 'Executing run functions' . "\n";
		$runtimeObj = eGlooRuntime::getInstanceFromCLIArgumentArray( $arguments );
	}

	public static function executeSearch( $arguments ) {
		echo 'Executing search functions' . "\n";
	}

	public static function executeSimulate( $arguments ) {
		echo 'Executing simulate functions' . "\n";
		$simulationObj = eGlooSimulation::getInstanceFromCLIArgumentArray( $arguments );
	}

	public static function executeStatus( $arguments ) {
		echo 'Executing status functions' . "\n";
		$statusObj = eGlooStatus::getInstanceFromCLIArgumentArray( $arguments );
	}

	public static function executeSystem( $arguments ) {
		echo 'Executing system functions' . "\n";
		$systemObj = eGlooSystem::getInstanceFromCLIArgumentArray( $arguments );
	}

	public static function executeTest( $arguments ) {
		echo 'Executing test functions' . "\n";
		$testObj = eGlooTest::getInstanceFromCLIArgumentArray( $arguments );
	}

	public static function executeUI( $arguments ) {
		echo 'Executing UI functions' . "\n";
		$uiObj = eGlooUI::getInstanceFromCLIArgumentArray( $arguments );
	}

	public static function executeUninstall( $arguments ) {
		echo 'Executing uninstall functions' . "\n";
		$installObj = eGlooInstall::getInstanceFromCLIArgumentArray( $arguments );

		$uninstallObj = new eGlooUninstall();
		$uninstallObj->setInstall( $installObj );
		$uninstallObj->execute();
	}

	public static function executeUpgrade( $arguments ) {
		echo 'Executing upgrade functions' . "\n";

		$installObj = eGlooInstall::getInstanceFromCLIArgumentArray( $arguments );

		$upgradeObj = new eGlooUpgrade();
		$upgradeObj->setInstall( $installObj );
		$upgradeObj->execute();
	}

	public static function executeWebRoot( $arguments ) {
		echo 'Executing webroot functions' . "\n";
		$webRootObj = eGlooWebRoot::getInstanceFromCLIArgumentArray( $arguments );
	}

	public static function executeZalgo( $arguments ) {
		$zalgoObj = new eGlooZalgo( $arguments );
		$zalgoObj->execute();
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
			case 'application' :
				self::printHelpInfoForApplicationCommand();
				break;
			case 'bundle' :
				self::printHelpInfoForBundleCommand();
				break;
			case 'cache' :
				self::printHelpInfoForCacheCommand();
				break;
			case 'check' :
				self::printHelpInfoForCheckCommand();
				break;
			case 'config' :
			case 'configuration' :
				self::printHelpInfoForConfigurationCommand();
				break;
			case 'cube' :
			case 'cubes' :
				self::printHelpInfoForCubesCommand();
				break;
			case 'daemon' :
			case 'daemons' :
			case 'daemonmaster' :
			case 'dm' :
				self::printHelpInfoForDaemonMasterCommand();
				break;
			case 'data' :
			case 'dp' :
				self::printHelpInfoForDataProcessingCommand();
				break;
			case 'form' :
			case 'forms' :
				self::printHelpInfoForFormsCommand();
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
			case 'log' :
				self::printHelpInfoForLogCommand();
				break;
			case 'net' :
			case 'network' :
				self::printHelpInfoForNetworkCommand();
				break;
			case 'req' :
			case 'request' :
				self::printHelpInfoForRequestCommand();
				break;
			case 'run' :
			case 'runtime' :
				self::printHelpInfoForRunCommand();
				break;
			case 'search' :
				self::printHelpInfoForSearchCommand();
				break;
			case 'sim' :
			case 'simulate' :
				self::printHelpInfoForSimulateCommand();
				break;
			case 'stat' :
			case 'status' :
				self::printHelpInfoForStatusCommand();
				break;
			case 'sys' :
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
			case 'web' :
			case 'webroot' :
				self::printHelpInfoForWebRootCommand();
				break;
			case 'zalgo' :
				self::printHelpInfoForZalgoCommand();
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
		echo eGlooApplication::getHelpString() . "\n";
	}

	public static function printHelpInfoForBundleCommand() {
		echo eGlooBundle::getHelpString() . "\n";
	}

	public static function printHelpInfoForCacheCommand() {
		echo eGlooCache::getHelpString() . "\n";
	}

	public static function printHelpInfoForCheckCommand() {
		echo eGlooSystemChecks::getHelpString() . "\n";
	}

	public static function printHelpInfoForConfigurationCommand() {
		echo eGlooConfiguration::getHelpString() . "\n";
	}

	public static function printHelpInfoForCubesCommand() {
		echo eGlooCube::getHelpString() . "\n";
	}

	public static function printHelpInfoForDaemonMasterCommand() {
		echo eGlooDaemonMaster::getHelpString() . "\n";
	}

	public static function printHelpInfoForDataProcessingCommand() {
		echo eGlooDataProcessing::getHelpString() . "\n";
	}

	public static function printHelpInfoForFormsCommand() {
		echo eGlooForms::getHelpString() . "\n";
	}

	public static function printHelpInfoForFrameworkCommand() {
		echo eGlooFramework::getHelpString() . "\n";
	}

	public static function printHelpInfoForGlobalCommand() {
		echo eGlooGlobal::getHelpString() . "\n";
	}

	public static function printHelpInfoForInfoCommand() {
		echo eGlooInfo::getHelpString() . "\n";
	}

	public static function printHelpInfoForInstallCommand() {
		echo eGlooInstall::getHelpString() . "\n";
	}

	public static function printHelpInfoForListCommand() {
		echo eGlooLister::getHelpString() . "\n";
	}

	public static function printHelpInfoForLogCommand() {
		echo eGlooLog::getHelpString() . "\n";
	}

	public static function printHelpInfoForNetworkCommand() {
		echo eGlooNetwork::getHelpString() . "\n";
	}

	public static function printHelpInfoForRequestCommand() {
		echo eGlooRequestLibrary::getHelpString() . "\n";
	}

	public static function printHelpInfoForRunCommand() {
		echo eGlooRuntime::getHelpString() . "\n";
	}

	public static function printHelpInfoForSearchCommand() {
		echo eGlooSystemSearch::getHelpString() . "\n";
	}

	public static function printHelpInfoForSimulateCommand() {
		echo eGlooSimulation::getHelpString() . "\n";
	}

	public static function printHelpInfoForStatusCommand() {
		echo eGlooSystemStatus::getHelpString() . "\n";
	}

	public static function printHelpInfoForSystemCommand() {
		echo eGlooSystem::getHelpString() . "\n";
	}

	public static function printHelpInfoForTestCommand() {
		echo eGlooTest::getHelpString() . "\n";
	}

	public static function printHelpInfoForUICommand() {
		echo eGlooUI::getHelpString() . "\n";
	}

	public static function printHelpInfoForUninstallCommand() {
		echo eGlooUninstall::getHelpString() . "\n";
	}

	public static function printHelpInfoForUpgradeCommand() {
		echo eGlooUpgrade::getHelpString() . "\n";
	}

	public static function printHelpInfoForWebRootCommand() {
		echo eGlooWebRoot::getHelpString() . "\n";
	}

	public static function printHelpInfoForZalgoCommand() {
		echo eGlooZalgo::getHelpString() . "\n";
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

