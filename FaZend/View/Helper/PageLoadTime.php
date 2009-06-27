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
 * Show the time in seconds of the page loading
 *
 * @package FaZend
 */
class FaZend_View_Helper_PageLoadTime {

    /**
     * Show the time in seconds of the page loading
     *
     * You should call this function at the end of your layout/scripts/layout.phtml
     *
     * @return string Time in seconds, properly formatted
     */
    public function pageLoadTime() {

         // this variable is set in FaZend/Application/index.php
         global $startTime; 

         // we calculate the difference, and format the value
         return sprintf('%0.2f', round(microtime(true)-$startTime, 2)) . 'sec';

     }

}
