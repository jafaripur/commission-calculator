<?php

declare(strict_types=1);

namespace App\Service\CurrencyProvider\ServiceInternal\Exception;

class CurrencyRateNotFoundException extends \InvalidArgumentException implements \Throwable
{
}
