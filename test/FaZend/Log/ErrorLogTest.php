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

class FaZend_ErrorLogTest extends AbstractTestCase {
    
    public function testLoggingWorks () {

        $log = FaZend_Log_ErrorLog::getInstance();
        $log->crit('test message');

        //$this->assertRegExp('/test message/', $written);

    }

}
        