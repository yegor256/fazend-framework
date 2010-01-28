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
 * Show google analytics JavaScript in your layout
 *
 * @see http://naneau.nl/2007/07/08/use-the-url-view-helper-please/
 * @package View
 * @subpackage Helper
 */
class FaZend_View_Helper_GoogleAnalytics extends FaZend_View_Helper
{

    /**
     * Show GA script
     *
     * @param boolean Show google analytics if the user is logged in?
     * @return string HTML code of GA
     */
    public function googleAnalytics($showForLoggedInUser = true)
    {
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
