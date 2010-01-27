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
 *
 * @see http://naneau.nl/2007/07/08/use-the-url-view-helper-please/
 * @package View
 * @subpackage Helper
 */
abstract class FaZend_View_Helper
{

    /**
     * Instance of the view
     *
     * @var Zend_View
     */
    private $_view;

    /**
    * Save view locally
    *
    * @return void
    */
    public function setView(Zend_View_Interface $view)
    {
        $this->_view = $view;
    }       

    /**
    * Get view saved locally
    *
    * @return Zend_View
    */
    public function getView()
    {
        return $this->_view;
    }

}
