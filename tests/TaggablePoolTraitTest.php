<?php

namespace Cache\Taggable\Tests;
use Cache\Taggable\Tests\Helper\CachePool;

class TaggablePoolTraitTest extends \PHPUnit_Framework_TestCase
{
    public function testGenerateCacheKey()
    {
        $cache = new CachePool();
        $inputKey = 'foobar';
        $tags = ['bar', 'biz'];

        $key1 = $cache->exposeGenerateCacheKey($inputKey, $tags);
        $key2 = $cache->exposeGenerateCacheKey($inputKey, $tags);
        $this->assertTrue($key1 === $key2, 'Same input should generate same cache keys');

        $key1 = $cache->exposeGenerateCacheKey($inputKey, ['abc', '123']);
        $key2 = $cache->exposeGenerateCacheKey($inputKey, ['123', 'abc']);
        $this->assertTrue($key1 === $key2, 'Order should not matter when generating cache keys');
    }
}