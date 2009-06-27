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
 * Compress and minify CSS text and return it
 *
 * @package FaZend 
 */
class FaZend_View_Helper_StripCSS extends FaZend_View_Helper {

    /**
     * Strip CSS and include it into HEAD section of the layout
     *
     * @param string CSS content
     * @return void
     */
    public function stripCSS($script) {

        $content = $this->getView()->render($script);

        $filter = new FaZend_View_Filter_CssCompressor();
        $content = $filter->filter($content);

        $this->getView()->headStyle($content);

    }

}
