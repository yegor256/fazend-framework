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
 * Mockup meta element
 *
 * @package UiModeller
 * @subpackage Mockup
 */
abstract class FaZend_Pan_Ui_Meta_Abstract implements FaZend_Pan_Ui_Meta_Interface {

    /**
     * List of options
     *
     * @var array
     */
    protected $_options = array();

    /**
     * Mockup, where this element is located
     *
     * @var FaZend_Pan_Ui_Mockup
     */
    protected $_mockup;
    
    /**
     * Initialize this class
     *
     * @return void
     */
    public function __construct(FaZend_Pan_Ui_Mockup $mockup) {
        $this->_mockup = $mockup;
    }

    /**
     * Setter
     *
     * @return this
     */
    public function __call($method, $args) {
        if (substr($method, 0, 3) == 'set') {
            $var = strtolower($method{3}) . (substr($method, 4));

            if (!count($args))
                $args = true;
            elseif (count($args) == 1)
                $args = current($args);

            $this->$var = $args;
        }
        return $this;
    }

    /**
     * Setter
     *
     * @return this
     */
    public function __set($var, $value) {
        $this->_options[$var] = $value;
    }

    /**
     * Getter
     *
     * @return this
     */
    public function __get($var) {
        if (!isset($this->_options[$var]))
            return false;
        return $this->_options[$var];
    }

    /**
     * Setter
     *
     * @return this
     */
    protected function _getOptions($preg = '//') {
        return array_intersect_key($this->_options, array_flip(preg_grep($preg, array_keys($this->_options))));
    }

    /**
     * Calculate the height of one line of text
     *
     * @return int
     */
    protected function _getLineHeight($fontSize, $fontFile) {
        list($width, ) = FaZend_Image::getTextDimensions(
            str_repeat("test\n", 10),
            $fontSize, 
            $fontFile);
        return $width/10;
    }

    /**
     * Parse text
     *
     * @param string|array|false Text to show
     * @return void
     */
    protected function _parse($txt) {
        if ($txt === false)
            $txt = '%s';
        elseif (is_array($txt)) 
            $txt = $txt[array_rand($txt)];

        // replace complex meta-s:
        // %name%, %email%, %url%, etc.
        if (preg_match_all('/%([a-z]+)%/', $txt, $matches)) {
            foreach ($matches[0] as $id=>$match) {
                switch ($matches[1][$id]) {
                    // random name of a person
                    case 'name':
                        $replacer = array_rand(array_flip(array(
                            'John Smith',
                            'Angela Johnson',
                            'Pamela Peterson',
                            'Vincenze Scavolini',
                            'Manuela Orlando',
                            )));
                        break;
                        
                    // random label (title, name of something, etc.)
                    case 'label':
                        $replacer = array_rand(array_flip(array(
                            'Alpha Green',
                            'Mega Star',
                            'World Extra',
                            'Edwards Universe',
                            'Ultra Senior',
                            )));
                        break;

                    // random email address
                    case 'email':
                        $replacer = array_rand(array_flip(array('john', 'pam', 'n', 't'))) . rand(100, 999) . '@example.com';
                        break;
                        
                    // name of the company
                    case 'company':
                        $replacer = array_rand(array_flip(array(
                            'John & John Ltd.',
                            'Vittorio Brothers Inc.',
                            'William & Sons, Co.',
                            )));
                        break;
                        
                    // date in the past
                    case 'date':
                    case 'pdate':
                        $replacer = Zend_Date::now()->subDay(rand(20, 100))->get(Zend_Date::DATE_MEDIUM);
                        break;
                        
                    // date in the future
                    case 'fdate':
                        $replacer = Zend_Date::now()->addDay(rand(20, 100))->get(Zend_Date::DATE_MEDIUM);
                        break;
                        
                    // country name
                    case 'country':
                        $replacer = array_rand(array_flip(array(
                            'United States',
                            'Germany',
                            'Canada',
                            'Switzerland',
                            'Russian Federation',
                            'Japan',
                            )));
                        break;

                    // random city name
                    case 'city':
                        $replacer = array_rand(array_flip(array(
                            'New York', 'San Francisco', 'Milan', 'Munich', 'Berlin', 'Toronto', 'Tokyo'
                            )));
                        break;

                    // random address
                    case 'address':
                        $replacer = array_rand(array_flip(array(
                            rand(1, 9) . 'th Street, ' . rand(10, 99),
                            rand(10, 99) . '/' . rand(1, 9) . ' Via Giuseppe Mercalli',
                            rand(10, 99) . ', ft.' . rand(1, 9) . ', ' . rand(1, 9) . 'th Avenue',
                            )));
                        break;

                    // random currency
                    // @todo replace it with Zend_Locale call
                    case 'currency':
                        $replacer = array_rand(array_flip(array('USD', 'EUR', 'GBP', 'CHF')));
                        break;

                    // random phone name
                    case 'phone':
                        $replacer = array_rand(array_flip(array(
                            '(' . rand(100, 999) . ') ' . rand(100, 999) . '-' . rand(1000, 9999),
                            '+' . rand(10, 99) . ' ' . rand(100, 999) . rand(100, 999) . '-' . rand(1000, 9999),
                            )));
                        break;

                    // random ZIP code
                    case 'zip':
                        $replacer = array_rand(array_flip(array(
                            rand(10000, 99999),
                            'SR' . rand(100, 999) . ' ' . rand(10, 99), 
                            )));
                        break;

                    // random SWIFT code
                    case 'swift':
                        $replacer = array_rand(array_flip(array(
                            'NYABABAB'
                            )));
                        break;

                    // random bank name
                    case 'bank':
                        $replacer = array_rand(array_flip(array(
                            'Bank Of New York', 'Bank Of America', 'ING', 'Bank Of Cyprus',
                            )));
                        break;

                    // random IBAN code
                    case 'iban':
                        $replacer = array_rand(array_flip(array(
                            'NY' . rand(10, 99) . 'ABCD' . rand(10000, 99999) . ' 0000000' . '12345678901',
                            )));
                        break;

                }
                $txt = str_replace($match, $replacer, $txt);
            }
        }
        
        if (preg_match_all('/%(\d+)?([sdf])/', $txt, $matches)) {
            $args = array();
            foreach ($matches[0] as $id=>$match) {
                switch ($matches[2][$id]) {
                    case 'd':
                        $pow = empty($matches[1][$id]) ? 2 : (int)$matches[1][$id];
                        $args[] = rand(pow(10, $pow-1), pow(10, $pow)-1);
                        break;

                    case 's':
                        $args[] = FaZend_View_Helper_LoremIpsum::getLoremIpsum($matches[1][$id]);
                        break;

                    case 'f':
                        $args[] = rand(0, 1);
                        break;
                }
            }

            $txt = vsprintf(str_replace('\n', "\n", $txt), $args);
        }

        return $txt;
    }

    /**
     * Build HTML link
     *
     * @param Zend_View Current view
     * @return string HTML image of the element
     */
    public function _htmlLink($script, $label) {
        // maybe it's a self link?
        if (!$script)
            $script = $this->_mockup->getScript();

        // broken link?
        if (!FaZend_Pan_Ui_Navigation::getInstance()->getAcl()->has($script))
            return '<span class="broken" title="Script ' . $script . ' is absent">' . $label . '</span>';

        return '<a href="' . $this->_mockup->getView()->url(array('action'=>'index', 'id'=>$script), 'ui', true, false). 
            '" title="' . $script . '">' . 
            $label. '</a>';
    }

}
