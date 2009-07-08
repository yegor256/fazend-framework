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
function bug($var) { 
    echo '<pre>' . htmlspecialchars(print_r($var, true)) . '</pre>'; 
    die(); 
}

/**
 * Cut string to a shorter one, with a nice ending
 *
 * @return string
 * @package Application
 */
function cutLongLine($line, $length = 100) {

    if (strlen($line) <= $length)
        return $line;

    return substr($line, 0, $length-3) . '...';    
}


