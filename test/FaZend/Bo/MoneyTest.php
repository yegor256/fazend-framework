<?php

require_once 'AbstractTestCase.php';

class FaZend_Bo_MoneyTest extends AbstractTestCase
{

    public function testWeCanCreateClassInDifferentWay()
    {
        foreach (array('1 USD', 333, '55EUR', -9.89, '+0') as $value) {
            $money = new FaZend_Bo_Money($value);
            $this->assertTrue($money instanceof FaZend_Bo_Money);
        }
    }

    public function testWeCanDoArithmeticOperations()
    {
        $money = new FaZend_Bo_Money('100 USD');
        $this->assertEquals(101, $money->add(1)->usd);
        $this->assertEquals(100, $money->add(new FaZend_Bo_Money('-1 USD'))->usd);
        
        $this->assertEquals(99, $money->sub('1 USD')->usd);
        $this->assertEquals(100, $money->sub(-1)->usd);
        
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

}

