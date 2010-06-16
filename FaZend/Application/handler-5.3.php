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
 * @version $Id: handler.php 2048 2010-06-16 12:41:04Z yegor256@gmail.com $
 * @category FaZend
 */

set_error_handler(
    function($errno, $errstr, $errfile, $errline)
    {
        if (in_array($errno, array(E_WARNING)) && error_reporting() == 0) {
            return;
        }
        echo sprintf(
            "%d %s, file: %s(%d)\n",
            $errno, 
            $errstr,
            $errfile,
            $errline
        );
    }
);
