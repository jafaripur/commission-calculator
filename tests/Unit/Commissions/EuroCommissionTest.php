<?php

namespace App\Tests\Unit\Commissions;

use App\Commission\Commissions\EuroCommission;
use App\Service\BinProvider\Providers\BinFakerProvider;
use App\Service\CurrencyProvider\Providers\CurrencyFakeProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class EuroCommissionTest extends KernelTestCase
{
    private const DATA = [
        '45717360' => [
            'amount' => 100,
            'currency' => 'EUR',
            'result' => 1,
            'result_ceil' => 1,
        ],
        '516793' => [
            'amount' => '50',
            'currency' => 'USD',
            'result' => 0.460138,
            'result_ceil' => 0.47,
        ],
        '45417360' => [
            'amount' => 10000,
            'currency' => 'JPY',
            'result' => '1.5953623550194',
            'result_ceil' => 1.6,
        ],
        '41417360' => [
            'amount' => 130,
            'currency' => 'USD',
            'result' => 2.3927176,
            'result_ceil' => 2.4,
        ],
        '4745030' => [
            'amount' => 2000,
            'currency' => 'GBP',
            'result' => '48.272848633947',
            'result_ceil' => 48.28,
        ],
    ];

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
        $container = static::getContainer();

        $euRate = 0.01;
        $noneEuRate = 0.02;

        foreach (self::DATA as $bin => $item) {
            $commission = new EuroCommission(
                (string) $bin,
                (float) $item['amount'],
                (string) $item['currency'],
                $container->get(BinFakerProvider::class),
                $container->get(CurrencyFakeProvider::class),
                $euRate,
                $noneEuRate,
            );

            $valueCeil = $commission->calculate('EUR', true, 2);
            $value = $commission->calculate('EUR', false);

            $this->assertEquals((float) $item['result'], (float) $value);
            $this->assertEquals((float) $item['result_ceil'], (float) $valueCeil);
        }
    }
}
