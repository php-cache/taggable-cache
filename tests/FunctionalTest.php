<?php

namespace Cache\Taggable\Tests;

use Cache\Doctrine\CachePool;
use Cache\IntegrationTests\TaggableCachePoolTest;
use Doctrine\Common\Cache\MemcachedCache;

class FunctionalTest extends TaggableCachePoolTest
{
    function createCachePool()
    {
        $memcached = new \Memcached();
        $memcached->addServer('localhost', 11211);
        $doctrineCache = new MemcachedCache();
        $doctrineCache->setMemcached($memcached);

        return new CachePool($doctrineCache);
    }
}