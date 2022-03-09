<?php

namespace App\Tests\Unit\Service\CurrencyProvider;

use App\Service\CurrencyProvider\Providers\CurrencyFakeProvider;
use App\Service\CurrencyProvider\Providers\CurrencyFreakProvider;
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
         * @var CurrencyFreakProvider
         */
        $currency = static::getContainer()->get(CurrencyFreakProvider::class);
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
         * @var CurrencyFreakProvider
         */
        $currency = static::getContainer()->get(CurrencyFreakProvider::class);
        $currency->load();
        
        $this->assertEquals(100, $currency->convert(100, 'USD', 'USD'));
        $this->assertEquals(91.7481, $currency->convert(100, 'USD', 'EUR'));
        $this->assertEquals(11575.2, $currency->convert(100, 'USD', 'JPY'));
        $this->assertEquals(76.3266, $currency->convert(100, 'USD', 'GBP'));


        $this->assertEquals(0.6593976777939, $currency->convert(100, 'JPY', 'GBP'));
        $this->assertEquals(131.01592367536, $currency->convert(100, 'GBP', 'USD'));
        $this->assertEquals(15165.355197270675, $currency->convert(100, 'GBP', 'JPY'));
        $this->assertEquals(83.191477534685, $currency->convert(100, 'EUR', 'GBP'));
        
    }
}
