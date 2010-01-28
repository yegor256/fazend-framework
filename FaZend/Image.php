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
 * Image creator
 *
 * @package Image
 */
class FaZend_Image
{

    // brand colors of fazend, don't change them

    const BRAND_BLUE1 = '062B35'; // dark blue, almost black
    const BRAND_BLUE2 = '134558'; // dark blue, in logo
    const BRAND_BLUE3 = '2276A4'; // blue, in logo
    const BRAND_BLUE4 = '92BECE'; // light blue

    const BRAND_GRAY      = '6D6F71'; // gray
    const BRAND_LIGHTGRAY = 'BCBEC0'; // light gray

    const BRAND_RED = 'EE0000'; // just red
    const BRAND_GREEN = '00EE00'; // just green

    const UML_FILL = 'FFFFCC'; // fill UML elements
    const UML_BORDER = '9A0033'; // border of UML elements

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
    public function __construct()
    {
    }

    /**
     * Disable any drawing
     *
     * @return void
     */
    public function disableDrawing()
    {
        $this->_enabled = false;
    }

    /**
     * Enable any drawing
     *
     * @return void
     */
    public function enableDrawing()
    {
        $this->_enabled = true;
    }

    /**
     * Set dimensions
     *
     * @return void
     */
    public function setDimensions($width, $height)
    {
        // create an image
        $this->_image = imagecreatetruecolor($width, $height);
        
        // fill it
        $this->imagefill(0, 0, $this->getColor('background'));
        
        // draw border as rectangle
        $this->imagerectangle(0, 0, $width-1, $height-1, $this->getColor('border'));

        // label image
        $label = imagecreatefrompng(FAZEND_PATH . '/Image/images/label.png');

        // put the label onto the image
        $this->imagecopy(
            $label, 
            $width - imagesx($label) - 1, $height - imagesy($label) - 1, 
            0, 0, imagesx($label), imagesy($label)
        );
    }

    /**
     * Call forward to GD library
     *
     * @return void
     * @throws FaZend_Image_NoDimensionsSet
     */
    public function __call($method, $args)
    {
        if (!$this->_enabled)
            return;

        // sanity check
        if (!isset($this->_image)) {
            FaZend_Exception::raise(
                'FaZend_Image_NoDimensionsSet', 
                'First you should call FaZend_Image::setDimensions()'
            );
        }

        array_unshift($args, $this->_image);
        
        return call_user_func_array($method, $args);
    }

    /**
     * Build PNG image
     *
     * @var string Binary PNG
     */
    public function png()
    {
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
    public static function getCssColor($mnemo)
    {
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
    public function getColor($mnemo)
    {
        $color = self::getCssColor($mnemo);
        return $this->imagecolorallocate(
            hexdec('0x' . $color{1} . $color{2}), 
            hexdec('0x' . $color{3} . $color{4}), 
            hexdec('0x' . $color{5} . $color{6})
        );
    }

    /**
     * Get the location of TTF font file
     *
     * @param string Mnemo code of the font
     * @var string
     * @todo Implement it properly
     */
    public static function getFont($mnemo)
    {
        return FAZEND_PATH . '/Image/fonts/arial.ttf';
    }
    
    /**
     * Get width and height of the text message
     *
     * @return array (width, height)
     **/
    public static function getTextDimensions($text, $fontSize = 10, $fontMnemo = 'default')
    {
        if (function_exists('imagettfbbox')) {
            $bbox = imagettfbbox($fontSize, 0, self::getFont($fontMnemo), $text);
            return array(abs($bbox[4]), abs($bbox[5]));
        }
        
        $width = 0;
        foreach (str_split($text) as $letter) {
            switch ($letter) {
                case 'l':
                case 'i':
                case 't':
                    $width += 0.7;
                    break;
                case 'm':
                case 'M':
                    $width += 1.7;
                    break;
                default: 
                    $width++;
                    break;
            }
            if ($letter >= 'A' && $letter <= 'Z')
                $width += 0.4;
        }
        return array($width * $fontSize, $fontSize);
    }

}
