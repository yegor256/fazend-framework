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
 * Generates URL that includes http:// prefix
 *
 * @package helpers
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

        return WEBSITE_URL . $this->getView()->url($urlOptions, $name, $reset, $encode);

    }

}
