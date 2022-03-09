<?php

namespace App\Tests\Unit\Service\CurrencyProvider;

use App\Service\CurrencyProvider\Providers\CurrencyFreakProvider;
use App\Service\CurrencyProvider\ServiceInternal\Exception\CurrencyRateNotFoundException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CurrencyFreakProviderTest extends KernelTestCase
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
         * @var CurrencyFreakProvider
         */
        $currency = static::getContainer()->get(CurrencyFreakProvider::class);
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

        $amountInJpy = $currency->convert(10000, 'JPY', 'GBP');
        $amountInUsd = $currency->convert($amountInJpy, 'GBP', 'USD');
        $amountInEur = $currency->convert($amountInUsd, 'USD', 'EUR');
        $amountInJpyLast = $currency->convert($amountInEur, 'EUR', 'JPY');
        
        $this->assertEquals(10000, $amountInJpyLast);
    }
}
