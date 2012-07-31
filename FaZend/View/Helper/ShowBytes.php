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
 * Show size in bytes/Mb/Kb/Tb/etc.
 *
 * @see http://naneau.nl/2007/07/08/use-the-url-view-helper-please/
 * @package View
 * @subpackage Helper
 */
class FaZend_View_Helper_ShowBytes
{

    /**
     * Show size
     *
     * @return string
     */
    public function showBytes($size)
    {
        return self::show($size);
    }

    /**
     * Show size
     *
     * @return string
     */
    public static function show($size)
    {
        switch (true) {
            case($size < 1024*5):
                $txt = $size.'bytes';
                break;

            case($size < 1024*1024*4):
                $txt = round($size/1024, 2).'Kb';
                break;

            case($size < 1024*1024*1024*3):
                $txt = round($size/(1024*1024), 2).'Mb';
                break;

            case($size < 1024*1024*1024*1024*2):
                $txt = round($size/(1024*1024*1024), 2).'Gb';
                break;

            default:
                $txt = round($size/(1024*1024*1024*1024), 2).'Tb';
                break;
        }
        return $txt;
    }

}
