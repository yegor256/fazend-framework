<?php
/**
 * @version $Id$
 */

require_once 'AbstractTestCase.php';

class FaZend_LogTest extends AbstractTestCase
{

    public function testLogWorks()
    {
        logg('Logging mechanism works properly');
        $this->assertNotEquals(
            true,
            FaZend_Log::getInstance()
            ->getWriter(FaZend_Application_Resource_fz_logger::DEBUG_WRITER)
            ->isEmpty()
        );
    }

    public function testObserverWorks()
    {
        FaZend_Log::getInstance()
            // try to add a new one
            ->addWriter('Memory', 'test')
            // delete this one
            ->removeWriter('test')
            // add new named one and delete it right now
            ->addWriter('Memory', 'testWriter')
            ->removeWriter('testWriter')
            ->addWriter('Memory', 'test');

        $writer = FaZend_Log::getInstance()->getWriter('test');
    }

}
