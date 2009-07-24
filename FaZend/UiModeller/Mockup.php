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

        $title = $this->_script . '.phtml';

        $bbox = imagettfbbox(9, 0, $this->_getImage()->getFont('mockup.title'), $title);
        $this->_getImage()->imagettftext(9, 0, $width-$bbox[4] - 3, -$bbox[5], 
            $this->_getImage()->getColor('mockup.title'), 
            $this->_getImage()->getFont('mockup.title'), 
            $title);

        $line = 0;
        foreach ($this->_getMetas() as $meta) {

            $meta->draw($line);
            $line++;

        }

        // return the PNG content
        return $this->_getImage()->png();

    }

    /**
     * Get the image
     *
     * @return FaZend_Image
     */
    public function _getImage() {

        if (!isset($this->_image)) {

            // create it
            $this->_image = new FaZend_Image();

            // get the size of the image
            list($width, $height) = $this->_getDimensions();

            // create new image
            $this->_image->setDimensions($width, $height);
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

        $metas = array();

        $metas[] = new FaZend_UiModeller_Mockup_Meta_Text($this->_getImage());

        return $this->_metas = $metas;

    }

    /**
     * Calculate the size of the image
     *
     * @return array
     */
    public function _getDimensions() {

        $metas = $this->_getMetas();

        $total = count($metas);

        return array(800, 200 + $total * 100);

    }

}
