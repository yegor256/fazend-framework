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
 * Test injector
 *
 * You should inherit this class and place your injector into
 * /test/injector/Injector.php
 *
 * @see http://fazend.com/a/2009-11-TestInjection.html
 * @see FaZend_Application_Resource_Fazend::_initTestInjection()
 * @package Test
 */
abstract class FaZend_Test_Injector {

    /**
     * Make all existing injections
     *
     * @return void
     **/
    public final function inject() {
        $rc = new ReflectionClass($this);
        foreach ($rc->getMethods() as $method) {
            if (preg_match('/^\_inject/', $method->getName())) {
                $this->{$method->getName()}();
            }
        }
    }

}
