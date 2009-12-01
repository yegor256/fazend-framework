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
 * Form element
 *
 * @package UiModeller
 * @subpackage Mockup
 */
abstract class FaZend_Pan_Ui_Meta_FormElement extends FaZend_Pan_Ui_Meta_Abstract {

    /**
     * Field is visible in TWO columns (name of field, field)
     *
     * @var boolean
     */
    protected $_alignedStyle = true;

    /**
     * Set the style of form, an aligned one
     *
     * @return this
     */
    public function setAlignedStyle($style = true) {
        $this->_alignedStyle = $style;
        return $this;
    }
    
}
