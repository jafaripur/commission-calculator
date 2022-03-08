<?php

declare(strict_types=1);

namespace App\Service\BinProvider\ServiceInternal;

final class BinInformation
{
    private string $countryCode = '';
    private string $countryName = '';

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function setCountryCode(string $name): self
    {
        if (empty($name)) {
            throw new \InvalidArgumentException('Country code can not be empty.');
        }

        $new = clone $this;
        $new->countryCode = $name;

        return $new;
    }

    public function getCountryName(): string
    {
        return $this->countryName;
    }

    public function setCountryName(string $name): self
    {
        $new = clone $this;
        $new->countryName = $name;

        return $new;
    }
}
