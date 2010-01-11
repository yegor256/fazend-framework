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
 * Simple and easy method for testing
 *
 * @package Application
 * @return void
 */
function bug($var = false)
{ 
    echo '<pre>' . htmlspecialchars(print_r($var, true)) . '</pre>'; 
    die(); 
}

/**
 * Cut string to a shorter one, with a nice ending
 *
 * @return string
 * @package Application
 */
function cutLongLine($line, $length = 100)
{
    if (strlen($line) <= $length)
        return $line;

    return substr($line, 0, $length-3) . '...';    
}

/**
 * Create and return validator
 *
 * @return FaZend_Validator
 */
function validate()
{
    return FaZend_Validator::factory();
}

// workaround 5.1-5.2 compatibility
if (!function_exists('sys_get_temp_dir')) {
    function sys_get_temp_dir()
    {
        if ($temp = getenv('TMP'))
            return $temp;
        if ($temp = getenv('TEMP'))
            return $temp;
        if ($temp = getenv('TMPDIR'))
            return $temp;
            
        // trying to create a temp directory
        $temp = realpath(APPLICATION_PATH . '/../fz-temp');
        if (!file_exists($temp))
            @mkdir($temp);
        
        if (is_dir($temp) && is_writable($temp))
            return $temp;
        
        throw new Exception('Function sys_get_temp_dir() is absent, probably you should upgrade to PHP 5.2+. ' . 
            'Also we failed to create a custom TEMP directory here: ' . $temp);
    }
}

// patch for PHP 5.2
if (!function_exists('lcfirst')) {
    function lcfirst($str) 
    {
        if (!isset($str[0]))
            return $str;
        return strtolower($str[0]) . substr($str, 1);
    }
}