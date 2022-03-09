<?php

namespace App\Tests\Unit\Service\BinProvider;

use App\Service\BinProvider\Providers\BinFakerProvider;
use App\Service\BinProvider\ServiceInternal\BinInformation;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BinFakerProviderTest extends KernelTestCase
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

    public function up()
    {
    }

    public function testBinListJp()
    {
        /**
         * @var BinFakerProvider
         */
        $bin = static::getContainer()->get(BinFakerProvider::class);

        $response = $bin->get(self::BIN_JP);

        $this->assertInstanceOf(BinInformation::class, $response);

        $this->assertEquals('JP', $response->getCountryCode());
        $this->assertEquals('Japan', $response->getCountryName());
    }

    public function testBinListGb()
    {
        /**
         * @var BinFakerProvider
         */
        $bin = static::getContainer()->get(BinFakerProvider::class);

        $response = $bin->get(self::BIN_GB);

        $this->assertInstanceOf(BinInformation::class, $response);

        $this->assertEquals('GB', $response->getCountryCode());
        $this->assertEquals('United Kingdom of Great Britain and Northern Ireland', $response->getCountryName());
    }

    public function testBinListNotFound()
    {
        /**
         * @var BinFakerProvider
         */
        $bin = static::getContainer()->get(BinFakerProvider::class);

        try {
            $response = $bin->get('a');
        } catch (\InvalidArgumentException $th) {
            $this->assertStringContainsString('Country code can not be empty', $th->getMessage());
        }
    }
}
