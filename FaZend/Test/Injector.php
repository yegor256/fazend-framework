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
     */
    public final function inject()
    {
        $rc = new ReflectionClass($this);
        foreach ($rc->getMethods() as $method) {
            if (preg_match('/^\_inject/', $method->getName())) {
                $this->{$method->getName()}();
            }
        }
    }
    
    /**
     * Bootstrap given resource
     *
     * You may need this method when some resource should be loaded
     * BEFORE injector. Injector is executed before all other resources
     * loading, that's why you may need to bootstrap something explicitly.
     *
     * @param string Name of the resource to bootstrap
     * @return mixed
     */
    protected function _bootstrap($resource) 
    {
        return Zend_Registry::get('Zend_Application')
            ->getBootstrap()->bootstrap($resource);
    }

}
