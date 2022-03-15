<?php

namespace App\Tests\Unit\Service\CurrencyProvider;

use App\Service\CurrencyProvider\Providers\CurrencyFakeProvider;
use App\Service\CurrencyProvider\ServiceInternal\Exception\CurrencyRateNotFoundException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CurrencyFakeProviderTest extends KernelTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::bootKernel([
            // 'environment' => 'my_test_env',
            'debug' => true,
        ]);
    }

    public function testLoad()
    {
        /**
         * @var CurrencyFakeProvider
         */
        $currency = static::getContainer()->get(CurrencyFakeProvider::class);
        $currency->load();

        $this->assertNotNull($currency->getBaseCurrency());
    }

    public function testGetCurrencyRate()
    {
        /**
         * @var CurrencyFakeProvider
         */
        $currency = static::getContainer()->get(CurrencyFakeProvider::class);
        $currency->load();

        $this->assertIsNumeric($currency->getCurrencyRate('EUR'));

        try {
            $currency->getCurrencyRate('EUR11');
        } catch (CurrencyRateNotFoundException $th) {
            $this->assertStringContainsString('Currency not found', $th->getMessage());
        }
    }

    public function testConvert()
    {
        /**
         * @var CurrencyFakeProvider
         */
        $currency = static::getContainer()->get(CurrencyFakeProvider::class);
        $currency->load();

        $this->assertEquals(100, $currency->convert(100, 'USD', 'USD'));
        $this->assertEquals(92.0276, $currency->convert(100, 'USD', 'EUR'));
        $this->assertEquals(11536.89, $currency->convert(100, 'USD', 'JPY'));
        $this->assertEquals(76.2562, $currency->convert(100, 'USD', 'GBP'));
        $this->assertEquals(0.66097709174656, $currency->convert(100, 'JPY', 'GBP'));
        $this->assertEquals(131.13687805057, $currency->convert(100, 'GBP', 'USD'));
        $this->assertEquals(15129.117370128592, $currency->convert(100, 'GBP', 'JPY'));
        $this->assertEquals(82.862315218478, $currency->convert(100, 'EUR', 'GBP'));
    }
}
