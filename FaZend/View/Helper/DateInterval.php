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

/**
 * Show interval between now and the given date
 *
 * @package View
 * @subpackage Helper
 */
class FaZend_View_Helper_DateInterval
{

    /**
     * Show interval between now and the given date
     *
     * @param int|Zend_Date Date/time value, in seconds
     * @return string
     */
    public function dateInterval($time)
    {
        $compared = time();

        $hoursDifference = ($compared - $time) / (60*60);    

        // we can't compare future and past...
        if ($hoursDifference < 0)
            $sign = '-';
        else
            $sign = '';    

        $hoursDifference = abs($hoursDifference);

        switch (true) {
            // more than 24months days - we show years
            case $hoursDifference > 24 * 30 * 24:
                $years = $hoursDifference/(24*30*12);
                return $sign . self::_mod($years) . 'years';

            // more than 60 days - we show months
            case $hoursDifference > 24 * 60:
                return $sign . self::_mod($hoursDifference/(24*30)) . 'months';

            // more than 14days - we show weeks
            case $hoursDifference > 24 * 14:
                return $sign . self::_mod($hoursDifference/(24*7)) . 'weeks';

            // more than 2 days we shouw days    
            case $hoursDifference > 48:
                $hours = round(fmod($hoursDifference, 24));
                return $sign . round($hoursDifference/24) . 'days' . ($hours ? "&nbsp;{$hours}hrs" : '');

            // more than 5 hours - we should hours    
            case $hoursDifference > 5:
                return $sign . round($hoursDifference) . 'hrs';    

            // more than 1 hour - we should hour+min    
            case $hoursDifference >= 1:
                $minutes = round(fmod($hoursDifference, 1) * 60);
                return $sign . floor($hoursDifference) . 'hrs' . ($minutes ? "&nbsp;{$minutes}min" : '');    

            // otherwise just minutes    
            default:
                return $sign . round($hoursDifference * 60) . 'min';
        }
    }

    /**
     * To convert "A/B" into "C + 1/2 or 1/4 or 3/4"
     *
     * @param float Value, with float point, e.g. 13.79
     * @return string String, e.g. "13 3/4"
     */
    protected static function _mod($a)
    {
        $str = floor($a);
        
        $mod = $a - $str;

        switch (true) {
            case ($mod > 0.75):
                $str .= '&frac34;';
                break;

            case ($mod > 0.5):
                $str .= '&frac12;';
                break;

            case ($mod > 0.25):
                $str .= '&frac14;';
                break;
        }    

        return $str;
    }

}
