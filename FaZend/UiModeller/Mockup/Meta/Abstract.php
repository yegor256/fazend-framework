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
 * Mockup meta element
 *
 * @package FaZend 
 */
abstract class FaZend_UiModeller_Mockup_Meta_Abstract implements FaZend_UiModeller_Mockup_Meta_Interface {

    /**
     * Image to put this element onto
     *
     * @var FaZend_Image
     */
    protected $_image;
    
    /**
     * Indent, right and left
     *
     * @var int
     */
    protected $_indent;
    
    /**
     * Label
     *
     * @var string
     */
    protected $_label;
    
    /**
     * Initialize this class
     *
     * @return void
     */
    public function __construct(FaZend_Image $image, $indent) {
        $this->_image = $image;
        $this->_indent = $indent;
    }

    /**
     * Set label
     *
     * @return void
     */
    public function setLabel($label) {
        $this->_label = $label;
    }

}
