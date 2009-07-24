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
 * One-page mockup builder
 *
 * @package FaZend 
 */
class FaZend_UiModeller_Mockup {

    /**
     * Name of the script, like 'index/settings'
     *
     * @var string
     */
    protected $_script;
    
    /**
     * The image we build
     *
     * @var FaZend_Image
     */
    protected $_image;
    
    /**
     * Get the image
     *
     * @var int
     */
    public function __construct($script) {

        $this->_script = $script;

    }

    /**
     * Build PNG image
     *
     * @var string
     */
    public function png() {

        // get the size of the image
        list($width, $height) = $this->_getDimensions();

        $line = 0;
        foreach ($this->_getMetas() as $meta) {

            $meta->draw($line);
            $line++;

        }

        // return the PNG content
        ob_start();
        imagepng($this->_getImage());
        return ob_get_clean();

    }

    /**
     * Get the image
     *
     * @return FaZend_Image
     */
    public function _getImage() {

        if (!isset($this->_image)) {
            // get the size of the image
            list($width, $height) = $this->_getDimensions();

            // create new image
            $this->_image = new FaZend_Image($width, $height);
        }

        return $this->_image;
    }

    /**
     * Get the list of metas in the page
     *
     * @return FaZend_UiModeller_Mockup_Meta_Interface[]
     */
    public function _getMetas() {

        if (isset($this->_metas))
            return $this->_metas;

        $this->_metas = array();

        $this->_metas[] = new FaZend_UiModeller_Mockup_Meta_Text($this->_getImage());

        return $this->_metas;

    }

    /**
     * Calculate the size of the image
     *
     * @return array
     */
    public function _getDimensions() {

        $metas = $this->_getMetas();

        $total = count($metas);

        return array(800, $total * 150);

    }

}
