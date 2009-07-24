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
     * @var FaZend_Image
     */
    protected $_image;
    
    /**
     * Build PNG image
     *
     * @var string
     */
    public function png() {

        // get the size of the image
        list($width, $height) = $this->_getDimensions();

        // intitial coordinates
        $angle = 180; // start with left position
        $radius = $width * 0.25;

        $tables = $this->_getTables();

        if (count($tables)) {

            // change angle and radius, but the clock-order circle
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

                // calculate coordinates
                $x = round($width * 0.3 + $radius * cos(deg2rad($angle)) * $scaleX);
                $y = round($height * 0.3 + $radius * sin(deg2rad($angle)) * $height/$width);

                // draw one table
                $table->draw($x, $y);

                $angle += $angleDelta;
                $radius += $radiusDelta;

            }

        } else {

            // one system message instead of a picture
            $this->_getImage()->imagettftext(12, 0, 0, 15, 
                $this->_getImage()->getColor('error'), $this->_getImage()->getFont('table.title'), 
                'No tables found');

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
            // get the size of the image
            list($width, $height) = $this->_getDimensions();

            // create new image
            $this->_image = new FaZend_Image($width, $height);
        }

        return $this->_image;
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
            $this->_tables[] = new FaZend_Deployer_MapTable($table, $this->_getImage());

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
