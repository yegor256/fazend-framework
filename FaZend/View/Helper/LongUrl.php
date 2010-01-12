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
 * Generates URL that includes http:// prefix
 *
 * @package View
 * @subpackage Helper
 */
class FaZend_View_Helper_LongUrl extends FaZend_View_Helper {

    /**
     * Returns full long url with http:// prefix
     *
     * @param  array Options passed to the assemble method of the Route object.
     * @param  mixed The name of a Route to use. If null it will use the current Route
     * @param  bool Whether or not to reset the route defaults with those provided
     * @return string Url for the link href attribute.
     */
    public function longUrl(array $urlOptions = array(), $name = null, $reset = false, $encode = true) {
        return $this->getView()->serverUrl() . $this->getView()->url($urlOptions, $name, $reset, $encode);
    }

}
