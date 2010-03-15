<?php

include_once(eGlooConfiguration::getFrameworkRootPath() . '/PHP/SimpleTest/autorun.php');

class eGlooCoreTestSuite extends TestSuite {

	function eGlooCoreTestSuite() {
		$this->TestSuite('All tests');

		// $testOfLogging = new TestOfLogging();
		// $testOfRocking = new TestOfRocking();

		$this->addFile(eGlooConfiguration::getFrameworkRootPath() . '/PHP/TestOfLogging.php');
	}

}