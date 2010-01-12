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

if (defined('APPLICATION_ENV') && APPLICATION_ENV !== 'production')
    set_error_handler(array('FaZendErrorHandler', 'handle'));

/**
 * Error handler class
 *
 * @package Application
 */
class FaZendErrorHandler {
    
    /**
     * undocumented function
     *
     * @return void
     **/
    public static function handle($errno, $errstr, $errfile, $errline) {
        $errorType = array (
            E_ERROR             => 'ERROR',
            E_WARNING           => 'WARNING',
            E_PARSE             => 'PARSING ERROR',
            E_NOTICE            => 'NOTICE',
            E_CORE_ERROR        => 'CORE ERROR',
            E_CORE_WARNING      => 'CORE WARNING',
            E_COMPILE_ERROR     => 'COMPILE ERROR',
            E_COMPILE_WARNING   => 'COMPILE WARNING',
            E_USER_ERROR        => 'USER ERROR',
            E_USER_WARNING      => 'USER WARNING',
            E_USER_NOTICE       => 'USER NOTICE',
            E_STRICT            => 'STRICT NOTICE',
            E_RECOVERABLE_ERROR => 'RECOVERABLE ERROR'
        );
        
        // ignore warnings
        if (in_array($errno, array(E_WARNING)))
            return;

        $message = "{$errorType[$errno]} {$errstr}, file: {$errfile} ({$errline})";

        echo $message . "\n";

    }
    
}
