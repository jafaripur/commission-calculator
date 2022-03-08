<?php

declare(strict_types=1);

namespace App\Service\HttpRequest;

use App\Service\HttpRequest\Interface\HttpRequestInterface;
use App\Service\HttpRequest\ServiceInternal\Exception\HttpRequestErrorException;
use App\Service\HttpRequest\ServiceInternal\HttpResponse;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HttpRequestService implements HttpRequestInterface
{
    public function __construct(
        private HttpClientInterface $http
    ) {
    }

    public function get(string $url, array $headers = []): HttpResponse
    {
        try {
            $response = $this->http->request(
                'GET',
                $url,
                [
                    'headers' => $headers,
                ]
            );

            return new HttpResponse(
                $response->getContent(),
                $response->getHeaders(),
                $response->getStatusCode()
            );
        } catch (ClientException|TransportException $th) {
            throw new HttpRequestErrorException(sprintf('Error on http request with code: %s', $th->getCode()), $th->getCode());
        }
    }
}
