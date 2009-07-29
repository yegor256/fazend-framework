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
    const ENTITY_PADDING = 8;
    const ENTITIES_WIDTH = 300;

    const MAX_COMMENT_LENGTH = 100; // maximum length of one comment line

    const ARROW_SIZE = 10;

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
        $angle = 37; 
        $centerX = $x + self::ENTITIES_WIDTH/2;
        $centerY = $y + self::ENTITIES_WIDTH/2;
        $radius = $centerX - $x;

        // change angle and radius, but the clock-order circle
        $angleDelta = 360/count($entities);

        $center = FaZend_StdObject::create()
            ->set('title', $this->_name)
            ->set('name', $this->_name);
        $this->_drawEntity($center, $centerX, $centerY);

        foreach ($entities as $entity) {

            // calculate coordinates
            $x = round($centerX + $radius * cos(deg2rad($angle)));
            $y = round($centerY + $radius * sin(deg2rad($angle)));

            $this->_drawEntity($entity, $x, $y);

            $angle += $angleDelta;

        }

        foreach ($entities as $entity)
            $this->_drawLink($entity);

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
     * @param Entity
     * @return void
     */
    public function _drawEntity($entity, $x, $y) {

        $bbox = imagettfbbox(FaZend_Deployer_MapTable::TITLE_SIZE, 0, 
            $this->_getImage()->getFont('table.title'), 
            $entity->title);

        $this->_getImage()->imagettftext(FaZend_Deployer_MapTable::TITLE_SIZE, 0, $x, $y + abs($bbox[5]), 
            $this->_getImage()->getColor('table.title'), 
            $this->_getImage()->getFont('table.title'), 
            $entity->title);

        $x = $x - self::ENTITY_PADDING;
        $y = $y - self::ENTITY_PADDING;
        $width = abs($bbox[4]) + self::ENTITY_PADDING*2;
        $height = abs($bbox[5]) + self::ENTITY_PADDING*2;

        $this->_getImage()->imagerectangle($x, $y, 
            $x + $width, $y + $height,
            $this->_getImage()->getColor('table.title'));

        $this->_locations[$entity->title] = FaZend_StdObject::create()
            ->set('x', $x)
            ->set('y', $y)
            ->set('width', $width)
            ->set('height', $height);

    }

    /**
     * Draw link between two entities
     *
     * @param Entity
     * @return void
     */
    public function _drawLink($entity) {

        $center = $this->_locations[$this->_name];
        $leaf = $this->_locations[$entity->name];

        list($centerX, $centerY) = self::_calculateBorder(
            $center->x + $center->width/2, $center->y + $center->height/2, 
            $leaf->x + $leaf->width/2, $leaf->y + $leaf->height/2,
            $center->width, $center->height);

        list($leafX, $leafY) = self::_calculateBorder(
            $leaf->x + $leaf->width/2, $leaf->y + $leaf->height/2, 
            $center->x + $center->width/2, $center->y + $center->height/2, 
            $leaf->width, $leaf->height);

        $this->_getImage()->imageline($centerX, $centerY,
            $leafX, $leafY, 
            $this->_getImage()->getColor('table.column'));

        $polygon = 
            ($entity->out ? 
                array_merge(array($centerX, $centerY), self::_calculateArrow($leafX, $leafY, $centerX, $centerY)) :
                array_merge(array($leafX, $leafY), self::_calculateArrow($centerX, $centerY, $leafX, $leafY)));

        if ($entity->composition) {
            $this->_getImage()->imagefilledpolygon($polygon, 4,
                $this->_getImage()->getColor('table.column'));
        } else {
            $this->_getImage()->imagefilledpolygon($polygon, 4,
                $this->_getImage()->getColor('background'));
        }

        $this->_putMark($centerX, $centerY, $leafX, $leafY, $entity->leafText);
        $this->_putMark($leafX, $leafY, $centerX, $centerY, $entity->centerText);

    }

    /**
     * Put small mark close to the arrow
     *
     * @param int Source x
     * @param int Source y
     * @param int Dest x
     * @param int Dest y
     * @return void
     */
    public function _putMark($x1, $y1, $x2, $y2, $mark) {

        $x = (($x2 - $x1 <= 0) ? -1 : 0.3);
        $y = (($y2 - $y1 <= 0) ? 1.4 : -0.2);

        if (abs(($y2 - $y1)/($x2 - $x1)) < 0.5)
            $x = (($x2 - $x1 <= 0) ? 1 : -1);

        $bbox = imagettfbbox(FaZend_Deployer_MapTable::COMMENT_SIZE, 0, $this->_getImage()->getFont('table.column'), $mark);

        $this->_getImage()->imagettftext(FaZend_Deployer_MapTable::COMMENT_SIZE, 0, 
            $x2 + $x * abs($bbox[2]) + $x * FaZend_Deployer_MapTable::COMMENT_SIZE, 
            $y2 + $y * FaZend_Deployer_MapTable::COMMENT_SIZE, 
            $this->_getImage()->getColor('table.column'), 
            $this->_getImage()->getFont('table.column'), 
            $mark);

    }

    /**
     * Finds and returns list of entities
     *
     * @return array
     */
    public function _findEntities() {

        $tables = array();
        foreach (FaZend_Deployer::getInstance()->getTables() as $table)
            $tables[$table] = FaZend_Deployer::getInstance()->getTableInfo($table);
        
        $entities = array();

        $columns = $this->_getInfo();
        foreach ($columns as $column) {

            if (empty($column['FK']))
                continue;

//            $fk = $tables[$column['FK_TABLE']][$column['FK_COLUMN']];

            $entities[] = FaZend_StdObject::create()
                ->set('title', $column['FK_TABLE'])
                ->set('name', $column['COLUMN_NAME'])
                ->set('out', false)
                ->set('composition', !empty($column['FK_COMPOSITION']))
                ->set('leafText', empty($column['NULL']) ? '1' : '0..1')
                ->set('centerText', !empty($column['UNIQUE']) ? '1' : '*');

        }

        foreach ($tables as $table=>$info) {

            foreach ($info as $column) {

                if (empty($column['FK']))
                    continue;

                // FK not to us
                if ($column['FK_TABLE'] !== $this->_name)
                    continue;

                $entities[] = FaZend_StdObject::create()
                    ->set('title', $table)
                    ->set('name', $table)
                    ->set('out', true)
                    ->set('composition', !empty($column['FK_COMPOSITION']))
                    ->set('centerText', empty($column['NULL']) ? '1' : '0..1')
                    ->set('leafText', '*');

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

            // antialias
            $this->_image->imageantialias(true);
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

    /**
     * Calcualate coordidates of an arrow
     *
     * @return array
     */
    protected static function _calculateArrow($x1, $y1, $x2, $y2) {    

        $dX = $x1 - $x2;
        $dY = $y1 - $y2;

        $Z = sqrt($dX*$dX + $dY*$dY);

        $sinAlpha = $dX / $Z;
        $alpha = asin($sinAlpha);

        if ($dY < 0)
            $signY = -1;
        else
            $signY = 1;    

        return array(
            sin($alpha - pi()/6) * self::ARROW_SIZE + $x2, // x2
            cos($alpha - pi()/6) * self::ARROW_SIZE * $signY + $y2, // y2
            sin($alpha) * self::ARROW_SIZE * 2 + $x2, // x3
            cos($alpha) * self::ARROW_SIZE * 2 * $signY + $y2, // y3
            sin($alpha + pi()/6) * self::ARROW_SIZE + $x2, // x1
            cos($alpha + pi()/6) * self::ARROW_SIZE * $signY + $y2, // y1
        );

    }

    /**
     * Calcualate border
     *
     * @return array
     */
    protected static function _calculateBorder($x1, $y1, $x2, $y2, $width, $height) {
        $dY = abs($y1 - $y2);
        $dX = abs($x1 - $x2);

        $height = $height/2;
        $width = $width/2;

        $signX = ($x1 < $x2) ? 1 : -1;
        $signY = ($y1 < $y2) ? 1 : -1;

        if ($dY/$dX < $height/$width) {
            $x = $width * $signX;
            $y = $width * ($dY/$dX) * $signY;
        } else {
            $y = $height * $signY;
            $x = $height * ($dX/$dY) * $signX;
        }

        return array($x1 + $x, $y1 + $y);
    }

}
