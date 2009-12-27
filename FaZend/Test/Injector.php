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

/**
 * Test injector parent class, abstract
 *
 * You should inherit this class and place your injector into
 * /test/injector/Injector.php. For more information about this concept,
 * read an article at fazend.com blog (link you can find below).
 *
 * You inherit your class from this one and define _init*() methods. All
 * of them will be called BEFORE unit tests and on every page click in the
 * "development" environment.
 *
 * This class is a good place for:
 *  - making automatic login of a test user
 *  - replacing real-life SOAP/RPC/REST proxies with mocks
 *  - turning logging ON in certain classes
 *  - turning some time-consuming functionality OFF
 *
 * @see http://fazend.com/a/2009-11-TestInjection.html
 * @see FaZend_Application_Resource_Fazend::_initTestInjection()
 * @see FaZend_Test_Starter
 * @package Test
 */
abstract class FaZend_Test_Injector
{

    /**
     * Make all existing injections
     *
     * @return void
     **/
    public final function inject()
    {
        $rc = new ReflectionClass($this);
        foreach ($rc->getMethods() as $method) {
            if (preg_match('/^\_inject/', $method->getName())) {
                $this->{$method->getName()}();
            }
        }
    }

}
