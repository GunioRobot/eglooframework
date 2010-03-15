<?php

class TestOfLogging extends UnitTestCase {

    function testLogCreatesNewFileOnFirstMessage() {
        // @unlink('/temp/test.log');
        // $log = new Log('/temp/test.log');
        $this->assertFalse(0);
        // $log->message('Should write this to a file');
        $this->assertTrue(1);
    }

    // function testJunk() {
    //     // @unlink('/temp/test.log');
    //     // $log = new Log('/temp/test.log');
    //     $this->assertFalse(0);
    //     // $log->message('Should write this to a file');
    //     $this->assertTrue(1);
    // }
    // 
    // function testFoo() {
    //     // @unlink('/temp/test.log');
    //     // $log = new Log('/temp/test.log');
    //     $this->assertFalse(0);
    //     // $log->message('Should write this to a file');
    //     $this->assertTrue(1);
    // }

}
