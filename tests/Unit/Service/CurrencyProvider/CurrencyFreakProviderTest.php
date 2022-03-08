<?php

namespace App\Tests\Unit\Service\CurrencyProvider;

use App\Service\CurrencyProvider\Providers\CurrencyFreakProvider;
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

    public function testCalculate()
    {
        /**
         * @var CurrencyFreakProvider
         */
        $currency = static::getContainer()->get(CurrencyFreakProvider::class);
        $currency->load();

        $this->assertNotNull($currency->getBaseCurrency());
    }
}
