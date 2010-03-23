<?php
/**
 * @version $Id$
 */

require_once 'AbstractTestCase.php';

class FaZend_Test_tests_BaselineTestTest extends AbstractTestCase
{
    
    public function testTestWorksFine()
    {
        require_once 'FaZend/Test/tests/BaselineTest.php';
        $unit = new BaselineTest('testCodeConformsToBaselines');
        $result = $unit->run();
        
        if (!$result->wasSuccessful()) {
            foreach ($result->errors() as $error)
                logg('Error: ' . $error->toString());
            foreach ($result->failures() as $failure)
                logg('Failure: ' . $failure->toString());
            $this->fail("Failed to run BaselineTest");
        }
    }

}
        