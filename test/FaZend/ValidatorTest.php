<?php
/**
 *
 * Copyright (c) FaZend.com
 * All rights reserved.
 *
 * You can use this product "as is" without any warranties from authors.
 * You can change the product only through Google Code repository
 * at http://code.google.com/p/fazend
 * If you have any questions about privacy, please email privacy@fazend.com
 *
 * @copyright Copyright (c) FaZend.com
 * @version $Id$
 * @category FaZend
 */

require_once 'AbstractTestCase.php';

/**
 * Test the validator mechanism
 *
 * @package test
 */
class FaZend_ValidatorTest extends AbstractTestCase {

    /**
     * Test it
     *
     * @return void
     */
    public function testSimpleScenarioWorks() {

        // positive case
        validate()
            ->true(true, 'works')
            ->false(false)
            ->type('test', 'string')
            ->startWith('works good?', 'works')
            ->notStartWith('works', '_')
            ->regex('test', '/^\w+$/', 'Regular expression is correct')
            ->instanceOf(new FaZend_StdObject(), 'FaZend_StdObject');
            
        $step = 6;
        // negative case
        do {
            try {
                switch ($step) { 
                    case 0:
                        validate()->regex('try', '/^\d+/');
                    case 1:
                        validate()->true(false, 'it is ok');
                    case 2:
                        validate()->false(true);
                    case 3:
                        validate()->startWith('works', 'oops');
                    case 4:
                        validate()->true(false, 'it is ok');
                    case 5:
                        validate()->regex('try', 'invalid regular expression');
                    default:
                        FaZend_Exception::raise('FaZend_Validator_Failure');
                        break;
                }
                $this->fail('Exception should be raised, step: ' . $step);
            } catch (FaZend_Validator_Failure $e) {
                FaZend_Log::info($e->getMessage());
            }
            
        } while ($step--);

    }
    
}
