<?php

include_once( eGlooConfiguration::getSimpleTestIncludePath() );

class eGlooCoreTestSuite extends TestSuite {

	function eGlooCoreTestSuite() {
		$this->TestSuite('All tests');

		$this->addFile(eGlooConfiguration::getFrameworkRootPath() . '/PHP/Classes/Testing/UnitTestCases/TestOfLogging.php');
		$this->addFile(eGlooConfiguration::getFrameworkRootPath() . '/PHP/Classes/Testing/UnitTestCases/TestOfRocking.php');
	}

}