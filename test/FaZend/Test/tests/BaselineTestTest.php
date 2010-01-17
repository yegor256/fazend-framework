<?php

require_once 'AbstractTestCase.php';

class FaZend_Test_tests_BaselineTestTest extends AbstractTestCase
{
    
    public function testTestWorksFine()
    {
        require_once 'FaZend/Test/tests/BaselineTest.php';
        $unit = new BaselineTest();
        $result = $unit->run();
        
        if (!$result->wasSuccessful())
            $this->fail("Failed to run BaselineTest");
    }

}
        