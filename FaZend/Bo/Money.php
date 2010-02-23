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
 * Cost/money value holder
 *
 * Use it like this:
 *
 * <code>
 * $money = new FaZend_Bo_Money('23 EUR');
 * if ($money->usd > 50.56) doSmth();
 * $money->set('18 GBP'); // will do everything automatically
 * </code>
 *
 * @package Bo
 */
class FaZend_Bo_Money extends FaZend_Bo_Abstract
{
    
    const POINT_WEIGHT = 10000;

    /**
     * Default currency short name
     *
     * @var string
     */
    protected static $_defaultCurrency = 'USD';

    /**
     * The value, in points, in original currency (NOT in USD!)
     *
     * 100 points = 1 cent
     *
     * @var integer
     */
    protected $_points;
    
    /**
     * Currency
     *
     * @var Zend_Currency
     */
    protected $_currency;

    /**
     * Currency to set
     *
     * @param string|Zend_Currency The currency to use as default
     * @return void
     */
    public static function setDefaultCurrency($currency) 
    {
        if ($currency instanceof Zend_Currency)
            $currency = $currency->getShortName();
        self::$_defaultCurrency = strval($currency);
    }

    /**
     * Constructor
     *
     * @param string Text representation of the cost
     * @return void
     */
    public function __construct($value = false)
    {
        $this->set($value);
    }

    /**
     * Create class
     *
     * @param mixed Value
     * @return FaZend_Bo_Money
     */
    public static function factory($value)
    {
        return new self($value);
    }

    /**
     * Create object from given amount of cents
     *
     * @param integer Total amount of cents
     * @return FaZend_Bo_Money
     */
    public static function convertFromCents($cents) 
    {
        return self::factory($cents / 100);
    }

    /**
     * Create object from given amount of POINTS
     *
     * @param integer Total amount of points
     * @return FaZend_Bo_Money
     */
    public static function convertFromPoints($points) 
    {
        return self::factory($points / self::POINT_WEIGHT);
    }

    /**
     * Set value
     *
     * @param string Text representation of the cost
     * @return void
     * @throws FaZend_Bo_Money_InvalidFormat
     */
    public function set($value)
    {
        $currency = self::$_defaultCurrency;
        $value = (string)$value;
        
        if ($value && !is_numeric($value)) {
            // remove spaces and replace comas with dots
            $value = preg_replace(
                array('/\s+/', '/\,/'), 
                array('', '.'), 
                $value
            );

            // @todo this is UGLY, and we need to do it with regexp
            $slices = explode('.', $value);
            $value = implode('', array_slice($slices, 0, -1)) . '.' . $slices[count($slices)-1];
            
            // @todo Zend_Locale should be properly used 
            // bug(Zend_Locale::getTranslationList('currency'));
            if (preg_match('/[a-zA-Z]{3}/', $value, $matches)) {
                $currency = strtoupper($matches[0]);
            }
            
            // validate format
            if (!preg_match('/\d+(?:\.\d+)?/', $value, $matches)) {
                FaZend_Exception::raise(
                    'FaZend_Bo_Money_InvalidFormat', 
                    "Invalid cost format '{$value}', numeric literal not found"
                );
            }
            if (strpos($value, '-') !== false)
                $matches[0] = '-' . $matches[0];
            $value = $matches[0];
        }
        
        // we should implement it properly
        $this->_currency = 
        FaZend_Flyweight::factory('Zend_Currency', 'en_US', $currency)
        ->setFormat(
            array(
                'precision' => 2, // cents to show
                'display' => Zend_Currency::USE_SHORTNAME,
                'position' => Zend_Currency::RIGHT
            )
        );
        $this->_points = (int)($value * self::POINT_WEIGHT);
    }

    /**
     * Show this value as a string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->_currency->toCurrency($this->original);
    }

    /**
     * Getter dispatcher
     *
     * @param string Name of the property to get
     * @return mixed
     * @throws FaZend_Bo_Money_UnknownProperty
     */
    public function __get($name)
    {
        switch ($name) {
            case 'usd':
                return $this->_getPoints() / self::POINT_WEIGHT;
            case 'cents':
                return $this->_getPoints() / 100;
            case 'points':
                return $this->_getPoints();
            case 'original':
                return $this->_points / self::POINT_WEIGHT;
            case 'origCents':
                return $this->_points / 100;
            case 'origPoints':
                return $this->_points;
            case 'currency':
                return $this->_currency;
            default:
                FaZend_Exception::raise(
                    'FaZend_Bo_Money_UnknownProperty',
                    "Property {$name} is not found in " . get_class($this)
                );
        }
    }

