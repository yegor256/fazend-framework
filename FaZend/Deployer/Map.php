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
 * Deploy Db schema
 *
 * @package FaZend 
 */
class FaZend_Deployer_Map {

    /**
     * List of tables
     *
     * @var FaZend_Deployer_MapTable[]
     */
    protected $_tables;
    
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

        // intitial coordinates
        $angle = 180;
        $radius = $width * 0.25;

        $tables = $this->_getTables();

        if (count($tables)) {

            $angleDelta = 360/count($tables);
            $radiusDelta = ($width * 0.05) / count($tables);

            // put all tables onto it
            // going by a clock-rolling spiral
            foreach ($tables as $table) {

                // right part of the image should be moved right, a bit
                if (cos(deg2rad($angle)) > 0)
                    $scaleX = 1.5; 
                else 
                    $scaleX = 1;

                $x = round($width * 0.3 + $radius * cos(deg2rad($angle)) * $scaleX);
                $y = round($height * 0.3 + $radius * sin(deg2rad($angle)) * $height/$width);

                $table->draw($x, $y);

                $angle += $angleDelta;
                $radius += $radiusDelta;

            }

        } else {
            imagettftext($this->_getImage(), 12, 0, 0, 15, $this->_map->getColor('table.title'), $this->_map->getFont('table.title'), 'No tables found');
        }

        // return the PNG content
        ob_start();
        imagepng($this->getImage());
        return ob_get_clean();

    }

    /**
     * Get the color code
     *
     * @var int
     */
    public function getColor($mnemo) {

        $colors = array(
            'background' => 'ffffff',
            'border' => 'dddddd',
            'table.title' => '0055ff',
            'table.column' => '333333',
            'table.comment' => '777777',
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

        $total = count($this->_getTables());

        return array(round(200 + sqrt($total) * 250), 
            round(200 + sqrt($total) * 200));

    }

}
