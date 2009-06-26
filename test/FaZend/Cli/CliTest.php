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

class FaZend_Cli_CliTest extends AbstractTestCase {
    
    public function testCliCallsAreProcessed () {

        $param = rand(100, 999);

        chdir(APPLICATION_PATH . '/public');
        $result = shell_exec('php index.php OSVersion --param=' . $param . ' 2>&1');

        $this->assertNotEquals(false, $result, "Empty result, why?");

    }

}
        