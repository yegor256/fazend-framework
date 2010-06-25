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
 * Compress and minify CSS text and return it
 *
 * @package View
 * @subpackage Helper
 */
class FaZend_View_Helper_StripCSS extends FaZend_View_Helper
{

    /**
     * Strip CSS and include it into HEAD section of the layout
     *
     * @param string Script name with CSS content
     * @return string Stripped CSS content
     */
    public function stripCSS($script)
    {
        // render the CSS file
        $content = $this->getView()->render($script);

        // compress it
        $filter = new FaZend_View_Filter_CssCompressor();
        $content = $filter->filter($content);

        // add it to header
        $this->getView()->headStyle($content);
        
        // and return it as compressed version
        return $content;
    }

}
