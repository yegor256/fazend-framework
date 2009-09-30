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
            ->regex('test', '/^\w+$/', 'Regular expression is correct')
            ->instanceOf(new FaZend_StdObject(), 'FaZend_StdObject');
            
        // negative case
        try {
            validate()
                ->true(false, 'it is ok');
            $this->fail('Exception should be raised');
        } catch (FaZend_Validator_Failure $e) {
            // it's fine
        }

    }

}