    /**
     * Return the value in USD points
     *
     * @return integer
     * @todo implement it properly, getting conversion rates somewhere
     * @uses $_points
     */
    protected function _getPoints()
    {
        return $this->_points * $this->_getRate($this->_currency);
    }
    
    /**
     * Add new value to current one
     *
     * @param FaZend_Bo_Money The cost to add
     * @return $this
     */
    public function add($money = null)
    {
        $this->_normalize($money);
        $this->_points += $money;
        return $this;
    }

    /**
     * Deduct this value from current one
     *
     * @param FaZend_Bo_Money The cost to deduct
     * @return $this
     */
    public function sub($money = null)
    {
        $this->_normalize($money);
        $this->_points -= $money;
        return $this;
    }

    /**
     * Multiply current value by this new value
     *
     * @param integer Multiplier
     * @return $this
     * @throws FaZend_Bo_Money_InvalidMuliplication
     */
    public function mul($money = null)
    {
        if ($money instanceof FaZend_Bo_Money) {
            FaZend_Exception::raise(
                'FaZend_Bo_Money_InvalidMuliplication',
                "You can't multiply money to money"
            );
        }

        $this->_points *= $money;
        return $this;
    }

    /**
     * Divide it
     *
     * @param float|FaZend_Bo_Money Divider
     * @return $this|float
     * @throws FaZend_Bo_Money_DivisionByZero
     */
    public function div($money)
    {
        $div = $money;
        $this->_normalize($div);

        if ($div == 0) {
            FaZend_Exception::raise(
                'FaZend_Bo_Money_DivisionByZero',
                "You can't divide by zero"
            );
        }
        
        if ($money instanceof FaZend_Bo_Money)
            return $this->_points / $div;

        $this->_points /= $money;
        return $this;
    }
    
    /**
     * Inverse the sign of the amount, from PLUS to MINUS, and vice versa
     *
     * @return $this
     */
    public function inverse() 
    {
        $this->_points = -$this->_points;
        return $this;
    }
    
    /**
     * This value equals to the given one?
     *
     * @param FaZend_Bo_Money|mixed Another value
     * @return boolean
     */
    public function equalsTo($money) 
    {
        $this->_normalize($money);
        return $this->_points == $money;
    }

    /**
     * Current value is GREATER than provided one?
     *
     * @param FaZend_Bo_Money|mixed Another value
     * @return boolean
     */
    public function isGreater($money = null, $orEqual = false)
    {
        $this->_normalize($money);
        if ($orEqual)
            return $this->_points >= $money;
        return $this->_points > $money;
    }

    /**
     * Current value is LESS than provided one?
     *
     * @param FaZend_Bo_Money|mixed Another value
     * @return boolean
     */
    public function isLess($money = null, $orEqual = false)
    {
        $this->_normalize($money);
        if ($orEqual)
            return $this->_points <= $money;
        return $this->_points < $money;
    }
    
    /**
     * Is it zero?
     *
     * @return boolean
     */
    public function isZero() 
    {
        return empty($this->_points);
    }
    
    /**
     * Get conversion rate for the given currency
     *
     * @param Zend_Currency Currency to work with
     * @return float
     * @todo Implement through www.foxrate.org
     * @throws FaZend_Bo_Money_UnknownCurrency
     */
    protected function _getRate(Zend_Currency $currency)
    {
        $symbol = $currency->getShortName();
        
        switch ($symbol) {
            case 'USD':
                return 1;
            case 'EUR':
                return 1.48;
            case 'GBP':
                return 1.9;
            default:
                FaZend_Exception::raise(
                    'FaZend_Bo_Money_UnknownCurrency',
                    "Unknown currency symbol: '{$symbol}'"
                );
        }
    }
    
    /**
     * Normalize value to cents
     *
     * @param mixed
     * @return void
     */
    protected function _normalize(&$money) 
    {
        switch (true) {
            case is_null($money):
                $money = 0;
                break;
            
            case $money instanceof FaZend_Bo_Money:
                $money = $money->points;
                break;
        
            case is_string($money):
                $money = self::factory($money)->points;
                break;
        
            default:
                $money *= self::POINT_WEIGHT;
                break;
        }
    }

}
