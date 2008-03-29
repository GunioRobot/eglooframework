<?php
require_once( "PHPUnit/Framework/TestSuite.php" );

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