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
 * @see FaZend_View_Helper
 */
require_once 'FaZend/View/Helper.php';

/**
 * Includes JQuery into the list of scripts to load
 * 
 * @see http://naneau.nl/2007/07/08/use-the-url-view-helper-please/
 * @package View
 * @subpackage Helper
 */
class FaZend_View_Helper_IncludeJQuery extends FaZend_View_Helper
{

    /**
     * Absolute URL of the jQuery JS
     * @link http://docs.jquery.com/Downloading_jQuery
     */
    const JQUERY_PATH = 'http://code.jquery.com/jquery-1.4.2.min.js';

    /**
     * Include a jQuery minified JS file as a link
     *
     * @return void
     */
    public function includeJQuery()
    {
        $this->getView()->headScript()->appendFile(self::JQUERY_PATH);
    }

}
