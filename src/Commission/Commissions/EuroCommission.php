<?php

declare(strict_types=1);

namespace App\Commission\Commissions;

use App\Commission\Interface\CommissionInterface;
use App\Service\BinProvider\Interface\BinProviderInterface;
use App\Service\CurrencyProvider\Interface\CurrencyProviderInterface;

final class EuroCommission implements CommissionInterface
{
    private const EU = 0.01;
    private const NONE_EU = 0.02;
    private const EU_COUNTRY = [
        'AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GR', 'HR',
        'HU', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PO', 'PT', 'RO', 'SE', 'SI',
        'SK',
    ];

    public function __construct(
        private string $bin,
        private float $amount,
        private string $currency,
        private BinProviderInterface $binProvider,
        private CurrencyProviderInterface $currencyProvider,
        private float $euRate = self::EU,
        private float $noneEuRate = self::NONE_EU,
        bool $autoLoadCurrencyProvider = true
    ) {
        if ($euRate <= 0) {
            throw new \InvalidArgumentException(sprintf('Eu rate could not be less than , provided: %d', $euRate));
        }

        if ($noneEuRate <= 0) {
            throw new \InvalidArgumentException(sprintf('None-eu rate could not be less than 0, provided: %d', $noneEuRate));
        }

        if ($autoLoadCurrencyProvider) {
            $this->currencyProvider->load();
        }
    }

    public function setBinProvider(BinProviderInterface $provider): self
    {
        $new = clone $this;
        $new->binProvider = $provider;

        return $new;
    }

    public function setCurrencyProvider(CurrencyProviderInterface $provider): self
    {
        $new = clone $this;
        $new->currencyProvider = $provider;

        return $new;
    }

    public function calculate(string $targetCurrency, bool $ceil = true, int $ceilPrecision = 2): float
    {
        $binResult = $this->binProvider->get($this->bin);

        $value = $this->currencyProvider->convert($this->amount, $this->currency, $targetCurrency);
        $value = $value * ($this->isEuroCountry($binResult->getCountryCode()) ? $this->euRate : $this->noneEuRate);

        return $ceil ? $this->ceil($value, $ceilPrecision) : $value;
    }

    private function isEuroCountry(string $countryCode, bool $strict = false): bool
    {
        return in_array($countryCode, self::EU_COUNTRY, $strict);
    }

    private function ceil(float $value, int $precision): float
    {
        $mult = pow(10, $precision);

        return ceil($value * $mult) / $mult;
    }
}
