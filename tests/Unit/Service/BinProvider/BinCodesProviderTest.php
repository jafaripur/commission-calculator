<?php

namespace App\Tests\Unit\Service\BinProvider;

use App\Service\BinProvider\Providers\BinCodesProvider;
use App\Service\BinProvider\ServiceInternal\BinInformation;
use App\Service\BinProvider\ServiceInternal\Exception\BinProviderErrorException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BinCodesProviderTest extends KernelTestCase
{
    private const BIN_JP = '45417360';
    private const BIN_GB = '4745030';

    public static function setUpBeforeClass(): void
    {
        self::bootKernel([
            // 'environment' => 'my_test_env',
            // 'debug' => true,
        ]);
    }

    protected function setUp(): void
    {
        $this->markTestSkipped('This provider has rate limit request!');
    }

    public function testBinCodesJp()
    {
        /**
         * @var BinCodesProvider
         */
        $bin = static::getContainer()->get(BinCodesProvider::class);

        $response = $bin->get(self::BIN_JP);

        $this->assertInstanceOf(BinInformation::class, $response);

        $this->assertEquals('JP', $response->getCountryCode());
        $this->assertEquals('JAPAN', $response->getCountryName());
    }

    public function testBinCodesGb()
    {
        /**
         * @var BinCodesProvider
         */
        $bin = static::getContainer()->get(BinCodesProvider::class);

        $response = $bin->get(self::BIN_GB);

        $this->assertInstanceOf(BinInformation::class, $response);

        $this->assertEquals('GB', $response->getCountryCode());
        $this->assertEquals('UNITED KINGDOM', $response->getCountryName());
    }

    public function testBinCodesNotFound()
    {
        /**
         * @var BinCodesProvider
         */
        $bin = static::getContainer()->get(BinCodesProvider::class);

        try {
            $bin->get('a');
        } catch (BinProviderErrorException $th) {
            $this->assertEquals(1011, $th->getCode());
        }
    }
}
