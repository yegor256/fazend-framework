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

class FaZend_Cli_cli_PanTest extends AbstractTestCase
{
    
    public function testWeCanGetJSON()
    {
        chdir(APPLICATION_PATH . '/public');
        $result = shell_exec('php index.php Pan --pan=analysis 2>&1');

        $this->assertNotEquals(false, $result, "Empty result, why?");
        $json = Zend_Json::decode($result);
        
        FaZend_Log::info('JSON returned: ' . count($json) . ': ' . cutLongLine($result));
    }

}
        