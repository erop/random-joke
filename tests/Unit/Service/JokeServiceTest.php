<?php

namespace App\Tests\Unit\Service;

use App\Exception\NoSuchCategoryException;
use App\Service\JokeService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\CacheInterface;

class JokeServiceTest extends TestCase
{
    public function testJokeServiceProvidesJokeCategories(): void
    {
        $cache = $this->createMock(CacheInterface::class);
        $responseBody = '{ "type": "success", "value": [ "explicit", "nerdy"] }';
        $serializer = $this->createMock(SerializerInterface::class);
        $serializer->expects($this->once())
            ->method('deserialize')
            ->with($responseBody)
            ->willReturn(json_decode($responseBody, false));
        $info = ['response_headers' => ['content-type' => ['application/json']]];
        $response = new MockResponse($responseBody, $info);
        $client = new MockHttpClient([$response]);
        $service = new JokeService($client, $serializer, $cache, 3600);
        $categories = $service->getUncachedCategories();
        $this->assertIsArray($categories);
        $this->assertEquals(['explicit', 'nerdy'], $categories);
    }

    public function testJokeServiceProvidesRandomJoke(): void
    {
        $cache = $this->createMock(CacheInterface::class);
        $responseBody = '{ "type": "success", "value": { "id": 456, "joke": "All browsers support the hex definitions #chuck and #norris for the colors black and blue.", "categories": ["nerdy"] } }';
        $serializer = $this->createMock(SerializerInterface::class);
        $serializer->expects($this->once())
            ->method('deserialize')
            ->with($responseBody)
            ->willReturn(json_decode($responseBody, false));
        $info = ['response_headers' => ['content-type' => ['application/json']]];
        $response = new MockResponse($responseBody, $info);
        $client = new MockHttpClient([$response]);
        $service = new JokeService($client, $serializer, $cache, 3600);
        $joke = $service->getJokeByCategory('nerdy');
        $this->assertEquals(
            'All browsers support the hex definitions #chuck and #norris for the colors black and blue.',
            $joke
        );
    }

    public function testInvalidJokeCategory(): void
    {
        $cache = $this->createMock(CacheInterface::class);
        $this->expectException(NoSuchCategoryException::class);
        $responseBody = '{ "type": "NoSuchCategoryException", "value": "No such categories=auto" }';
        $serializer = $this->createMock(SerializerInterface::class);
        $info = ['response_headers' => ['content-type' => ['application/json']]];
        $response = new MockResponse($responseBody, $info);
        $client = new MockHttpClient([$response]);
        $service = new JokeService($client, $serializer, $cache, 3600);
        $service->getJokeByCategory('non_existing_category');
    }


}
