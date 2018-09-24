<?php

declare (strict_types = 1);

namespace app\tests;

use PHPUnit\Framework\TestCase;
use app\helpers\CurrencyHelper;

final class CurrencyHelperTest extends TestCase
{
    public function testCanFormatJPY()
    {
        $value = 152.3;
        $this->assertEquals('153', CurrencyHelper::format($value, 'JPY'));
    }

    public function testCanFormatEUR()
    {
        $value = 152.312;
        $this->assertEquals('152.32', CurrencyHelper::format($value, 'EUR'));
    }

    public function testCanFormatUSD()
    {
        $value = 152.312;
        $this->assertEquals('152.32', CurrencyHelper::format($value, 'USD'));
    }

    public function testCanConvertEURJPY()
    {
        $eur = 1;
        $jpy = 129.53;
        $this->assertEquals($jpy, CurrencyHelper::convert('EUR', 'JPY', $eur));
    }

    public function testCanConvertJPYEUR()
    {
        $eur = 1;
        $jpy = 129.53;
        $this->assertEquals($eur, CurrencyHelper::convert('JPY', 'EUR', $jpy));
    }

    public function testCanConvertEURUSD()
    {
        $eur = 1;
        $usd = 1.1497;
        $this->assertEquals($usd, CurrencyHelper::convert('EUR', 'USD', $eur));
    }

    public function testCanConvertUSDEUR()
    {
        $eur = 1;
        $usd = 1.1497;
        $this->assertEquals($eur, CurrencyHelper::convert('USD', 'EUR', $usd));
    }

    public function testCanConvertEUREUR()
    {
        $eur = 1;
        $this->assertEquals($eur, CurrencyHelper::convert('EUR', 'EUR', $eur));
    }

    public function testCanConvertUSDJPY()
    {
        $jpy = 129.53;
        $usd = 1.1497;
        $this->assertEquals($jpy, CurrencyHelper::convert('USD', 'JPY', $usd));
    }

    public function testCanConvertJPYUSD()
    {
        $jpy = 129.53;
        $usd = 1.1497;
        $this->assertEquals($usd, CurrencyHelper::convert('JPY', 'USD', $jpy));
    }

    public function testCanGetRates()
    {
        $config = $GLOBALS['config'];

        $this->assertEquals($config['rates'], CurrencyHelper::getRates());
    }
}
