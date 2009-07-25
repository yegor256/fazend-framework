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

    const FONT_SIZE = 12; // font size to use

    /**
     * Convert to HTML
     *
     * @param Zend_View Current view
     * @return string HTML image of the element
     */
    public function html(Zend_View $view) {

        $html = '<p><div style="display: inline-block; background:' . FaZend_Image::getCssColor ('mockup.table.grid') . ';"><table cellpadding="0" cellspacing="1">';

        $columns = $this->_getOptions('/^column.*/');

        // draw table header
        $html .= '<tr>';
        foreach ($columns as $details) {
            $html .= '<th>' . $details['title'] . '</th>';
        }
        $html .= '</tr>';

        for ($i=0; $i<=$this->totalLines; $i++) {

            $html .= '<tr>';
            foreach ($columns as $details) {

                if (is_array($details['mask']))
                    $txt = $details['mask'][array_rand($details['mask'])];
                else
                    $txt = $this->_parse($details['mask']);

                $html .= '<td>' . $txt . '</td>';
            }
            $html .= '</tr>';

        }

        $html .= '</table></div></p>';

        return $html;

    }

    /**
     * Draw 
     *
     * @return int Height
     */
    public function draw($top) {

        // calculate the height of one line of text
        $lineHeight = $this->_getLineHeight(self::FONT_SIZE, $this->_image->getFont('mockup.content'));

        $columns = $this->_getOptions('/^column.*/');
        $leftMargin = $x = FaZend_UiModeller_Mockup::INDENT;
        foreach ($columns as &$dets) {
            $dets['widthPixels'] = $dets['width'] * self::FONT_SIZE;
            $dets['x'] = $x;
            $x += $dets['widthPixels'];
        }

        // right border of the table
        $rightMargin = $x - 2;

        // start with top
        $y = $top;

        // draw table header
        foreach ($columns as $details) {

            // filled header
            $this->_image->imagefilledrectangle(
                $details['x'] - 2, $y, 
                $details['x'] + $details['widthPixels'] - 2, $y + $lineHeight, 
                $this->_image->getColor('mockup.table.header.background')); 

            $txt = $this->_parse($details['title']);
            $this->_image->imagettftext(self::FONT_SIZE, 0, 
                $details['x'], 
                $y + $lineHeight - 3, // because (x,y) in text is at the left-bottom corner
                $this->_image->getColor('mockup.table.header'), 
                $this->_image->getFont('mockup.content'), 
                $txt);
        }

        $y += $lineHeight;

        for ($i=0; $i<=$this->totalLines; $i++) {

            // horizontal line
            $this->_image->imageline($leftMargin, $y,
                $rightMargin, $y, $this->_image->getColor('mockup.table.grid')); 

            // just draw the bottom horizontal line
            if ($i == $this->totalLines)
                break;

            $height = 1; 
            foreach ($columns as $details) {

                if (is_array($details['mask']))
                    $txt = $details['mask'][array_rand($details['mask'])];
                else
                    $txt = $this->_parse($details['mask']);

                $bbox = imagettfbbox(self::FONT_SIZE, 0, $this->_image->getFont('mockup.content'), $txt);

                $scale = 1.1 * $bbox[4]/$details['widthPixels'];
                $txt = wordwrap($txt, strlen($txt) / $scale, "\n", true);

                $this->_image->imagettftext(self::FONT_SIZE, 0, 
                    $details['x'], 
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
        foreach ($columns as $details)
            $xs[] = $details['x'] - 2;
        $xs[] = end($xs) + $details['widthPixels'];
        foreach ($xs as $x)
            $this->_image->imageline($x, $top, $x, $y, $this->_image->getColor('mockup.table.grid')); 

        // return the height of the table 
        return $y;

    }

    /**
     * Add new column
     *
     * @return this
     */
    public function addColumn($name, $mask, $width) {
        $this->__set('column' . $name, array(
            'title'=>$name,
            'mask'=>$mask, 
            'width'=>$width));
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
