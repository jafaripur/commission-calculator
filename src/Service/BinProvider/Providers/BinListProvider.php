<?php

declare(strict_types=1);

namespace App\Service\BinProvider\Providers;

use App\Service\BinProvider\Interface\BinProviderInterface;
use App\Service\BinProvider\ServiceInternal\BinInformation;
use App\Service\HttpRequest\Interface\HttpRequestInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * https://binlist.net.
 */
final class BinListProvider implements BinProviderInterface
{
    private const HTT_CONTENT_TYPE_ACCEPT = 'application/json';
    private const CACHE_KEY = 'bin_list_provider';

    public function __construct(
        private HttpRequestInterface $http,
        private CacheInterface $cache,
        private string $endpoint,
        private bool $cacheEnabled = false,
        private string $cacheKey = self::CACHE_KEY,
        private int $cacheExpireSecond = 3600, // 1 Hour
    ) {
        if ($this->cacheEnabled && empty($this->cacheKey)) {
            $this->cacheKey = self::CACHE_KEY;
        }
    }

    public function get(string $identity): BinInformation
    {
        $data = $this->cacheEnabled ? $this->cache->get($this->cacheKey.md5($identity), function (ItemInterface $item) use ($identity) {
            return $this->makeRequest($identity, $item);
        }) : $this->makeRequest($identity);

        return (new BinInformation())
            ->setCountryCode($data['country']['alpha2'] ?? '')
            ->setCountryName($data['country']['name'] ?? '');
    }

    private function makeRequest(string $identity, ?ItemInterface $item = null): array
    {
        $result = $this->http->get(sprintf($this->endpoint, $identity), [
            'accept' => self::HTT_CONTENT_TYPE_ACCEPT,
        ]);

        if ($item) {
            $item->expiresAfter(\DateInterval::createFromDateString(sprintf('%d SECOND', $this->cacheExpireSecond)));
        }

        return json_decode($result->getBody(), true, 512, JSON_THROW_ON_ERROR);
    }
}
