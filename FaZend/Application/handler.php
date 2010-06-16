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

if (defined('APPLICATION_ENV') && APPLICATION_ENV !== 'production') {
    if (version_compare(PHP_VERSION, '5.3') >= 0) {
        set_error_handler(
            function($errno, $errstr, $errfile, $errline)
            {
                if (in_array($errno, array(E_WARNING)) && error_reporting() == 0) {
                    return;
                }
                echo "{$errno} {$errstr}, file: {$errfile} ({$errline})\n";
            }
        );
    } else {
        set_error_handler(
            create_function(
                '$errno, $errstr, $errfile, $errline',
                '
                if (in_array($errno, array(E_WARNING)) && error_reporting() == 0) {
                    return;
                }
                echo "{$errno} {$errstr}, file: {$errfile} ({$errline})\n";
                '
            )
        );
    }
}
