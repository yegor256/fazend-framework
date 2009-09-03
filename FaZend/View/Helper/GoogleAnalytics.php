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
 * Show google analytics JavaScript in your layout
 *
 * @see http://naneau.nl/2007/07/08/use-the-url-view-helper-please/
 */
class FaZend_View_Helper_GoogleAnalytics extends FaZend_View_Helper {

    /**
     * Show GA script
     *
     * @param boolean Show google analytics if the user is logged in?
     * @return string HTML code of GA
     */
    public function googleAnalytics($showForLoggedInUser = true) {

        // don't show if the user is not logged in
        if (!$showForLoggedInUser && FaZend_User::isLoggedIn())
            return false;

        // skip it for the testing and development environments           
        if (APPLICATION_ENV !== 'production')
            return "<!-- google analytics skipped -->\n";

        $this->getView()->addScriptPath(FAZEND_PATH . '/View/scripts/');
        return $this->getView()->render('google-analytics.phtml');

    }    

}
