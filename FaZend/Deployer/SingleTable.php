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
    const ENTITY_PADDING = 10;
    const ENTITIES_WIDTH = 400;

    const MAX_COMMENT_LENGTH = 100; // maximum length of one comment line

    const LINK_ASSOCIATION = 1;

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
     * @return void
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
                FaZend_Deployer_MapTable::formatColumnTitle($column));

            $y += FaZend_Deployer_MapTable::COLUMN_SIZE+2;

            if (!empty($column['COMMENT'])) {
                $this->_getImage()->imagettftext(FaZend_Deployer_MapTable::COMMENT_SIZE, 0, $x+10, $y, 
                    $this->_getImage()->getColor('table.comment'), 
                    $this->_getImage()->getFont('table.comment'), 
                    cutLongLine($column['COMMENT'], self::MAX_COMMENT_LENGTH));
                $y += FaZend_Deployer_MapTable::COMMENT_SIZE+2;
            }

        }

        // draw nice UML
        list($width, $height) = $this->_getDimensions();
        $this->_drawUML($width - self::ENTITIES_WIDTH - self::PADDING - 80, self::PADDING);

        // return the PNG content
        return $this->_getImage()->png();

    }

    /**
     * Draw UML relations
     *
     * @param int X-coordinate to draw
     * @param int Y-coordinate to draw
     * @return void
     */
    public function _drawUML($x, $y) {

        $entities = $this->_findEntities();

        // intitial coordinates
        $angle = 0; 
        $centerX = $x + self::ENTITIES_WIDTH/2;
        $centerY = $y + self::ENTITIES_WIDTH/2;
        $radius = $centerX - $x;

        // change angle and radius, but the clock-order circle
        $angleDelta = 360/count($entities);

        $this->_drawEntity($this->_name, $centerX, $centerY);

        foreach ($entities as $entity) {

            // calculate coordinates
            $x = round($centerX + $radius * cos(deg2rad($angle)));
            $y = round($centerY + $radius * sin(deg2rad($angle)));

            $this->_drawEntity($entity->name, $x, $y);

            $this->_drawLink($this->_name, $entity->name, self::LINK_ASSOCIATION);

            $angle += $angleDelta;

        }

    }
    
    /**
     * Cache
     *
     * @var array
     */
    private $_locations = array();

    /**
     * Draw one entity
     *
     * @param string Title
     * @param int X-coordinate
     * @param int Y-coordinate
     * @return void
     */
    public function _drawEntity($title, $x, $y) {

        $bbox = imagettfbbox(FaZend_Deployer_MapTable::TITLE_SIZE, 0, 
            $this->_getImage()->getFont('table.title'), 
            $title);

        $this->_getImage()->imagettftext(FaZend_Deployer_MapTable::TITLE_SIZE, 0, $x, $y + abs($bbox[5]), 
            $this->_getImage()->getColor('table.title'), 
            $this->_getImage()->getFont('table.title'), 
            $title);

        $x = $x - self::ENTITY_PADDING;
        $y = $y - self::ENTITY_PADDING;
        $width = abs($bbox[4]) + self::ENTITY_PADDING*2;
        $height = abs($bbox[5]) + self::ENTITY_PADDING*2;

        $this->_getImage()->imagerectangle($x, $y, 
            $x + $width, $y + $height,
            $this->_getImage()->getColor('table.title'));

        $this->_locations[$title] = FaZend_StdObject::create()
            ->set('x', $x)
            ->set('y', $y)
            ->set('width', $width)
            ->set('height', $height);

    }

    /**
     * Draw link between two entities
     *
     * @param string Source
     * @param string Destination
     * @param int Type of link
     * @return void
     */
    public function _drawLink($source, $destination, $type) {

        $src = $this->_locations[$source];
        $dest = $this->_locations[$destination];

        $this->_getImage()->imageline($src->x, $src->y,
            $dest->x, $dest->y, 
            $this->_getImage()->getColor('table.column'));

    }

    /**
     * Finds and returns list of entities
     *
     * @return array
     */
    public function _findEntities() {

        $entities = array();

        $columns = $this->_getInfo();
        foreach ($columns as $column) {

            if (empty($column['FK']))
                continue;

            $entities[] = FaZend_StdObject::create()
                ->set('entity', $column['FK_TABLE'])
                ->set('name', $column['COLUMN_NAME'])
                ->set('out', false);

        }

        foreach (FaZend_Deployer::getInstance()->getTables() as $table) {

            foreach (FaZend_Deployer::getInstance()->getTableInfo($table) as $column) {

                if (empty($column['FK']))
                    continue;

                // FK not to us
                if ($column['FK_TABLE'] !== $this->_name)
                    continue;

                $entities[] = FaZend_StdObject::create()
                    ->set('entity', $table)
                    ->set('name', $table)
                    ->set('out', true);

            }

        }

        return $entities;

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

        return array(
            self::PADDING * 2 + $width * FaZend_Deployer_MapTable::COLUMN_SIZE * 0.6 + self::ENTITIES_WIDTH, // width
            self::PADDING * 2 
                + max(count($info) * (FaZend_Deployer_MapTable::COLUMN_SIZE + FaZend_Deployer_MapTable::COMMENT_SIZE + 4)
                + FaZend_Deployer_MapTable::TITLE_SIZE + 3, self::ENTITIES_WIDTH) // height
        );

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
