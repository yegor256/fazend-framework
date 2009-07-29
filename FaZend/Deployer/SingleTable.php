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
 * One single table visual drawer
 *
 * @package FaZend 
 */
class FaZend_Deployer_SingleTable {

    const PADDING = 30; // white space width around the table

    /**
     * Name of the table
     *
     * @var string
     */
    protected $_name;
    
    /**
     * The image we build
     *
     * @var FaZend_Image
     */
    protected $_image;
    
    /**
     * Constructor
     *
     * @param string Name of the table
     * @return void
     */
    public function __construct($name) {
        $this->_name = $name;
    }

    /**
     * Build PNG image
     *
     * @var string
     */
    public function png() {

        $x = self::PADDING;
        $y = self::PADDING + FaZend_Deployer_MapTable::TITLE_SIZE;

        // table title
        $this->_getImage()->imagettftext(FaZend_Deployer_MapTable::TITLE_SIZE, 0, $x, $y, 
            $this->_getImage()->getColor('table.title'), $this->_getImage()->getFont('table.title'), $this->_name);

        $bbox = imagettfbbox(FaZend_Deployer_MapTable::TITLE_SIZE, 0, $this->_getImage()->getFont('table.title'), $this->_name);

        $this->_getImage()->imageline($x, $y+1, $x+$bbox[4]+10, $y+1, $this->_getImage()->getColor('table.title'));

        $y += 3 + FaZend_Deployer_MapTable::TITLE_SIZE;

        foreach ($this->_getInfo() as $column) {
      
            $matches = array();
            preg_match('/^(\w+\s?(?:\(\d+\))?)/i', $column['DATA_TYPE'], $matches);

            $this->_getImage()->imagettftext(FaZend_Deployer_MapTable::COLUMN_SIZE, 0, $x, $y, 
                $this->_getImage()->getColor('table.column'), 
                $this->_getImage()->getFont('table.column'), 
                $column['COLUMN_NAME'] . ': ' . str_replace(' ', '', $matches[1]));

            $y += FaZend_Deployer_MapTable::COLUMN_SIZE+2;

            if (!empty($column['COMMENT'])) {
                $this->_getImage()->imagettftext(FaZend_Deployer_MapTable::COMMENT_SIZE, 0, $x+10, $y, 
                    $this->_getImage()->getColor('table.comment'), 
                    $this->_getImage()->getFont('table.comment'), 
                    $column['COMMENT']);
                $y += FaZend_Deployer_MapTable::COMMENT_SIZE+2;
            }

        }

        // return the PNG content
        return $this->_getImage()->png();

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
     * Calculate the size of the image
     *
     * @return array
     */
    protected function _getDimensions() {

        $info = $this->_getInfo();

        $width = 0;
        foreach ($info as $column)
            $width = max($width, strlen($column['COMMENT']), strlen($column['COLUMN_NAME'] . ': ' . $column['DATA_TYPE']));

        return array(self::PADDING * 2 + $width * FaZend_Deployer_MapTable::COLUMN_SIZE * 0.6, 
            self::PADDING * 2 
                + count($info) * (FaZend_Deployer_MapTable::COLUMN_SIZE + FaZend_Deployer_MapTable::COMMENT_SIZE + 4)
                + FaZend_Deployer_MapTable::TITLE_SIZE + 3);

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
