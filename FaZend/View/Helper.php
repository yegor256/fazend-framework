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
 *
 * @see http://naneau.nl/2007/07/08/use-the-url-view-helper-please/
 * @package FaZend 
 */
abstract class FaZend_View_Helper {

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
    public function setView(Zend_View_Interface $view) {
        $this->_view = $view;
    }       

    /**
    * Get view saved locally
    *
    * @return Zend_View
    */
    public function getView() {
        return $this->_view;
    }

}
