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
class FaZend_View_Helper_PageLoadTime extends FaZend_View_Helper
{

    /**
     * Show the time in seconds of the page loading
     *
     * You should call this function at the end of your layout/scripts/layout.phtml
     *
     * @return string Time in seconds, properly formatted
     */
    public function pageLoadTime()
    {
        // this variable is set in FaZend/Application/index.php
        global $startTime; 

        // we calculate the difference, and format the value
        $time = round(microtime(true)-$startTime, 2); // in secs
        $html = sprintf('%0.2f', $time) . 'sec';

        // encolor with red, if more than 2 seconds
        if ($time > 2) {
            $html = '<b style="color:red;">' . $html . '</b>';
        }

        // labels and DIVs to show inside brackets
        $labels = $divs = array();
        
        // add information from DB profiler, if any
        if (!is_null(Zend_Db_Table::getDefaultAdapter())) {
            $profiler = Zend_Db_Table::getDefaultAdapter()->getProfiler();
            if ($profiler instanceof Zend_Db_Profiler) {
                $queries = $profiler->getQueryProfiles();
                if (is_array($queries)) {
                    $labels[] = sprintf(
                        "<span style='font-size:1em;cursor:pointer;' onclick='$(\"#fz__profiler\").toggle();'>" 
                        . "%d queries in %0.2fsec</span>", 
                        $profiler->getTotalNumQueries(),
                        $profiler->getTotalElapsedSecs()
                    );
                    $divs[] = sprintf(
                        "<div id='fz__profiler' style='display:none;'>%s</div>",
                        implode(
                            '<br/>', 
                            array_map(
                                create_function(
                                    '$query', 
                                    '
                                    return sprintf(
                                        "[%0.3f]&#32;%s", 
                                        $query->getElapsedSecs(),
                                        $query->getQuery()
                                    );
                                    '
                                ), 
                                $queries
                            )
                        )
                    );
                }
            }
        }

        // add information from system log
        $factory = FaZend_Log::getInstance();
        if ($factory->hasWriter('FaZendDebug') && !$factory->getWriter('FaZendDebug')->isEmpty()) {
            $log = $factory->getWriter('FaZendDebug')->getLog();

            $labels[] = sprintf(
                "<span style='font-size:1em;cursor:pointer;color:red;' title='%s' "
                . "onclick='$(\"#fz__syslog\").toggle();'>syslog&#32;(%d)</span>",
                _t('log messages from the script'),
                substr_count($log, "\n")
            );
            $divs[] = sprintf(
                "<pre id='fz__syslog' style='display:none;'>%s</pre>",
                $log
            );
        }

        if ($labels) {
            $html .= '&#32;[' . implode(',&#32;', $labels) . ']' . implode('', $divs);
            // jQuery is required for this    
            $this->getView()->includeJQuery();
        }

        return $html;
    }

}
