<?php
/**
 * FaZend Framework
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt. It is also available 
 * through the world-wide-web at this URL: http://www.fazend.com/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@fazend.com so we can send you a copy immediately.
 *
 * @copyright Copyright (c) FaZend.com
 * @version $Id$
 * @category FaZend
 */

/**
 * Table or list of elements
 *
 * @package UiModeller
 * @subpackage Mockup
 */
class FaZend_Pan_Ui_Meta_Table extends FaZend_Pan_Ui_Meta_Abstract
{

    const FONT_SIZE = 12; // font size to use

    const PAGINATOR = 'prev 1 2 3 4 5 6 7 ... next'; // text for paginator

    /**
     * Convert to HTML
     *
     * @return string HTML image of the element
     */
    public function html()
    {
        $html = '<p><div style="display: inline-block; background:' . 
        FaZend_Image::getCssColor('mockup.table.grid') . ';"><table cellpadding="0" cellspacing="1">';

        $columns = $this->_getOptions('/^column.*/');
        $options = $this->_getOptions('/^option.*/');

        // draw table header
        $html .= '<tr>';
        foreach ($columns as $details) {

            $txt = $details['title'];

            if (!empty($details['sorter']))
                $txt .= '<span style="font-weight:bold;cursor:pointer;" title="sort by column">&darr;</span>';

            $html .= '<th>' . $txt . '</th>';
        }
        if (count($options))
            $html .= '<th>options</th>';
        $html .= '</tr>';

        for ($i=0; $i<$this->totalLines; $i++) {

            $html .= '<tr>';
            foreach ($columns as $details) {
                $txt = self::parse($details['mask']);

                // put a link onto this field
                if (!empty($details['link']))
                    $txt = $this->_htmlLink($details['link'], $txt);

                $html .= '<td style="width:' . ($details['width'] * self::FONT_SIZE) . 'px">' . $txt . '</td>';
            }

            if (count($options)) {
            
                $hrefs = array();
                foreach ($options as $opt)
                    $hrefs[] = $this->_htmlLink($opt['link'], self::parse($opt['title']));
                $html .= '<td>' . implode(' | ', $hrefs) . '</td>';
            }

            $html .= '</tr>';

        }

        $html .= '</table></div>';

        if ($this->paginator) {
            $html .= '<br/><span style="cursor:pointer;" title="Pages">' . self::PAGINATOR . '</span>';
        }
        
        $html .= '</p>';

        return $html;
    }

    /**
     * Draw in PNG
     *
     * @return int Height
     */
    public function draw($top)
    {
        // calculate the height of one line of text
        $lineHeight = $this->_getLineHeight(self::FONT_SIZE, $this->_mockup->getImage()->getFont('mockup.content'));

        $columns = $this->_getOptions('/^column.*/');
        $leftMargin = $x = FaZend_Pan_Ui_Mockup::INDENT;
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
            $this->_mockup->getImage()->imagefilledrectangle(
                $details['x'] - 2, 
                $y, 
                $details['x'] + $details['widthPixels'] - 2, 
                $y + $lineHeight, 
                $this->_mockup->getImage()->getColor('mockup.table.header.background')
            ); 

            $txt = self::parse($details['title']);
            $this->_mockup->getImage()->imagettftext(
                self::FONT_SIZE, 0, 
                $details['x'], 
                $y + $lineHeight - 3, // because (x,y) in text is at the left-bottom corner
                $this->_mockup->getImage()->getColor('mockup.table.header'), 
                $this->_mockup->getImage()->getFont('mockup.content'), 
                $txt
            );
        }

        $y += $lineHeight;

        for ($i=0; $i<=$this->totalLines; $i++) {
            // horizontal line
            $this->_mockup->getImage()->imageline(
                $leftMargin, 
                $y,
                $rightMargin, 
                $y, 
                $this->_mockup->getImage()->getColor('mockup.table.grid')
            ); 

            // just draw the bottom horizontal line
            if ($i == $this->totalLines)
                break;

            $height = 1; 
            foreach ($columns as $details) {
                $txt = self::parse($details['mask']);

                // @todo we should replace it!
                $bbox = imagettfbbox(
                    self::FONT_SIZE, 
                    0, 
                    $this->_mockup->getImage()->getFont('mockup.content'), 
                    $txt
                );

                $scale = 1.1 * $bbox[4]/$details['widthPixels'];
                $txt = wordwrap($txt, strlen($txt) / $scale, "\n", true);

                $this->_mockup->getImage()->imagettftext(
                    self::FONT_SIZE, 
                    0, 
                    $details['x'], 
                    $y + $lineHeight, // because (x,y) in text is at the left-bottom corner
                    $this->_mockup->getImage()->getColor('mockup.content'), 
                    $this->_mockup->getImage()->getFont('mockup.content'), 
                    $txt
                );

                $height = max($height, substr_count($txt, "\n") + 1);
            }

            $y += $lineHeight * ($height + 0.5);

        }

        // draw verticals in grid
        $xs = array();
        foreach ($columns as $details)
            $xs[] = $details['x'] - 2;
        $xs[] = end($xs) + $details['widthPixels'];
        foreach ($xs as $x) {
            $this->_mockup->getImage()->imageline(
                $x, $top, 
                $x, $y, 
                $this->_mockup->getImage()->getColor('mockup.table.grid')
            ); 
        }

        if ($this->paginator) {
            $this->_mockup->getImage()->imagettftext(
                self::FONT_SIZE * 0.9, 0, 
                FaZend_Pan_Ui_Mockup::INDENT, $y + self::FONT_SIZE * 1.2, 
                $this->_mockup->getImage()->getColor('mockup.link'), 
                $this->_mockup->getImage()->getFont('mockup.content'), 
                self::PAGINATOR
            );
            $y += self::FONT_SIZE * 1.2;
        }

        // return the height of the table 
        return $y - $top + self::FONT_SIZE * 2;
    }

    /**
     * Add new column
     *
     * @param string Unique name of the column (to be visible as header)
     * @param string Mask to use for column content
     * @param integer Width of the column in letters
     * @return $this
     */
    public function addColumn($name, $mask, $width)
    {
        $this->__set(
            'column' . $name, 
            array(
                'title'=>$name,
                'mask'=>$mask, 
                'width'=>$width
            )
        );
        return $this;
    }

    /**
     * Add new option
     *
     * @param string Unique name of the link
     * @param string Name of the view script to link to
     * @param string Optional parameter of the link (visible for end-user)
     * @return $this
     */
    public function addOption($name, $link = false, $header = null)
    {
        $this->__set(
            'option' . $name, 
            array(
                'title' => (is_null($header) ? $name : $header),
                'link' => $link
            )
        );
        return $this;
    }

    /**
     * Add a link to column
     *
     * @param string Name of the column
     * @param string Optional name of the VIEW script to link to
     * @return $this
     */
    public function addLink($name, $link = false)
    {
        $details = $this->__get('column' . $name);
        $details['link'] = $link;
        $this->__set('column' . $name, $details);
        return $this;
    }

    /**
     * Add a sorter to column
     *
     * @param string Name of the column to become sortable
     * @return this
     */
    public function addSorter($name)
    {
        $details = $this->__get('column' . $name);
        $details['sorter'] = true;
        $this->__set('column' . $name, $details);
        return $this;
    }

}
