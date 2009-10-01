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

require_once 'FaZend/View/Helper.php';

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
         $time = round(microtime(true)-$startTime, 2); // in secs
         $html = sprintf('%0.2f', $time) . 'sec';

         // encolor with red, if more than 2 seconds
         if ($time > 2)
             $html = '<b style="color:red;">' . $html . '</b>';

         // add information from DB profiler, if any
         if ((APPLICATION_ENV == 'development') && !is_null(Zend_Db_Table::getDefaultAdapter())) {

             $profiler = Zend_Db_Table::getDefaultAdapter()->getProfiler();

             $queries = $profiler->getQueryProfiles();

             if (is_array($queries)) {

                 $html .= "&#32;[<span style='cursor:pointer;' onclick='$(\"#profiler\").toggle();'>". 
                     $profiler->getTotalNumQueries() . ' queries</span>, ' . sprintf('%0.2f', $profiler->getTotalElapsedSecs()) . ' sec]' .
                     "<p id='profiler' style='display:none;'>" . 
                     implode('<br/>', array_map(create_function('$query', 
                     'return "[" . sprintf("%0.3f", $query->getElapsedSecs()) . "]&#32;" . $query->getQuery();'), $queries)) . 
                     '</p>';
        
                // jQuery is required for this    
                $this->getView()->includeJQuery();

            }

         }

         // add information from system log
         if ((APPLICATION_ENV == 'development') && !FaZend_Log::getInstance()->getWriter('FaZendDebug')->isEmpty()) {

             $log = FaZend_Log::getInstance()->getWriter('FaZendDebug')->getLog();

             $html .= "&#32;<span style='cursor:pointer;color:red;' " . 
                "title='Log messages from the script' ".
                "onclick='$(\"#syslog\").toggle();'>syslog(" . substr_count($log, "\n"). ")</span>".
                 "<pre id='syslog' style='display:none;'>{$log}</pre>";
         }

         return $html;

     }

}
