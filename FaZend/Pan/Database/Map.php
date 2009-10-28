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
 * DB schema visual modeller
 *
 * @package Deployer
 */
class FaZend_Pan_Database_Map {

    const PADDING = 30; // white space width around the tables

    const CIRCLE_MODE_LIMIT = 6; // maximum amount of tables to show in circle

    const COLUMNS = 5; // max columns in spreadsheet mode

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

        $tables = $this->_getTables();
        
        if (count($tables) < self::CIRCLE_MODE_LIMIT) {

            $this->_drawCircle($tables);

        } elseif (count($tables) == 0) {

            // one system message instead of a picture
            $this->_getImage()->imagettftext(12, 0, 0, 15, 
                $this->_getImage()->getColor('error'), $this->_getImage()->getFont('table.title'), 
                'No tables found');

        } else {

            $this->_drawSpreadsheet($tables);

        }

        // return the PNG content
        return $this->_getImage()->png();

    }

    /**
     * Draw tables in spreadsheet mode
     *
     * @param FaZend_Deployer_MapTable[]
     * @return void
     */
    protected function _drawSpreadsheet($tables) {

        // allocate them in a spreadsheet mode
        $rowHeight = 0;
        $counter = 0;
        $y = self::PADDING;
        foreach ($tables as $table) {

            // calculate coordinates
            $x = round(self::PADDING + fmod($counter, self::COLUMNS) * FaZend_Pan_Database_MapTable::WIDTH);

            if (fmod($counter, self::COLUMNS) == 0) {
                $y += $rowHeight;
                $rowHeight = 0;
            }

            // draw one table
            $table->draw($x, $y);

            $rowHeight = max($rowHeight, $table->height);

            $counter++;

        }

    }

    /**
     * Draw tables in a circle mode
     *
     * @param FaZend_Deployer_MapTable[]
     * @return void
     */
    protected function _drawCircle($tables) {

        // get the size of the image
        list($width, $height) = $this->_getDimensions();

        // intitial coordinates
        $angle = 180; // start with left position
        $centerX = self::PADDING + ($width - self::PADDING*2 - FaZend_Pan_Database_MapTable::WIDTH)/2;
        $centerY = self::PADDING + 0.7 * ($height - self::PADDING*2)/2;
        $radiusX = $centerX - self::PADDING - 10;
        $radiusY = $centerY - self::PADDING - 10;

        // change angle and radius, but the clock-order circle
        $angleDelta = 360/count($tables);
        $radiusDelta = 0; //($width * 0.05) / count($tables);

        // put all tables onto it
        // going by a clock-rolling spiral
        foreach ($tables as $table) {

            // calculate coordinates
            $x = round($centerX + $radiusX * cos(deg2rad($angle)));
            $y = round($centerY + $radiusY * sin(deg2rad($angle)) * $height/$width);

            // draw one table
            $table->draw($x, $y);

            $angle += $angleDelta;
            $radius += $radiusDelta;

        }

    }

    /**
     * Get the image
     *
     * @return FaZend_Image
     */
    protected function _getImage() {

        if (!isset($this->_image)) {
            
            // create image
            $this->_image = new FaZend_Image();

            // get the size of the image
            list($width, $height) = $this->_getDimensions();

            // set dimensions
            $this->_image->setDimensions($width, $height);
        }

        return $this->_image;
    }

    /**
     * Get the list of tables in collection
     *
     * @return FaZend_Deployer_MapTable[]
     */
    protected function _getTables() {

        if (isset($this->_tables))
            return $this->_tables;

        $tables = array();

        foreach (FaZend_Deployer::getInstance()->getTables() as $table)
            $tables[] = new FaZend_Pan_Database_MapTable($table, $this->_getImage());

        // smaller tables come first
        usort($tables, create_function('$a, $b', 'return $a->size > $b->size;'));

        return $this->_tables = $tables;

    }

    /**
     * Calculate the size of the image
     *
     * @return array
     */
    protected function _getDimensions() {

        $tables = $this->_getTables();
        $total = count($tables);

        if ($total < self::CIRCLE_MODE_LIMIT) {

            $biggest = array_pop($tables);
            return array(
                round(self::PADDING*2 + $total * FaZend_Pan_Database_MapTable::WIDTH * 1), // width
                round(self::PADDING*2 + $biggest->height * 3) // height
            ); 

        } else {

            $height = 0;
            $counter = 0;
            foreach ($tables as $table) {
                if (fmod($counter, self::COLUMNS) == self::COLUMNS - 1)
                    $height += $table->height;
                $counter++;
            }        
            if (fmod($counter, self::COLUMNS) != 0)
                $height += $table->height;

            return array(
                round(FaZend_Pan_Database_MapTable::WIDTH * self::COLUMNS + 2 * self::PADDING), // width
                round($height + 2 * self::PADDING) // height
            );
             
        }


    }

}
