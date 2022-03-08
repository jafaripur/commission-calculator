<?php

declare(strict_types=1);

namespace App\Service\CurrencyProvider\Interface;

interface CurrencyProviderInterface
{
    public function load(bool $refresh = false): void;

    public function getBaseCurrency(): ?string;

    public function getCurrencyRate(string $currency): float;

    public function convert(float $amount, string $fromCurrency, string $toCurrency): float;
}
