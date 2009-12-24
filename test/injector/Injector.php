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
 * This class injects test components into a workable system
 *
 * Bootstrap calls this class only when APPLICATION_ENV is not 'production'
 *
 * @see bootstrap.php
 * @package test
 */
class Injector extends FaZend_Test_Injector 
{

    /**
     * Make all injections necessary
     *
     * @return void
     **/
    public function inject() 
    {
        $rc = new ReflectionClass($this);
        foreach ($rc->getMethods() as $method) {
            if (preg_match('/^\_inject/', $method->getName())) {
                $this->{$method->getName()}();
            }
        }
    }

    /**
     * Inject POS initial data
     *
     * @return void
     **/
    protected function _injectPos() 
    {
        // We should work with our own mock root object
        FaZend_Pos_Abstract::setRootClass('Model_Pos_Root');
        FaZend_Pos_Abstract::root();
    }

}