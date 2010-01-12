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
 * One-page mockup builder
 *
 * @package UiModeller
 */
class FaZend_Pan_Ui_Mockup {

    const INDENT = 50; // border indentation (left, right, top and bottom)
    const WIDTH = 800; // width of the mockup, in pixels

    const TITLE_FONT_SIZE = 9; // font size of the mockup title, small text on top right corner

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
     * Build PNG image and return its content
     *
     * @return string PNG image
     */
    public function png() {
        // get the size of the image
        list($width, $height) = $this->_getDimensions();

        // title of the mockup to draw on top right corner
        $title = $this->_script . '.phtml';

        // calculate the width of the text
        list($textWidth, $textHeight) = FaZend_Image::getTextDimensions(
            $title,
            self::TITLE_FONT_SIZE,
            $this->getImage()->getFont('mockup.title'));
            
        $this->getImage()->imagettftext(
            self::TITLE_FONT_SIZE, 0, 
            $width - $textWidth - 3, -$textHeight, 
            $this->getImage()->getColor('mockup.title'), 
            $this->getImage()->getFont('mockup.title'), 
            $title);

        // draw all mockup's elements
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
        // save View to the class, for further use
        $this->_view = $view;
        
        // put all elements in HTML format to the page
        $html = '';
        foreach ($this->_getMetas() as $meta) {
            $html .= $meta->html();
        }

        // return the HTML
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
            if ($meta instanceof FaZend_Pan_Ui_Meta_Actor)
                $actors[] = $meta->label;

        return $actors;
    }

    /**
     * Draw all metas and return total Height
     *
     * @return int
     */
    protected function _draw() {
        // start from top
        $y = self::INDENT;

        // put all of them to the PNG, top-down
        foreach ($this->_getMetas() as $meta) {
            $y += $meta->draw($y);
        }

        // return the height of the image
        return $y;
    }

    /**
     * Get the list of metas in the page
     *
     * @return FaZend_Pan_Ui_Meta_Interface[]
     */
    protected function _getMetas() {
        if (isset($this->_metas))
            return $this->_metas;

        $metas = array();

        $scriptFile = APPLICATION_PATH . '/views/scripts/' . $this->_script . '.phtml';

        if (file_exists($scriptFile)) {

            $html = preg_replace('/[\n\t\r\s]/', ' ', file_get_contents($scriptFile));

            $matches = array();
            preg_match_all('/\<\!\-\-\s?\@(\w+)\(((?:\(.*?\)|.)*?)\)(.*?)\-\-\>/', $html, $matches);
            foreach ($matches[0] as $id=>$match) {

                $className = 'FaZend_Pan_Ui_Meta_' . ucfirst($matches[1][$id]);

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
     * @return array Dimensions of the image (width, height)
     */
    protected function _getDimensions() {
        // the image should NOT draw anything, just calculate the parameter
        $this->getImage()->disableDrawing();
        $height = $this->_draw();
        $this->getImage()->enableDrawing();

        // return the array of WIDTH and HEIGHT
        return array(self::WIDTH, self::INDENT + $height);
    }

}
