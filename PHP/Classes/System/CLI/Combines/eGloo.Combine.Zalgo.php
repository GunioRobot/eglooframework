<?php
namespace eGloo\Combine;

/**
 * eGloo\Combine\Zalgo Class File
 *
 * Contains the class definition for the eGloo\Combine\Zalgo
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
 * eGloo\Combine\Zalgo
 *
 * $short_description
 *
 * $long_description
 *
 * @category System
 * @package CLI
 * @subpackage Combines
 */
class Zalgo extends Combine {

	/**
	 * @var array List of supported commands and their options/required arguments
	 */
	protected static $_supported_commands = array(
		'_empty' => array(),
		'_zero_argument' => array(),
	);

	public function execute() {
		$retVal = null;

		switch( $this->_command ) {
			case '_empty' :
				$retVal = $this->info();
				break;
			case '_zero_argument' :
				$retVal = $this->info();
				break;
			default :
				break;
		}

		return $retVal;
	}

	protected function info() {
		echo 'H҉̵̞̟̠̖̗̘Ȅ̐̑̒̚̕̚ IS C̒̓̔̿̿̿̕̚̚̕̚̕̚̕̚̕̚̕̚OMI҉̵̞̟̠̖̗̘NG > ͡҉҉ ̵̡̢̛̗̘̙̜̝̞' .
			'̟̠͇̊̋̌̍̎̏̿̿̿̚ ҉ ҉҉̡̢̡̢̛̛̖̗̘̙̜̝̞̟̠̖̗̘̙̜̝̞̟̠̊̋̌̍̎̏̐̑̒̓̔̊̋̌̍̎̏̐̑ ͡҉҉ ' . "\n";
	}

	public function commandRequirementsSatisfied() {
		return true;
	}

	/**
	 * Return the help information for this class as a string
	 *
	 * @return string the help information for this class
	 * @author George Cooper
	 **/
	public static function getHelpString() {
		return 'T҉̵̞̟̠̖̗̘̙̜̝̞̟̠͇̊̋̌̍̎̏̐̑̒̓̔̊̋̌̍̎̏̐̑̒̓̔̿̿̿̕̚̕̚͡ ̒̓̔̕̚o invoke the h҉̵̞̟̠̖̗' .
			'̘̙̜̝̞̟̠͇̊̋̌̍̎̏̐̑̒̓̔̊̋̌̍̎̏̐̑̒̓̔̿̿̿̕̚̕̚͡ ̒̓̔̕̚ive-mind re҉̵̞̟̠̖̗̘̙̜̝̞̟̠͇̊̋̌̍' .
			'̎̏̐̑̒̓̔̊̋̌̍̎̏̐̑̒̓̔̿̿̿̕̚̕̚͡ ̒̓̔̕̚presenting chaos. Invoking҉̵̞̟̠̖̗̘̙̜̝̞̟̠͇̊' .
			'̋̌̍̎̏̐̑̒̓̔̊̋̌̍̎̏̐̑̒̓̔̿̿̿̕̚̕̚͡ ̒̓̔̕̚ the feeling of ch҉̵̞̟̠̖̗̘̙̜̝̞̟̠͇̊̋̌̍̎̏' .
			'̐̑̒̓̔̊̋̌̍̎̏̐̑̒̓̔̿̿̿̕̚̕̚͡ ̒̓̔̕̚aos. With out ҉̵̞̟̠̖̗̘̙̜̝̞̟̠͇̊̋̌̍̎̏̐̑̒̓̔̊̋̌̍' .
			'̎̏̐̑̒̓̔̿̿̿̕̚̕̚͡ ̒̓̔̕̚order.҉̵̞̟̠̖̗̘̙̜̝̞̟̠͇̊̋̌̍̎̏̐̑̒̓̔̊̋̌̍̎̏̐̑̒̓̔̿̿̿̕̚ ̌̍̎̏' .
			'̐̑̒̓̔̊̋̌̍̎̏̐̑̒̓̔̿̿̿̚̕̕̚̕̚͡ ͡҉҉ ̕̚͡ ̒̓̔̕̚ The Nezperd҉̵̞̟̠̖̗̘̙̜̝̞̟̠͇̊̋̌̍̎̏̐' .
			'̑̒̓̔̊̋̌̍̎̏̐̑̒̓̔̿̿̿̕̚̕̚͡ ̒̓̔̕̚ian hive-mind of chaos. Zalgo. He w҉̵̞̟̠̖̗̘̙̜' .
			'̝̞̟̠͇̊̋̌̍̎̏̐̑̒̓̔̊̋̌̍̎̏̐̑̒̓̔̿̿̿̕̚̕̚͡ ̒̓̔̕̚ho Waits Behind ҉̵̞̟̠̖̗̘̙̜̝̞̟̠͇̊̋' .
			'̌̍̎̏̐̑̒̓̔̊̋̌̍̎̏̐̑̒̓̔̿̿̿̕̚̕̚͡ ̒̓̔̕̚The Wall. ҉̵̞̟̠̖̗̘̙̜̝̞̟̠͇̊̋̌̍̎̏̐̑̒̓̔̊̋̌̍' .
			'̎̏̐̑̒̓̔̿̿̿̕̚̕̚͡ ̒̓̔̕̚ ҉̵̞̟̠̖̗̘̙̜̝̞̟̠͇̊̋̌̍̎̏̐̑̒̓̔̊̋̌̍̎̏̐̑̒̓̔̿̿̿̕̚̕̚ ͡ ̒̓̔̕̚, ';
	}

}

