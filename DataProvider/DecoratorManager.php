<?php

namespace src\Decorator;

use DateTime;
use Exception;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use src\Integration\DataProvider;
use src\Integration\DataProviderInterface;

class DecoratorManager implements DataProviderInterface
{
    private const CACHE_LIFETIME = '+1 day';

    /**
     * @var DataProvider
     */
    private $dataProvider;

    /**
     * @var CacheItemPoolInterface
     */
    private $cache;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param DataProvider $dataProvider
     * @param CacheItemPoolInterface $cache
     * @param LoggerInterface $logger
     */
    public function __construct(DataProvider $dataProvider, CacheItemPoolInterface $cache, LoggerInterface $logger)
    {
        $this->dataProvider = $dataProvider;
        $this->cache = $cache;
        $this->logger = $logger;
    }

    /**
     * @param array $input
     * @return array
     */
    public function get(array $input): array
    {
        try {
            $cacheKey = $this->getCacheKey($input);
            $cacheItem = $this->cache->getItem($cacheKey);
            if ($cacheItem->isHit()) {
                return $cacheItem->get();
            }
            $result = $this->dataProvider->get($input);
            $cacheItem
                ->set($result)
                ->expiresAt(
                    (new DateTime())->modify(self::CACHE_LIFETIME)
                );

            return $result;
        } catch (Exception $e) {
            $this->logger->critical('Error', $e->getMessage());
        }

        return [];
    }

    /**
     * @param array $input
     * @return string
     */
    private function getCacheKey(array $input): string
    {
        return json_encode($input);
    }
}
