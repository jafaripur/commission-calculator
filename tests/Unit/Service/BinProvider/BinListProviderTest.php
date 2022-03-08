<?php

namespace App\Tests\Unit\Service\BinProvider;

use App\Service\BinProvider\Providers\BinListProvider;
use App\Service\BinProvider\ServiceInternal\BinInformation;
use App\Service\HttpRequest\ServiceInternal\Exception\HttpRequestErrorException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BinListProviderTest extends KernelTestCase
{
    private const BIN_JP = '45417360';
    private const BIN_GB = '4745030';
    private const BIN_NO_COUNTRY = '4541';

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::bootKernel([
            // 'environment' => 'my_test_env',
            // 'debug' => true,
        ]);
    }

    public function testBinListJp()
    {
        /**
         * @var BinListProvider
         */
        $bin = static::getContainer()->get(BinListProvider::class);

        $response = $bin->get(self::BIN_JP);

        $this->assertInstanceOf(BinInformation::class, $response);

        $this->assertEquals('JP', $response->getCountryCode());
        $this->assertEquals('Japan', $response->getCountryName());
    }

    public function testBinListGb()
    {
        /**
         * @var BinListProvider
         */
        $bin = static::getContainer()->get(BinListProvider::class);

        $response = $bin->get(self::BIN_GB);

        $this->assertInstanceOf(BinInformation::class, $response);

        $this->assertEquals('GB', $response->getCountryCode());
        $this->assertEquals('United Kingdom of Great Britain and Northern Ireland', $response->getCountryName());
    }

    public function testBinListNotFound()
    {
        /**
         * @var BinListProvider
         */
        $bin = static::getContainer()->get(BinListProvider::class);

        try {
            $response = $bin->get('a');
        } catch (HttpRequestErrorException $th) {
            $this->assertEquals(400, $th->getCode());
        }
    }

    public function testBinListNoCountry()
    {
        /**
         * @var BinListProvider
         */
        $bin = static::getContainer()->get(BinListProvider::class);

        try {
            $bin->get(self::BIN_NO_COUNTRY);
        } catch (\Throwable $th) {
            $this->assertInstanceOf(\InvalidArgumentException::class, $th);
            $this->assertStringContainsString('Country code can not be empty', $th->getMessage());
        }
    }
}
