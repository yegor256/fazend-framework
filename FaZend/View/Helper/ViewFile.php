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
 * Link to a static file, in "views/files" directory
 *
 * @package View
 * @subpackage Helper
 */
class FaZend_View_Helper_ViewFile extends FaZend_View_Helper
{

    /**
     * File in views/files directory
     *
     * @param string Path of the file, from /public directory
     * @param boolean Shall we render this file in Zend_View before delivery?
     * @return string URL of the file
     */
    public function viewFile($file, $toRender = false)
    {
        //trim the file name (just in case)
        $file = trim($file);

        return $this->getView()->url(
            array(
                'file' => $file,
                'render' => $toRender ? 'r' : '',
            ), 
            'file', // ROUTE name, see routes.ini
            true, // ..
            false // DON'T encode the params in URL
        );
    }

}
