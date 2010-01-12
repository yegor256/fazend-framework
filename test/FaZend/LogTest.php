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
 * Test case
 *
 * @package tests
 */
class FaZend_LogTest extends AbstractTestCase {
    
    public function testLogWorks () {

        logg('Logging mechanism works properly');
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
        