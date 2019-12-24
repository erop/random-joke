<?php


namespace App\Service;


use App\Dto\CategoriesJokeResponse;
use App\Dto\MainJokeResponse;
use App\Exception\Http\WrongContentTypeException;
use App\Exception\NoSuchCategoryException;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class JokeService
{
    private const CATEGORIES_URL = 'http://api.icndb.com/categories';
    private const CATEGORIES_CACHE_KEY = 'joke_categories';
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
     * @var CacheInterface
     */
    private $cache;
    /**
     * @var int
     */
    private $jokeCategoriesTtl;

    /**
     * JokeService constructor.
     * @param HttpClientInterface $client
     * @param SerializerInterface $serializer
     * @param CacheInterface $cache
     * @param int $jokeCategoriesTtl
     */
    public function __construct(
        HttpClientInterface $client,
        SerializerInterface $serializer,
        CacheInterface $cache,
        int $jokeCategoriesTtl
    ) {
        $this->client = $client;
        $this->serializer = $serializer;
        $this->cache = $cache;
        $this->jokeCategoriesTtl = $jokeCategoriesTtl;
    }

    /**
     * @param string $category
     * @return string
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getJokeByCategory(string $category): string
    {
        $content = $this->makeRequest('GET', self::RANDOM_JOKE_URL . '?limitTo=' . $category);
        $checkObject = json_decode($content, false);
        if ($checkObject->type === 'NoSuchCategoryException') {
            $this->cache->delete(self::CATEGORIES_CACHE_KEY);
            throw new NoSuchCategoryException('No such category: ' . $category);
        }
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

    /**
     * @return array
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getCategories(): array
    {
        return $this->cache->get(
            self::CATEGORIES_CACHE_KEY,
            static function (ItemInterface $item) {
                $item->expiresAfter($this->jokeCategoriesTtl);
                return $this->getUncachedCategories();
            }
        );
    }

    /**
     * @return array
     */
    public function getUncachedCategories(): array
    {
        $object = $this->serializer->deserialize(
            $this->makeRequest('GET', self::CATEGORIES_URL),
            CategoriesJokeResponse::class,
            'json'
        );
        return $object->value;
    }

}
