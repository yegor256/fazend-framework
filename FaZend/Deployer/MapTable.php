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
    public function draw($img, $x, $y) {

        imagettftext($img, 10, 0, $x, $y, $this->_map->getColor('table.title'), $this->_map->getFont('table.title'), $this->_name);

    }

}
