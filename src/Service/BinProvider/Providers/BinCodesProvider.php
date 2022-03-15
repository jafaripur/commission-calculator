<?php

declare(strict_types=1);

namespace App\Service\BinProvider\Providers;

use App\Service\BinProvider\Interface\BinProviderInterface;
use App\Service\BinProvider\ServiceInternal\BinInformation;
use App\Service\BinProvider\ServiceInternal\Exception\BinProviderErrorException;
use App\Service\HttpRequest\Interface\HttpRequestInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * https://bincodes.com.
 */
final class BinCodesProvider implements BinProviderInterface
{
    private const HTT_CONTENT_TYPE_ACCEPT = 'application/json';
    private const CACHE_KEY = 'bin_codes_provider';

    public function __construct(
        private HttpRequestInterface $http,
        private CacheInterface $cache,
        private string $token,
        private string $endpoint,
        private bool $cacheEnabled = false,
        private string $cacheKey = self::CACHE_KEY,
        private int $cacheExpireSecond = 3600, // 1 Hour
    ) {
        if ($this->cacheEnabled) {
            if (empty($this->cacheKey)) {
                $this->cacheKey = self::CACHE_KEY;
            }

            $this->cacheKey = md5($this->cacheKey.$this->token);
        }
    }

    public function get(string $identity): BinInformation
    {
        $identity = substr($identity, 0, 6);

        $data = $this->cacheEnabled ? $this->cache->get($this->cacheKey.md5($identity), function (ItemInterface $item) use ($identity) {
            return $this->makeRequest($identity, $item);
        }) : $this->makeRequest($identity);

        return (new BinInformation())
            ->setCountryCode($data['countrycode'] ?? '')
            ->setCountryName($data['country'] ?? '');
    }

    private function makeRequest(string $identity, ?ItemInterface $item = null): array
    {
        $result = $this->http->get(sprintf($this->endpoint, $this->token, $identity), [
            'accept' => self::HTT_CONTENT_TYPE_ACCEPT,
        ]);

        if ($item) {
            $item->expiresAfter(\DateInterval::createFromDateString(sprintf('%d SECOND', $this->cacheExpireSecond)));
        }

        $data = json_decode($result->getBody(), true, 512, JSON_THROW_ON_ERROR);

        if (isset($data['valid']) && 'false' == $data['valid']) {
            throw new BinProviderErrorException($data['message'] ?? '', $data['error'] ?? 0);
        }

        return $data;
    }
}
