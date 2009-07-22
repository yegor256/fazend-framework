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
     * Build PNG image
     *
     * @var string
     */
    public function png() {

        // get the size of the image
        list($width, $height) = $this->_getDimensions();

        // create an image
        $this->_image = imagecreatetruecolor($width, $height);

        // intitial coordinates
        $angle = 0;
        $radius = 50;

        // put all tables onto it
        // going by a clock-rolling spiral
        $tables = $this->_getTables();
        foreach ($tables as $table) {

            $x = round($width/2 + $radius * cos($angle));
            $y = round($height/2 + $radius * sin($angle));

            $table->draw($this->_image, $x, $y);

            $angle += 360/count($tables);
            $radius += ($width/2) / count($tables);

        }

        // return the PNG content
        ob_start();
        imagepng($this->_image);
        return ob_get_clean();

    }

    /**
     * Get the color code
     *
     * @var int
     */
    public function getColor($mnemo) {

        return imagecolorallocate($this->_image, 0xff, 0xff, 0xff);

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

        return $this->_tables;

    }

    /**
     * Calculate the size of the image
     *
     * @return array
     */
    public function _getDimensions() {

        return array(400, 400);

    }

}
