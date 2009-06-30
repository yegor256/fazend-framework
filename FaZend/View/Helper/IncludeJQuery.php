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
 * Includes JQuery into the list of scripts to load
 * 
 * @see http://naneau.nl/2007/07/08/use-the-url-view-helper-please/
 * @package FaZend 
 */
class FaZend_View_Helper_IncludeJQuery extends FaZend_View_Helper {

    const JQUERY_PATH = 'http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js';

    /**
     * Include a jQuery minified JS file as a link
     *
     * @return void
     */
    public function includeJQuery() {

        $this->getView()->headScript()->appendFile(self::JQUERY_PATH);

    }

}
