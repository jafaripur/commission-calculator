<?php

declare(strict_types=1);

namespace App\Service\CurrencyProvider\ServiceInternal;

use App\Service\CurrencyProvider\Interface\CurrencyProviderInterface;
use App\Service\CurrencyProvider\ServiceInternal\Exception\CurrencyRateInvalidException;
use App\Service\CurrencyProvider\ServiceInternal\Exception\CurrencyRateNotFoundException;

abstract class CurrencyProviderAbstract implements CurrencyProviderInterface
{
    protected array $rates = [];

    public function __construct(
        protected string $baseCurrency
    ) {
    }

    public function getBaseCurrency(): ?string
    {
        return $this->baseCurrency;
    }

    public function getCurrencyRate(string $currency): float
    {
        if (!$rate = $this->rates[$currency] ?? false) {
            throw new CurrencyRateNotFoundException(sprintf('Currency not found: %s', $currency));
        }

        if ($rate <= 0) {
            throw new CurrencyRateInvalidException(sprintf('Currency rate is below zero: %s', (string) $rate));
        }

        return (float) $rate;
    }

    public function convert(float $amount, string $fromCurrency, string $toCurrency): float
    {
        if ($fromCurrency != $this->baseCurrency) {
            $amount = $this->convertValue($amount, $fromCurrency, $this->baseCurrency);
            $fromCurrency = $this->baseCurrency;
        }

        return $this->convertValue($amount, $fromCurrency, $toCurrency);
    }

    private function convertValue(float $amount, string $fromCurrency, string $toCurrency): float
    {
        if ($fromCurrency != $toCurrency) {
            $amount = ($amount * $this->getCurrencyRate($toCurrency)) / $this->getCurrencyRate($fromCurrency);
        }

        return $amount;
    }
}
