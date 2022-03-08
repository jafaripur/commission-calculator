<?php

declare(strict_types=1);

namespace App\Service\HttpRequest\ServiceInternal;

class HttpResponse
{
    public function __construct(
        private string $body,
        private array $headers,
        private int $status,
    ) {
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getStatus(): int
    {
        return $this->status;
    }
}
