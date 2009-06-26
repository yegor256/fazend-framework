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
 * Show interval between now and the given date
 *
 * @see http://naneau.nl/2007/07/08/use-the-url-view-helper-please/
 * @package FaZend 
 */
class FaZend_View_Helper_DateInterval {

    public function dateInterval($time) {

        $compared = time ();

        $hoursDifference = ($compared - $time) / (60*60);    

        // we can't compare future and past...
        if ($hoursDifference < 0)
            $sign = '-';
        else
            $sign = '';    

        $hoursDifference = abs ($hoursDifference);

        switch (true) {
            // more than 24months days - we show years
            case $hoursDifference > 24 * 30 * 24:
                $years = $hoursDifference/(24*30*12);
                return $sign.self::mod($years).'years';

            // more than 60 days - we show months
            case $hoursDifference > 24 * 60:
                return $sign.self::mod($hoursDifference/(24*30)).'months';

            // more than 14days - we show weeks
            case $hoursDifference > 24 * 14:
                return $sign.self::mod($hoursDifference/(24*7)).'weeks';

            // more than 2 days we shouw days    
            case $hoursDifference > 48:
                $hours = round (fmod ($hoursDifference, 24));
                return $sign.round ($hoursDifference/24).'days'.($hours ? "&nbsp;{$hours}hrs" : '');

            // more than 5 hours - we should hours    
            case $hoursDifference > 5:
                return $sign.round ($hoursDifference).'hrs';    

            // more than 1 hour - we should hour+min    
            case $hoursDifference >= 1:
                $minutes = round (fmod ($hoursDifference, 1) * 60);
                return $sign.floor ($hoursDifference).'hrs'.($minutes ? "&nbsp;{$minutes}min" : '');    

            // otherwise just minutes    
            default:
                return $sign.round ($hoursDifference * 60).'min';
        }
    }

    /**
    * internal function - will convert A/B into C + 1/2 or 1/4 or 3/4
    *
    * @return string
    */
    public static function mod ($a) {
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
