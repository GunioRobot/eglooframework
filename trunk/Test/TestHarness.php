<?php
/**
 * TestHarness Class File
 *
 * Needs to be commented
 * 
 * Copyright 2008 eGloo, LLC
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
 * @copyright 2008 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package Testing
 * @version 1.0
 */

require_once( "PHPUnit/Framework/TestSuite.php" );

/**
 * TestHarness
 * 
 * Needs to be commented
 *  
 * @package Testing
 */
class TestHarness extends PHPUnit_Framework_TestSuite {
    private $seen = array();
    
    public function __construct() {
        parent::__construct();
        
        foreach( get_declared_classes() as $class ) {
            $this->seen[ $class ] = true;   
        }

        $this->findTestCases( './units' );
        $this->registerTestCases();
    }

    private function findTestCases( $directory ) {
        $handler = opendir( $directory );   
        
        while( $file = readdir( $handler ) ) {
            if ( !is_dir( $file ) && 
                 substr( $file, -12, 12 ) == 'TestCase.php' ) {
                require_once( './units/' . $file );
            }
        }
        
        closedir( $handler );
    } // findTestCases

    public function registerTestCases() {
        foreach( get_declared_classes() as $class ) {
            if( array_key_exists( $class, $this->seen ) ) {
                continue;
            }
            
            $this->seen[ $class ] = true;
            if ( substr( $class, -8, 8 ) == 'TestCase' ) {
                echo "Adding $class\n";
                $this->addTestSuite( $class );
            }                   
        } 
    } // register
} // TestHarness

?>