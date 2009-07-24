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
     * Image where thie table belongs
     *
     * @var FaZend_Image
     */
    protected $_image;
    
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
    public function __construct($name, FaZend_Image $image) {
        $this->_name = $name;
        $this->_image = $image;
    }

    /**
     * Simple access to properties
     *
     * @return var
     */
    public function __get($name) {

        if ($name === 'size')
            return count($this->_getInfo());

    }

    /**
     * Put the table onto the map
     *
     * @return void
     */
    public function draw($x, $y) {

        $this->_image->imagettftext(13, 0, $x, $y, $this->_image->getColor('table.title'), $this->_image->getFont('table.title'), $this->_name);
        $y += 3;

        $this->_image->imageline($x, $y, $x+strlen($this->_name)*10, $y, $this->_image->getColor('table.title'));

        $line = 1;
        foreach ($this->_getInfo() as $column) {
      
            $matches = array();
            preg_match('/^(\w+\s?(?:\(\d+\))?)/i', $column['DATA_TYPE'], $matches);

            $this->_image->imagettftext(10, 0, $x, $y + $line * 12, 
                $this->_image->getColor('table.column'), 
                $this->_image->getFont('table.column'), 
                $column['COLUMN_NAME'] . ': ' . str_replace(' ', '', $matches[1]));

            if (!empty($column['COMMENT'])) {
                $comments = explode("\n", wordwrap(cutLongLine($column['COMMENT'], 80), 30, "\n", true));

                foreach ($comments as $comment) {
                    $line++;

                    $this->_image->imagettftext(9, 0, $x+10, $y + $line * 12 - 1, 
                        $this->_image->getColor('table.comment'), 
                        $this->_image->getFont('table.comment'), 
                        $comment);
                }
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

}
