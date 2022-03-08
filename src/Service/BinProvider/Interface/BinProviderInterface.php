<?php

declare(strict_types=1);

namespace App\Service\BinProvider\Interface;

use App\Service\BinProvider\ServiceInternal\BinInformation;

interface BinProviderInterface
{
    public function get(string $identity): BinInformation;
}
