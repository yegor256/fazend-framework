<?php
/**
 * @version $Id$
 */

/**
 * @see AbstractTestCase
 */
require_once 'AbstractTestCase.php';

class FaZend_Application_FunctionsTest extends AbstractTestCase
{

    public function testCutLongLine()
    {
        $this->assertEquals(
            'te...',
            cutLongLine('test me', 5)
        );
    }
    
    public function testTranslator()
    {
        $this->assertEquals(
            'test 13',
            _t('test %d', 13)
        );
    }
    
}
