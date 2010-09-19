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
 * Include a link to CSS.
 *
 * @see http://naneau.nl/2007/07/08/use-the-url-view-helper-please/
 * @package View
 * @subpackage Helper
 */
class FaZend_View_Helper_IncludeCSS extends FaZend_View_Helper
{

    /**
     * Include a CSS file as a link.
     *
     * @param string File name inside 'views/scripts/css' directory
     * @param boolean Shall we append it to headLink() or just return a HTTP URL?
     * @return string The URL
     */
    public function includeCSS($script, $append = true)
    {
        $url = $this->getView()->url(
            array(
                'revision' => FaZend_Revision::get(),
                'css' => $script
            ), 
            'fz__css', // route name, see routes.ini
            true, 
            false
        );
        
        if ($append) {
            $this->getView()->headLink()->appendStylesheet($url);
        }
        return $url;
    }

}
