<?php

declare(strict_types=1);

namespace App\Commission\Interface;

use App\Service\BinProvider\Interface\BinProviderInterface;
use App\Service\CurrencyProvider\Interface\CurrencyProviderInterface;

interface CommissionInterface
{
    public function setBinProvider(BinProviderInterface $provider): self;

    public function setCurrencyProvider(CurrencyProviderInterface $provider): self;

    public function calculate(string $targetCurrency, bool $ceil = true, int $ceilPrecision = 2): float;
}
