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
 * Test case
 *
 * @package tests
 */
class FaZend_LogTest extends AbstractTestCase {
    
    public function testLogWorks () {

        FaZend_Log::info('Logging mechanism works properly');
        $this->assertNotEquals(true, FaZend_Log::getInstance()->getWriter('FaZendDebug')->isEmpty());

    }

    public function testObserverWorks () {

        FaZend_Log::getInstance()

            // try to add a new one
            ->addWriter('Memory', 'test')

            // delete this one
            ->removeWriter('test')

            // add new named one and delete it right now
            ->addWriter('Memory', 'testWriter')
            ->removeWriter('testWriter')

            ->addWriter('Memory', 'test')
            ;

        $writer = FaZend_Log::getInstance()->getWriter('test');

    }

}
        