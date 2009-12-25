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
 * Test starter
 *
 * You should inherit this class and place your starter into
 * /test/starter/Starter.php
 *
 * @see build.xml
 * @package Test
 */
abstract class FaZend_Test_Starter {

    /**
     * Run it from build.xml
     *
     * @return void
     **/
    public static function run() {
        $starterPhp = APPLICATION_PATH . '/../../test/starter/Starter.php';
        if (!file_exists($starterPhp))
            return;

        require_once $starterPhp;
        $starter = new Starter();
        $starter->start();
    }

    /**
     * Make all initializations before tests
     *
     * @return void
     **/
    public final function start() 
    {
        $rc = new ReflectionClass($this);
        foreach ($rc->getMethods() as $method) {
            if (preg_match('/^\_start/', $method->getName())) {
                $this->{$method->getName()}();
            }
        }
    }

}
