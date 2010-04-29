<?php

require_once 'AbstractTestCase.php';

class FaZend_ValidatorTest extends AbstractTestCase
{

    public function testSimpleScenarioWorks()
    {
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
                        FaZend_Exception::raise(
                            'FaZend_Validator_Exception'
                        );
                        break;
                }
                $this->fail('Exception should be raised, step: ' . $step);
            } catch (FaZend_Validator_Exception $e) {
                logg($e->getMessage());
            } catch (Zend_Validate_Exception $e) {
                $this->fail("Exception thrown through our control, why? {$e->getMessage()}");
            }
            
        } while ($step--);
    }
    
}
