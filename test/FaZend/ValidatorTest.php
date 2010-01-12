<?php
/**
 * FaZend Framework
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt. It is also available 
 * through the world-wide-web at this URL: http://www.fazend.com/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@fazend.com so we can send you a copy immediately.
 *
 * @copyright Copyright (c) FaZend.com
 * @version $Id$
 * @category FaZend
 */

require_once 'AbstractTestCase.php';

/**
 * Test the validator mechanism
 *
 * @package tests
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
                logg($e->getMessage());
            }
            
        } while ($step--);

    }
    
}
