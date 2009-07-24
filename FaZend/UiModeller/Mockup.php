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

    const INDENT = 50;

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

        $y = self::INDENT;
        foreach ($this->_getMetas() as $meta) {

            $y += $meta->draw($y);

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

        $html = preg_replace('/[\n\t\r\s]/', ' ',
            file_get_contents(APPLICATION_PATH . '/views/scripts/' . $this->_script . '.phtml'));

        $matches = array();
        preg_match_all('/\<\!\-\-\s?\@(\w+)\((.*?)\)(.*?)\-\-\>/', $html, $matches);
        foreach ($matches[0] as $id=>$match) {

            $className = 'FaZend_UiModeller_Mockup_Meta_' . ucfirst($matches[1][$id]);

            $meta = new $className($this->_getImage(), self::INDENT);

            if (!empty($matches[2][$id]))
                $meta->setLabel(trim($matches[2][$id], '"\''));
                
            $metas[] = $meta;

        }

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

        return array(800, 200 + $total * 70);

    }

}
