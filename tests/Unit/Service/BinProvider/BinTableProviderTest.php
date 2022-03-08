<?php

namespace App\Tests\Unit\Service\BinProvider;

use App\Service\BinProvider\Providers\BinTableProvider;
use App\Service\BinProvider\ServiceInternal\BinInformation;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;

class BinTableProviderTest extends KernelTestCase
{
    private const BIN_JP = '45417360';
    private const BIN_GB = '4745030';

    protected function setUp(): void
    {
        $this->markTestSkipped('This provider some tme not work and is behind the cloudflare!');
    }

    public static function setUpBeforeClass(): void
    {
        self::bootKernel([
            // 'environment' => 'my_test_env',
            // 'debug' => true,
        ]);
    }

    public function up()
    {
    }

    public function testBinListJp()
    {
        /**
         * @var BinTableProvider
         */
        $bin = static::getContainer()->get(BinTableProvider::class);

        $response = $bin->get(self::BIN_JP);

        $this->assertInstanceOf(BinInformation::class, $response);

        $this->assertEquals('JP', $response->getCountryCode());
        $this->assertEquals('Japan', $response->getCountryName());
    }

    public function testBinListGb()
    {
        /**
         * @var BinTableProvider
         */
        $bin = static::getContainer()->get(BinTableProvider::class);

        $response = $bin->get(self::BIN_GB);

        $this->assertInstanceOf(BinInformation::class, $response);

        $this->assertEquals('GB', $response->getCountryCode());
        $this->assertEquals('United Kingdom of Great Britain and Northern Ireland', $response->getCountryName());
    }

    public function testBinListNotFound()
    {
        /**
         * @var BinTableProvider
         */
        $bin = static::getContainer()->get(BinTableProvider::class);

        try {
            $response = $bin->get('a');
        } catch (ClientExceptionInterface $th) {
            $this->assertEquals(404, $th->getCode());
        }
    }
}
