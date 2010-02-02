<?php

require_once 'AbstractTestCase.php';

class FaZend_Bo_MoneyTest extends AbstractTestCase
{
    
    public static function providerMonetaryStrings()
    {
        return array(
            array('gbp 6.765', 67650, 'GBP'),
            array('1 USD', 10000, 'USD'),
            array(333, 3330000, 'USD'),
            array("55\tEUR", 550000, 'EUR'),
            array(-9.89, -98900, 'USD'),
            array('-9 eur', -90000, 'EUR'),
            array(-0, 0, 'USD'),
            array(+0, 0, 'USD'),
            array('1,879.67 EUR', 18796700, 'EUR'),
            array('$7,500,000.00', 75000000000, 'USD'),
            array('$  10', 100000, 'USD'),
        );
    }

    /**
     * @dataProvider providerMonetaryStrings
     */
    public function testWeCanCreateClassInDifferentWay($value, $origPoints, $currency)
    {
        $money = new FaZend_Bo_Money($value);
        $this->assertTrue(
            $money instanceof FaZend_Bo_Money,
            "Format can't be parsed: '{$value}', why?"
        );
        $this->assertEquals(
            $origPoints, 
            $money->origPoints,
            "Invalid conversion of {$value}, why?"
        );
        $this->assertEquals(
            $currency, 
            $money->currency->getShortName(),
            "Invalid currency in {$value}, why?"
        );
    }

    public function testWeCanDoArithmeticOperations()
    {
        $money = new FaZend_Bo_Money('100 USD');
        $this->assertEquals(101, $money->add(1)->usd);
        $this->assertEquals(100, $money->add(new FaZend_Bo_Money('-1 USD'))->usd);
        
        $this->assertEquals(99, $money->sub('1 USD')->usd);
        $this->assertTrue($money->sub(-1)->equalsTo('100 USD'));
        
        $this->assertEquals(200, $money->mul(2)->usd);
        $this->assertEquals(40, $money->div(5)->usd);
        $this->assertEquals(2, $money->div(new FaZend_Bo_Money('20 USD')));

        $this->assertTrue($money->isGreater(10));
        $this->assertTrue($money->isLess(50));
    }

    public function testWeCanDoBasicCurrencyConversions()
    {
        $money = new FaZend_Bo_Money('100 EUR');
        $this->assertNotEquals($money->usd, $money->original);
        $this->assertNotEquals($money->cents, $money->origCents);
        $this->assertNotEquals($money->points, $money->origPoints);

        $money->add('20 GBP');

        $this->assertEquals('EUR', strval($money->currency->getShortName()));
    }

    /**
     * @expectedException FaZend_Bo_Money_InvalidMuliplication
     */
    public function testMoneyCantBeMultipliedByMoney()
    {
        $money = new FaZend_Bo_Money('100 USD');
        $money->mul(new FaZend_Bo_Money('100 USD'));
    }

    /**
     * @expectedException FaZend_Bo_Money_DivisionByZero
     */
    public function testMoneyCantBeDividedByZero()
    {
        $money = new FaZend_Bo_Money('100 USD');
        $money->div(0);
    }

    public function testSimpleMethodsWorkProperly()
    {
        $money = new FaZend_Bo_Money('100 USD');
        $money->inverse();
        $this->assertEquals(-100, $money->usd);
    }

}

