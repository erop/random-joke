<?php


namespace App\Service;


use App\Dto\CategoriesJokeResponse;
use App\Dto\MainJokeResponse;
use App\Exception\Http\WrongContentTypeException;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class JokeService
{
    private const CATEGORIES_URL = 'http://api.icndb.com/categories';
    private const RANDOM_JOKE_URL = 'http://api.icndb.com/jokes/random';

    /**
     * @var HttpClientInterface
     */
    private $client;
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * JokeService constructor.
     * @param HttpClientInterface $client
     * @param SerializerInterface $serializer
     */
    public function __construct(HttpClientInterface $client, SerializerInterface $serializer)
    {
        $this->client = $client;
        $this->serializer = $serializer;
    }

    public function getJokeByCategory(string $category): string
    {
        $content = $this->makeRequest('GET', self::RANDOM_JOKE_URL . '?limitTo=' . $category);
        /** @var MainJokeResponse $object */
        $object = $this->serializer->deserialize($content, MainJokeResponse::class, 'json');
        return $object->value->joke;
    }

    private function makeRequest(string $method, string $url): string
    {
        try {
            $response = $this->client->request($method, $url);
        } catch (TransportExceptionInterface $e) {
            throw new InvalidArgumentException(sprintf('%s: Wrong options provided', __METHOD__));
        }
        try {
            if ('application/json' !== $response->getHeaders()['content-type'][0]) {
                throw new WrongContentTypeException(sprintf('%s: HTTP Response has wrong content type', __METHOD__));
            }
            return $response->getContent();
        } catch (ClientExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface | TransportExceptionInterface $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    public function getCategories(): array
    {
        /** @var CategoriesJokeResponse $object */
        $object = $this->serializer->deserialize(
            $this->makeRequest('GET', self::CATEGORIES_URL),
            CategoriesJokeResponse::class,
            'json'
        );
        return $object->value;
    }
}
