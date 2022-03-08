<?php

namespace App\Tests\Unit\Service\HttpRequest;

use App\Service\HttpRequest\HttpRequestService;
use App\Service\HttpRequest\ServiceInternal\HttpResponse;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class HttpRequestServiceTest extends KernelTestCase
{
    private const ENDPOINT_GET = 'https://mocki.io/v1/6347ad2e-8470-43f7-b72d-b1860d565a91';

    public function testGet()
    {
        self::bootKernel([
            // 'environment' => 'my_test_env',
            // 'debug' => true,
        ]);

        /**
         * @var HttpRequestService
         */
        $http = static::getContainer()->get(HttpRequestService::class);

        $response = $http->get(self::ENDPOINT_GET);

        $this->assertInstanceOf(HttpResponse::class, $response);

        $data = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertEquals('test', $data['name']);
    }
}
