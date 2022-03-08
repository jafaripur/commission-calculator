<?php

declare(strict_types=1);

namespace App\Service\CurrencyProvider\Providers;

use App\Service\CurrencyProvider\ServiceInternal\CurrencyProviderAbstract;
use App\Service\HttpRequest\Interface\HttpRequestInterface;
use Closure;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * https://currencyfreaks.com.
 */
final class CurrencyFreakProvider extends CurrencyProviderAbstract
{
    private const HTT_CONTENT_TYPE_ACCEPT = 'application/json';
    private const CACHE_KEY = 'currencies_freak_provider';

    public function __construct(
        private HttpRequestInterface $http,
        private CacheInterface $cache,
        private string $token,
        private string $endpoint,
        string $baseCurrency,
        private bool $cacheEnabled = false,
        private string $cacheKey = self::CACHE_KEY,
        private int $cacheExpireSecond = 43200, // 12 Hour
    ) {
        parent::__construct($baseCurrency);

        if ($this->cacheEnabled) {
            if (empty($this->cacheKey)) {
                $this->cacheKey = self::CACHE_KEY;
            }

            $this->cacheKey = md5($this->cacheKey.$this->token);
        }
    }

    public function load(bool $refresh = false): void
    {
        if (!$refresh && count($this->rates)) {
            return;
        }

        $data = $this->cacheEnabled ? $this->cache->get($this->cacheKey, Closure::fromCallable([$this, 'makeRequest'])) : $this->makeRequest();

        $base = $data['base'] ?? '';

        if ($this->baseCurrency != $base) {
            throw new \InvalidArgumentException(sprintf('API base currency is %s but provided is %s.', $base, $this->baseCurrency));
        }

        $this->rates = $data['rates'] ?? [];

        if (!count($this->rates)) {
            throw new \InvalidArgumentException('Currencies not loaded.');
        }
    }

    private function makeRequest(?ItemInterface $item = null): array
    {
        $result = $this->http->get(sprintf($this->endpoint, $this->token), [
            'accept' => self::HTT_CONTENT_TYPE_ACCEPT,
        ]);

        if ($item) {
            $item->expiresAfter(\DateInterval::createFromDateString(sprintf('%d SECOND', $this->cacheExpireSecond)));
        }

        return json_decode($result->getBody(), true, 512, JSON_THROW_ON_ERROR);
    }
}
