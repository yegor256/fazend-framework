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
 * One table on the map
 *
 * @package FaZend 
 */
class FaZend_Deployer_MapTable {

    /**
     * Map where thie table belongs
     *
     * @var FaZend_Deployer_Map
     */
    protected $_map;
    
    /**
     * Name of the table, in db schema
     *
     * @var string
     */
    protected $_name;
    
    /**
     * Table info
     *
     * @var array[]
     */
    protected $_info;
    
    /**
     * Constructor
     *
     * @return void
     */
    public function __construct($name, FaZend_Deployer_Map $map) {
        $this->_name = $name;
        $this->_map = $map;
    }

    /**
     * Put the table onto the map
     *
     * @return void
     */
    public function draw($x, $y) {

        imagettftext($this->_getImage(), 12, 0, $x, $y, $this->_map->getColor('table.title'), $this->_map->getFont('table.title'), $this->_name);
        imageline($this->_getImage(), $x, $y+3, $x+strlen($this->_name)*10, $y+3, $this->_map->getColor('table.title'));

        $line = 1;
        $y += 3;
        foreach ($this->_getInfo() as $column) {
      
            $matches = array();
            preg_match('/^(\w+)/i', $column['DATA_TYPE'], $matches);

            imagettftext($this->_getImage(), 10, 0, $x, $y + $line * 12, 
                $this->_map->getColor('table.column'), 
                $this->_map->getFont('table.column'), 
                $column['COLUMN_NAME'] . ': ' . $matches[1]);

            if (!empty($column['COMMENT'])) {
                $line++;

                imagettftext($this->_getImage(), 9, 0, $x+10, $y + $line * 12 - 1, 
                    $this->_map->getColor('table.comment'), 
                    $this->_map->getFont('table.comment'), 
                    cutLongLine($column['COMMENT'], 50));
            }

            $line++;
        }

    }

    /**
     * Get info
     *
     * @return void
     */
    public function _getInfo() {

        if (!isset($this->_info))
            $this->_info = FaZend_Deployer::getInstance()->getTableInfo($this->_name);
        return $this->_info;

    }

    /**
     * Get image
     *
     * @return int
     */
    public function _getImage() {
        return $this->_map->getImage();
    }

}
