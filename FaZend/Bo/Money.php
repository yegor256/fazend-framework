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
class FaZend_Bo_Money
{

    /**
     * The value, in cents, in original currency (NOT in USD!)
     *
     * @var integer
     */
    protected $_cents;
    
    /**
     * Currency
     *
     * @var Zend_Currency
     */
    protected $_currency;

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
        return new FaZend_Bo_Money($value);
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
     * Set value
     *
     * @param string Text representation of the cost
     * @return void
     * @throws FaZend_Bo_Money_InvalidFormat
     */
    public function set($value)
    {
        $currency = 'USD';
        $value = (string)$value;
        
        if ($value && !is_numeric($value)) {
            if (!preg_match('/^([\-\+]?\d+(?:\.\d+)?)(?:\s?(\w{3}))?$/', str_replace(',', '', $value), $matches)) {
                FaZend_Exception::raise(
                    'FaZend_Bo_Money_InvalidFormat', 
                    "Invalid cost format: '{$value}'"
                );
            }
            $value = $matches[1];
            $currency = $matches[2];
        }
        
        // we should implement it properly
        $this->_currency = FaZend_Flyweight::factory('Zend_Currency', 'en_US', $currency)
            ->setFormat(array(
                'precision' => 2, // cents to show
                'display' => Zend_Currency::USE_SHORTNAME,
                'position' => Zend_Currency::RIGHT));
        $this->_cents = (int)($value * 100);
    }

    /**
     * Show this value as a string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->_currency->toCurrency($this->_cents / 100);
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
                return $this->_getCents() / 100;
            case 'cents':
                return $this->_getCents();
            default:
                FaZend_Exception::raise(
                    'FaZend_Bo_Money_UnknownProperty',
                    "Property {$name} is not found in " . get_class($this)
                );
        }
    }

    /**
     * Return the value in USD (cents)
     *
     * @return integer
     * @todo implement it properly, getting conversion rates somewhere
     */
    protected function _getCents()
    {
        return $this->_cents * $this->_getRate($this->_currency);
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
        $this->_cents += $money;
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
        $this->_cents -= $money;
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

        $this->_cents *= $money;
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
            return $this->_cents / $div;

        $this->_cents /= $money;
        return $this;
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
            return $this->_cents >= $money;
        return $this->_cents > $money;
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
            return $this->_cents <= $money;
        return $this->_cents < $money;
    }
    
    /**
     * Is it zero?
     *
     * @return boolean
     */
    public function isZero() 
    {
        return empty($this->_cents);
    }
    
    /**
     * Get conversion rate for the given currency
     *
     * @param Zend_Currency Currency to work with
     * @return float
     * @todo Implement through www.foxrate.org
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
                $money = $money->cents;
                break;
        
            case is_string($money):
                $money = self::factory($money)->cents;
                break;
        
            default:
                $money *= 100;
                break;
        }
    }

}
