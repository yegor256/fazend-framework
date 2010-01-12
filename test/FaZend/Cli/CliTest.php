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

require_once 'AbstractTestCase.php';

class FaZend_Cli_CliTest extends AbstractTestCase
{
    
    public function testCliCallsAreProcessed ()
    {

        $param = rand(100, 999);

        chdir(APPLICATION_PATH . '/public');
        $result = shell_exec('php index.php OSVersion --param=' . $param . ' 2>&1');

        $this->assertNotEquals(false, $result, "Empty result, why?");

    }

}
        