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
class FaZend_View_Helper_PageLoadTime extends FaZend_View_Helper {

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
         $html = sprintf('%0.2f', round(microtime(true)-$startTime, 2)) . 'sec';

         // add information from DB profiler, if any
         if (APPLICATION_ENV == 'development') {

             $profiler = Zend_Db_Table::getDefaultAdapter()->getProfiler();
             $html .= "&#32;[<span style='cursor:pointer;' onclick='$(#profiler).toggle();'>". 
                 $profiler->getTotalNumQueries() . ' queries</span>, ' . sprintf('%0.2f', $profiler->getTotalElapsedSecs()) . ' sec]' .
             	 "<p id='profiler' style='display:none;'>" . implode('<br/>', array_map(create_function('$query', 'return $query->getQuery();'), $profiler->getQueryProfiles())) . '</p>';

            // jQuery is required for this    
            $this->getView()->includeJQuery();

         }

         return $html;

     }

}
