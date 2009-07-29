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

    const WIDTH = 200; // maximum width of one table, in pixels

    const TITLE_SIZE = 13; // table title font size
    const COLUMN_SIZE = 10; // column text font size
    const COMMENT_SIZE = 9; // comment font size

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

        if ($name === 'height')
            return $this->_getHeight();

    }

    /**
     * Format one column title
     *
     * @param array Column details
     * @return string Text
     */
    public static function formatColumnTitle($column) {

        $matches = array();
        preg_match('/^(\w+\s?(?:\(\d+\))?)/i', $column['DATA_TYPE'], $matches);

        return $column['COLUMN_NAME'] . ': ' . str_replace(' ', '', $matches[1]) .
            (!empty($column['FK']) ? ', FK(' . $column['FK_TABLE'] . '.' . $column['FK_COLUMN'] . ')' : false);
    }

    /**
     * Put the table onto the map
     *
     * @param int Horizontal axis of top left corner
     * @param int Vertical axis of top left corner
     * @return int Height in pixels
     */
    public function draw($x, $y) {

        $top = $y;
        $y += self::TITLE_SIZE;

        // table title
        $this->_image->imagettftext(self::TITLE_SIZE, 0, $x, $y, 
            $this->_image->getColor('table.title'), $this->_image->getFont('table.title'), $this->_name);

        $bbox = imagettfbbox(self::TITLE_SIZE, 0, $this->_image->getFont('table.title'), $this->_name);

        $this->_image->imageline($x, $y+1, $x+$bbox[4]+10, $y+1, $this->_image->getColor('table.title'));

        $y += 3 + self::TITLE_SIZE;

        foreach ($this->_getInfo() as $column) {
      
            $this->_image->imagettftext(self::COLUMN_SIZE, 0, $x, $y, 
                $this->_image->getColor('table.column'), 
                $this->_image->getFont('table.column'), 
                self::formatColumnTitle($column));

            $y += self::COLUMN_SIZE+2;

            if (!empty($column['COMMENT'])) {
                $comments = explode("\n", wordwrap(cutLongLine($column['COMMENT'], 80), 30, "\n", true));

                foreach ($comments as $comment) {
                    $this->_image->imagettftext(self::COMMENT_SIZE, 0, $x+10, $y, 
                        $this->_image->getColor('table.comment'), 
                        $this->_image->getFont('table.comment'), 
                        $comment);
                    $y += self::COMMENT_SIZE+2;
                }
                $y += 2;
            }

        }

        return $y - $top;

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
     * Height of the table, in pixels
     *
     * @return int
     */
    public function _getHeight() {
        
        // the image should NOT draw anything, just calculate the parameter
        $this->_image->disableDrawing();
        $height = $this->draw(0, 0);
        $this->_image->enableDrawing();

        return $height;

    }

}
