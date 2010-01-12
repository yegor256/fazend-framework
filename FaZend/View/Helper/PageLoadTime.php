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

require_once 'FaZend/View/Helper.php';

/**
 * Show the time in seconds of the page loading
 *
 * @package View
 * @subpackage Helper
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
                     "<div id='profiler' style='display:none;'>" . 
                     implode('<br/>', array_map(create_function('$query', 
                     'return "[" . sprintf("%0.3f", $query->getElapsedSecs()) . "]&#32;" . $query->getQuery();'), $queries)) . 
                     '</div>';
        
                // jQuery is required for this    
                $this->getView()->includeJQuery();

            }

         }

         // add information from system log
         if ((APPLICATION_ENV == 'development') && !FaZend_Log::getInstance()->getWriter('FaZendDebug')->isEmpty()) {

             // jQuery is necessary
             $this->getView()->includeJQuery();
             $log = FaZend_Log::getInstance()->getWriter('FaZendDebug')->getLog();

             $html .= "&#32;<span style='cursor:pointer;color:red;' " . 
                "title='Log messages from the script' ".
                "onclick='$(\"#syslog\").toggle();'>syslog(" . substr_count($log, "\n"). ")</span>".
                 "<pre id='syslog' style='display:none;'>{$log}</pre>";
         }

         return $html;

     }

}
