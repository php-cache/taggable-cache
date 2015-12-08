<?php

namespace Cache\Taggable\Tests\Helper;

use Cache\Doctrine\CacheItem;
use Cache\Taggable\TaggablePoolTrait;
use Psr\Cache\CacheItemInterface;

/**
 * A cache pool used in tests
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class CachePool
{
    use TaggablePoolTrait;

    private $cache;

    public function getItem($key)
    {
        $taggedKey = $this->generateCacheKey($key, []);

        $item = false;
        if (isset($this->cache[$taggedKey])) {
            $item = $this->cache[$taggedKey];
        }
        if (false === $item) {
            $item = new CacheItem($taggedKey, []);
        }

        return $item;
    }

    public function save(CacheItemInterface $item)
    {
        $this->cache[$item->getKey()]=$item;
        return true;
    }

    public function exposeGenerateCacheKey($key, array $tags)
    {
        return $this->generateCacheKey($key, $tags);
    }
}