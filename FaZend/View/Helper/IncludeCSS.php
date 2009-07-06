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
 *
 * @see http://naneau.nl/2007/07/08/use-the-url-view-helper-please/
 * @package FaZend 
 */
class FaZend_View_Helper_IncludeCSS extends FaZend_View_Helper {

    /**
     * Include a CSS file as a link
     *
     * @return void
     */
    public function includeCSS($script) {

        $this->getView()->headLink()->appendStylesheet($this->getView()->url(array('css'=>$script), 'css', true));

    }

}
