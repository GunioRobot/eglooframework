<?php
/*
 * Created on Aug 12, 2006
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

require_once ( "../php/classes/RequestProcessor.php" );
require_once ( "PHPUnit/Framework/TestCase.php" );

class RequestProcessorTestCase extends PHPUnit_Framework_TestCase {
    
    public function __constructor( $name ) {
        parent::__constructor( $name );
        echo "blah";
    }

    public function testGetRequestProcessorInstance() {
        $requestProcessor = RequestProcessor::getRequestProcessor();
        $this->assertNotNull( $requestProcessor );
    } 

}

?>
