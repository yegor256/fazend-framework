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
    const WIDTH = 800;

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
     * View to show text into
     *
     * @var Zend_View
     */
    protected $_view;
    
    /**
     * Constructor
     *
     * @param string Name of the view script, like 'index/settings'
     * @return void
     */
    public function __construct($script) {
        $this->_script = $script;
    }

    /**
     * Build PNG image
     *
     * @return string PNG image
     */
    public function png() {

        // get the size of the image
        list($width, $height) = $this->_getDimensions();

        $title = $this->_script . '.phtml';

        $bbox = imagettfbbox(9, 0, $this->getImage()->getFont('mockup.title'), $title);
        $this->getImage()->imagettftext(9, 0, $width-$bbox[4] - 3, -$bbox[5], 
            $this->getImage()->getColor('mockup.title'), 
            $this->getImage()->getFont('mockup.title'), 
            $title);

        $this->_draw();

        // return the PNG content
        return $this->getImage()->png();

    }

    /**
     * Build HTML
     *
     * @param Zend_View Current view
     * @return string HTML of the page
     */
    public function html(Zend_View $view) {
        
        $this->_view = $view;
        
        $html = '';
        foreach ($this->_getMetas() as $meta) {
            $html .= $meta->html($view);
        }
        return $html;
    }

    /**
     * Get script
     *
     * @return string
     */
    public function getScript() {
        return $this->_script;
    }
                       
    /**
     * Get the instance of View
     *
     * @return Zend_View
     */
    public function getView() {
        return $this->_view;
    }
                       
    /**
     * Get the image
     *
     * @return FaZend_Image
     */
    public function getImage() {
                       
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
     * Get list of actors
     *
     * @return string[]
     */
    public function getActors() {
        $actors = array();

        foreach ($this->_getMetas() as $meta)
            if ($meta instanceof FaZend_UiModeller_Mockup_Meta_Actor)
                $actors[] = $meta->label;

        return $actors;
    }

    /**
     * Draw all metas and return total Height
     *
     * @return int
     */
    protected function _draw() {

        $y = self::INDENT;
        foreach ($this->_getMetas() as $meta) {

            $y += $meta->draw($y);

        }

        return $y;

    }

    /**
     * Get the list of metas in the page
     *
     * @return FaZend_UiModeller_Mockup_Meta_Interface[]
     */
    protected function _getMetas() {

        if (isset($this->_metas))
            return $this->_metas;

        $metas = array();

        $scriptFile = APPLICATION_PATH . '/views/scripts/' . $this->_script . '.phtml';

        if (file_exists($scriptFile)) {

            $html = preg_replace('/[\n\t\r\s]/', ' ', file_get_contents($scriptFile));

            $matches = array();
            preg_match_all('/\<\!\-\-\s?\@(\w+)\((.*?)\)(.*?)\-\-\>/', $html, $matches);
            foreach ($matches[0] as $id=>$match) {

                $className = 'FaZend_UiModeller_Mockup_Meta_' . ucfirst($matches[1][$id]);

                $meta = new $className($this);

                // set label if required
                if (!empty($matches[2][$id]))
                    $meta->setLabel(trim($matches[2][$id], '"\''));
                    
                // execute other calls
                if (!empty($matches[3][$id]))
                    eval('$meta ' . $matches[3][$id] . ';');
                    
                $metas[] = $meta;

            }

        }

        return $this->_metas = $metas;

    }

    /**
     * Calculate the size of the image
     *
     * @return array
     */
    protected function _getDimensions() {

        $this->getImage()->disableDrawing();
        $height = $this->_draw();
        $this->getImage()->enableDrawing();

        return array(self::WIDTH, self::INDENT + $height);

    }

}
