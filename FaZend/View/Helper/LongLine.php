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
 * Cut the line to a required length
 *
 * @package View
 * @subpackage Helper
 */
class FaZend_View_Helper_LongLine {

    /**
     * Cut the line to a required length
     *
     * @return string
     */
    public function longLine($line, $length) {

        // if it's short enough - just return it
        if (strlen($line) < $length)
            return $line;

        // cut the end
        return substr($line, 0, $length-3) . '...';    

    }

}
