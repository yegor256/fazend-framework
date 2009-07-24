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
     * @var int
     */
    protected $_image;
    
    /**
     * Get the image
     *
     * @var int
     */
    public function getImage() {

        if (!isset($this->_image)) {
        
            // get the size of the image
            list($width, $height) = $this->_getDimensions();

            // create an image
            $this->_image = imagecreatetruecolor($width, $height);
            
            // fill it
            imagefill($this->_image, 0, 0, $this->getColor('background'));
            
            // draw border as rectangle
            imagerectangle($this->_image, 0, 0, $width-1, $height-1, $this->getColor('border'));

            // label image
            $label = imagecreatefrompng(FAZEND_PATH . '/Deployer/images/label.png');

            // put the label onto the image
            imagecopy($this->_image, $label, 
                $width - imagesx($label) - 1, $height - imagesy($label) - 1, 
                0, 0, imagesx($label), imagesy($label));
        }

        return $this->_image;
    }

    /**
     * Build PNG image
     *
     * @var string
     */
    public function png() {

        // get the size of the image
        list($width, $height) = $this->_getDimensions();

        // return the PNG content
        ob_start();
        imagepng($this->getImage());
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

        return imagecolorallocate($this->getImage(), 
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

    /**
     * Get the list of tables in collection
     *
     * @return FaZend_Deployer_MapTable[]
     */
    public function _getTables() {

        if (isset($this->_tables))
            return $this->_tables;

        $this->_tables = array();

        foreach (FaZend_Deployer::getInstance()->getTables() as $table)
            $this->_tables[] = new FaZend_Deployer_MapTable($table, $this);

        // smaller tables come first
        usort($this->_tables, create_function('$a, $b', 'return $a->size > $b->size;'));

        return $this->_tables;

    }

    /**
     * Calculate the size of the image
     *
     * @return array
     */
    public function _getDimensions() {

        $tables = $this->_getTables();

        $total = count($tables);

        $biggest = array_pop($tables);

        return array(round(200 + sqrt($total) * 250), 
            round(200 + sqrt($total) * 110) + $biggest->size * 40);

    }

}
