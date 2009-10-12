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
 * Show size in bytes/Mb/Kb/Tb/etc.
 *
 * @see http://naneau.nl/2007/07/08/use-the-url-view-helper-please/
 * @package View
 * @subpackage Helper
 */
class FaZend_View_Helper_ShowBytes {

    /**
     * Show size
     *
     * @return string
     */
    public function showBytes($size) {
        return self::show($size);
    }

    /**
     * Show size
     *
     * @return string
     */
    public static function show($size) {

        switch (true) {
            case($size < 1024*5):
                return $size.'bytes';
        
            case($size < 1024*1024*4):
                return round($size/1024, 2).'Kb';    
        
            case($size < 1024*1024*1024*3):
                return round($size/(1024*1024), 2).'Mb';    

            case($size < 1024*1024*1024*1024*2):
                return round($size/(1024*1024*1024), 2).'Gb';    
        
            default:    
                return round($size/(1024*1024*1024*1024), 2).'Tb';    
        }
    }

}
