<?php
/**
 * @version $Id$
 */

/**
 * @see AbstractTestCase
 */
require_once 'AbstractTestCase.php';

class FaZend_ExceptionTest extends AbstractTestCase
{
    
    /**
     * @expectedException FirstTestException
     */
    public function testExceptionsAreDynamicallyCreated()
    {
        FaZend_Exception::raise('FirstTestException');
    }

    /**
     * @expectedException SecondTestException
     */
    public function testExceptionsCanInheritEachOther()
    {
        FaZend_Exception::raise(
            'SecondTestException',
            'Test works!',
            'FirstTestException'
        );
    }

    /**
     * @expectedException ThirdTestException
     */
    public function testExceptionsCanInheritFromInstances()
    {
        $e = new SecondTestException();
        FaZend_Exception::raise(
            'ThirdTestException',
            'Test works!',
            $e
        );
    }

    /**
     * @expectedException FaZend_Exception_InvalidParentException
     */
    public function testInvalidParentClass()
    {
        FaZend_Exception::raise(
            'ForthTestException',
            'Test works!',
            new FaZend_StdObject()
        );
    }

}
        