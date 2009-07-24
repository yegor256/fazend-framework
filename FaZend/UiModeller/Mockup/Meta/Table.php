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
 * Table or list of elements
 *
 * @package FaZend 
 */
class FaZend_UiModeller_Mockup_Meta_Table extends FaZend_UiModeller_Mockup_Meta_Abstract {

    const FONT_SIZE = 12;

    /**
     * Draw 
     *
     * @return int Height
     */
    public function draw($top) {

        // calculate the height of one line of text
        $lineHeight = $this->_getLineHeight(self::FONT_SIZE, $this->_image->getFont('mockup.content'));

        $columns = $this->_getOptions('/^column.*/');
        $x = 0;
        foreach ($columns as $column=>&$dets) {
            $dets['widthPixels'] = $dets['width'] * self::FONT_SIZE;
            $dets['x'] = $x;
            $x += $dets['widthPixels'];
        }

        $y = $top;

        for ($i=0; $i<=$this->totalLines; $i++) {

            // horizontal line
            $this->_image->imageline(FaZend_UiModeller_Mockup::INDENT, $y,
                FaZend_UiModeller_Mockup::WIDTH - FaZend_UiModeller_Mockup::INDENT, $y, $this->_image->getColor('mockup.table.grid')); 

            // just draw the bottom horizontal line
            if ($i == $this->totalLines)
                break;

            $height = 1; 
            foreach ($columns as $column=>$details) {

                $txt = $this->_parse($details['mask']);
                $bbox = imagettfbbox(self::FONT_SIZE, 0, $this->_image->getFont('mockup.content'), $txt);

                $scale = 1.1 * $bbox[4]/$details['widthPixels'];
                $txt = wordwrap($txt, strlen($txt) / $scale, "\n", true);

                $this->_image->imagettftext(self::FONT_SIZE, 0, 
                    FaZend_UiModeller_Mockup::INDENT + $details['x'], 
                    $y + $lineHeight, // because (x,y) in text is at the left-bottom corner
                    $this->_image->getColor('mockup.content'), 
                    $this->_image->getFont('mockup.content'), 
                    $txt);

                $height = max($height, substr_count($txt, "\n") + 1);
            }

            $y += $lineHeight * ($height + 0.5);

        }

        // draw verticals in grid
        $xs = array();
        foreach ($columns as $column=>$details)
            $xs[] = $details['x'] + FaZend_UiModeller_Mockup::INDENT - 2;
        $xs[] = end($xs) + $details['widthPixels'];

        if (end($xs) < FaZend_UiModeller_Mockup::WIDTH - FaZend_UiModeller_Mockup::INDENT) {
            array_pop($xs);
            $xs[] = FaZend_UiModeller_Mockup::WIDTH - FaZend_UiModeller_Mockup::INDENT;
        }
                      
        foreach ($xs as $x)
            $this->_image->imageline($x, $top, $x, $y, $this->_image->getColor('mockup.table.grid')); 
            
        return $y + $i * self::FONT_SIZE;

    }

    /**
     * Add new column
     *
     * @return this
     */
    public function addColumn($name, $mask, $width) {
        $this->__set('column' . $name, array('mask'=>$mask, 'width'=>$width));
        return $this;
    }

    /**
     * Add new option
     *
     * @return this
     */
    public function addOption($name, $link = false) {
        $this->__set('option' . $name, array('link'=>$link));
        return $this;
    }

}
