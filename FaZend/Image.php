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
 *
 */
class FaZend_Image {

    /**
     * The image we build
     *
     * @var int
     */
    protected $_image;
    
    /**
     * Build PNG image
     *
     * @var string
     */
    public function __construct($width, $height) {

        // create an image
        $this->_image = imagecreatetruecolor($width, $height);
        
        // fill it
        imagefill($this->_image, 0, 0, $this->getColor('background'));
        
        // draw border as rectangle
        imagerectangle($this->_image, 0, 0, $width-1, $height-1, $this->getColor('border'));

        // label image
        $label = imagecreatefrompng(FAZEND_PATH . '/Image/images/label.png');

        // put the label onto the image
        imagecopy($this->_image, $label, 
            $width - imagesx($label) - 1, $height - imagesy($label) - 1, 
            0, 0, imagesx($label), imagesy($label));

    }

    /**
     * Call forward to GD library
     *
     * @return void
     */
    public function __call($method, $args) {

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
     * Get the color code
     *
     * @param string Mnemo code of the color
     * @var int
     */
    public function getColor($mnemo) {

        $colors = array(
            'background' => 'ffffff', // white
            'border' => 'dddddd', // light gray
            'error' => 'ff0000', // red
            'table.title' => '0055ff', // blue
            'table.column' => '333333', // gray
            'table.comment' => '777777', // light gray
        );

        $color = $colors[$mnemo];

        return imagecolorallocate($this->_image, 
            hexdec('0x' . $color{0} . $color{1}), 
            hexdec('0x' . $color{2} . $color{3}), 
            hexdec('0x' . $color{4} . $color{5}));

    }

    /**
     * Get the location of TTF font file
     *
     * @param string Mnemo code of the font
     * @var string
     */
    public function getFont($mnemo) {

        return dirname(__FILE__) . '/fonts/arial.ttf';

    }

}
