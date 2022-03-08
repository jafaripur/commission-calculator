<?php

declare(strict_types=1);

namespace App\Service\CurrencyProvider\Providers;

use App\Service\CurrencyProvider\ServiceInternal\CurrencyProviderAbstract;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
final class CurrencyFakeProvider extends CurrencyProviderAbstract
{
    public function __construct()
    {
        parent::__construct('USD');
    }

    public function load(bool $refresh = false): void
    {
        $this->rates = [
            'USD' => 1.0,
            'EUR' => 0.920276,
            'JPY' => 115.3689,
            'GBP' => 0.762562,
        ];
    }
}
