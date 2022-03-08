<?php

declare(strict_types=1);

namespace App\Service\BinProvider\Providers;

use App\Service\BinProvider\Interface\BinProviderInterface;
use App\Service\BinProvider\ServiceInternal\BinInformation;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
final class BinFakerProvider implements BinProviderInterface
{
    private const BINS = [
        '45717360' => [
            'country' => 'DK',
            'country_name' => 'Denmark',
        ],
        '516793' => [
            'country' => 'LT',
            'country_name' => 'Lithuania',
        ],
        '45417360' => [
            'country' => 'JP',
            'country_name' => 'Japan',
        ],
        '41417360' => [
            'country' => 'US',
            'country_name' => 'United States of America',
        ],
        '4745030' => [
            'country' => 'GB',
            'country_name' => 'United Kingdom of Great Britain and Northern Ireland',
        ],
    ];

    /**
     * Undocumented function.
     */
    public function get(string $identity): BinInformation
    {
        return (new BinInformation())
            ->setCountryCode(self::BINS[$identity]['country'] ?? '')
            ->setCountryName(self::BINS[$identity]['country_name'] ?? '');
    }
}
