<?php

declare(strict_types=1);

namespace App\Service\HttpRequest\Interface;

use App\Service\HttpRequest\ServiceInternal\HttpResponse;

interface HttpRequestInterface
{
    public function get(string $url, array $headers = []): HttpResponse;
}
