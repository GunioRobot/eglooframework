<?php
namespace eGloo\Daemon;

use \System_Daemon as System_Daemon;

/**
 * eGloo\Daemon\TestDaemon Class File
 *
 * Contains the class definition for the eGloo\Daemon\TestDaemon
 * 
 * Copyright 2011 eGloo LLC
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *		  http://www.apache.org/licenses/LICENSE-2.0
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
 * @package Daemons
 * @version 1.0
 */

/**
 * eGloo\Daemon\TestDaemon
 *
 * $short_description
 *
 * $long_description
 *
 * @category System
 * @package Daemons
 */
class TestDaemon extends Daemon {

	public function start() {
		// Setup
		$options = array(
			'appName' => 'test',
			'appDir' => dirname(__FILE__),
			// 'appDescription' => 'eGloo Test Daemon',
			// 'sysMaxExecutionTime' => '0',
			// 'sysMaxInputTime' => '0',
			// 'sysMemoryLimit' => '1024M',
			// 'appRunAsGID' => 1000,
			// 'appRunAsUID' => 1000,
		);

		System_Daemon::setOptions($options);
		System_Daemon::start();
	}

	public function run() {
		// Run your code
		// Here comes your own actual code

		// This variable gives your own code the ability to breakdown the daemon:
		$runningOkay = true;

		// This variable keeps track of how many 'runs' or 'loops' your daemon has
		// done so far. For example purposes, we're quitting on 3.
		$cnt = 1;

		// While checks on 3 things in this case:
		// - That the Daemon Class hasn't reported it's dying
		// - That your own code has been running Okay
		// - That we're not executing more than 3 runs
		while (!System_Daemon::isDying() && $runningOkay && $cnt <=3) {
			// What mode are we in?
			$mode = '"'.(System_Daemon::isInBackground() ? '' : 'non-' ).
				'daemon" mode';

			// Log something using the Daemon class's logging facility
			// Depending on runmode it will either end up:
			//	- In the /var/log/logparser.log
			//	- On screen (in case we're not a daemon yet)
			System_Daemon::info('{appName} running in %s %s/3',
				$mode,
				$cnt
			);

			// In the actuall logparser program, You could replace 'true'
			// With e.g. a	parseLog('vsftpd') function, and have it return
			// either true on success, or false on failure.
			$runningOkay = true;
			//$runningOkay = parseLog('vsftpd');

			// Should your parseLog('vsftpd') return false, then
			// the daemon is automatically shut down.
			// An extra log entry would be nice, we're using level 3,
			// which is critical.
			// Level 4 would be fatal and shuts down the daemon immediately,
			// which in this case is handled by the while condition.
			if (!$runningOkay) {
				// System_Daemon::err('parseLog() produced an error, '.
				//	   'so this will be my last run');
			}

			// Relax the system by sleeping for a little bit
			// iterate also clears statcache
			System_Daemon::iterate(2);

			$cnt++;
		}
	}

	public function pause() {
		
	}

	public function stop() {
		// Shut down the daemon nicely
		// This is ignored if the class is actually running in the foreground
		System_Daemon::stop();
	}

	public function kill() {
	}

}

deprecate( __FILE__, '\eGloo\Daemons\TestDaemon' );
