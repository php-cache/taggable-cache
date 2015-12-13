<?php

namespace Cache\Taggable\Tests\Helper;

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

    /**
     * @var array
     */
    private $memoryCache;

    public function getItem($key, array $tags = array())
    {
        $taggedKey = $this->generateCacheKey($key, $tags);

        return $this->getTagItem($taggedKey);
    }

    protected function getTagItem($key)
    {
        if (isset($this->memoryCache[$key])) {
            $item = $this->memoryCache[$key];
        } else {
            $item = new CacheItem($key);
        }

        return $item;
    }


    public function save(CacheItemInterface $item)
    {
        $this->memoryCache[$item->getKey()]=$item;
        return true;
    }

    public function exposeGenerateCacheKey($key, array $tags)
    {
        return $this->generateCacheKey($key, $tags);
    }

    public function exposeFlushTag($name)
    {
        return $this->flushTag($name);
    }
}