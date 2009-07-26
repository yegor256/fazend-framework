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
 * Image creator
 *
 * @package FaZend
 */
class FaZend_Image {

    // brand colors of fazend, don't change them

    const BRAND_BLUE1 = '062B35'; // dark blue, almost black
    const BRAND_BLUE2 = '134558'; // dark blue, in logo
    const BRAND_BLUE3 = '2276A4'; // blue, in logo
    const BRAND_BLUE4 = '92BECE'; // light blue

    const BRAND_GRAY      = '6D6F71'; // gray
    const BRAND_LIGHTGRAY = 'BCBEC0'; // light gray

    /**
     * Enable drawing
     *
     * @var boolean
     */
    protected $_enabled = true;

    /**
     * The image we build
     *
     * @var int
     */
    protected $_image;
    
    /**
     * Constructor
     *
     * @return void
     */
    public function __construct() {
    }

    /**
     * Disable any drawing
     *
     * @return void
     */
    public function disableDrawing() {
        $this->_enabled = false;
    }

    /**
     * Enable any drawing
     *
     * @return void
     */
    public function enableDrawing() {
        $this->_enabled = true;
    }

    /**
     * Set dimensions
     *
     * @return void
     */
    public function setDimensions($width, $height) {

        // create an image
        $this->_image = imagecreatetruecolor($width, $height);
        
        // fill it
        $this->imagefill(0, 0, $this->getColor('background'));
        
        // draw border as rectangle
        $this->imagerectangle(0, 0, $width-1, $height-1, $this->getColor('border'));

        // label image
        $label = imagecreatefrompng(FAZEND_PATH . '/Image/images/label.png');

        // put the label onto the image
        $this->imagecopy($label, 
            $width - imagesx($label) - 1, $height - imagesy($label) - 1, 
            0, 0, imagesx($label), imagesy($label));

    }

    /**
     * Call forward to GD library
     *
     * @return void
     */
    public function __call($method, $args) {

        if (!$this->_enabled)
            return;

        array_unshift($args, $this->_image);

        return call_user_func_array($method, $args);

    }

    /**
     * Build PNG image
     *
     * @var string
     */
    public function png() {

        // return the PNG content
        ob_start();
        imagepng($this->_image);
        return ob_get_clean();

    }

    /**
     * List of mnemo's and codes
     *
     * @var string[]
     */
    private static $_colors = array(
            'background' => 'ffffff', // white
            'border' => self::BRAND_LIGHTGRAY, // light gray
            'error' => 'ff0000', // red

            'table.title' => self::BRAND_BLUE3, // blue
            'table.column' => self::BRAND_GRAY, // gray
            'table.comment' => self::BRAND_LIGHTGRAY, // light gray

            'mockup.title' => self::BRAND_LIGHTGRAY, // name of the mockup script
            'mockup.content' => '333333', // texts in mockups
            'mockup.content.title' => '333333', // titles of pages in mockups

            'mockup.link' => self::BRAND_BLUE3, // AHREF links

            'mockup.button' => 'dddddd', // background of buttons
            'mockup.button.border' => self::BRAND_LIGHTGRAY, // borders of buttons

            'mockup.input' => 'ffffff', // background of inputs
            'mockup.input.border' => self::BRAND_GRAY, // borders of inputs
            'mockup.input.text' => self::BRAND_BLUE1, // texts in input fields

            'mockup.table.grid' => self::BRAND_LIGHTGRAY, // grids
            'mockup.table.header' => 'ffffff',
            'mockup.table.header.background' => self::BRAND_BLUE3,
        );

    /**
     * Get the color code for CSS
     *
     * @param string Mnemo code of the color
     * @return string
     */
    public static function getCssColor($mnemo) {
     
        if (!isset(self::$_colors[$mnemo]))
            $mnemo = 'error';
            	
        return '#' . self::$_colors[$mnemo];

    }

    /**
     * Get the color code
     *
     * @param string Mnemo code of the color
     * @return int
     */
    public function getColor($mnemo) {

        $color = self::getCssColor($mnemo);

        return $this->imagecolorallocate( 
            hexdec('0x' . $color{1} . $color{2}), 
            hexdec('0x' . $color{3} . $color{4}), 
            hexdec('0x' . $color{5} . $color{6}));

    }

    /**
     * Get the location of TTF font file
     *
     * @param string Mnemo code of the font
     * @var string
     */
    public function getFont($mnemo) {

        return FAZEND_PATH . '/Image/fonts/arial.ttf';

    }

}
