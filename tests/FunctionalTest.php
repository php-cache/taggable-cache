<?php

namespace Cache\Taggable\Tests;

use Cache\Doctrine\CachePool;
use Doctrine\Common\Cache\MemcachedCache;

class FunctionalTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CachePool
     */
    private $cache;

    public function testBasicUsage()
    {
        $item = $this->cache->getItem('tobias', ['developer', 'speaker']);
        $item->set('foobar');
        $this->cache->save($item);

        $item = $this->cache->getItem('aaron', ['developer', 'nice guy']);
        $item->set('foobar');
        $this->cache->save($item);

        $item = $this->cache->getItem('the king of Sweden', ['nice guy', 'king']);
        $item->set('foobar');
        $this->cache->save($item);


        $this->assertTrue($this->cache->getItem('tobias', ['developer', 'speaker'])->isHit());
        $this->assertTrue($this->cache->getItem('tobias', ['speaker', 'developer'])->isHit());
        $this->assertFalse($this->cache->getItem('tobias', ['developer'])->isHit());


        // Remove everything tagged with 'nice guy'
        $this->cache->clear(['nice guy']);
        $this->assertTrue($this->cache->getItem('tobias', ['developer', 'speaker'])->isHit());
        $this->assertFalse($this->cache->getItem('aaron', ['developer', 'nice guy'])->isHit());
        $this->assertFalse($this->cache->getItem('the king of Sweden', ['nice guy', 'king'])->isHit());

        // To clear everything you do as you usually do
        $this->cache->clear();
        $this->assertFalse($this->cache->getItem('tobias', ['developer', 'speaker'])->isHit());
    }

    /**
     * Make sure we dont get conflicts with the tag key generation
     */
    public function testKeyGeneration()
    {
        $item1 = $this->cache->getItem('tobias', ['developer', 'speaker']);
        $item1->set('foobar');
        $this->cache->save($item1);

        $item2 = $this->cache->getItem('tag:speaker:key', []);
        $this->assertFalse($item2->isHit());
    }

    /**
     *
     * @return CachePool
     */
    public function setUp()
    {
        $memcached = new \Memcached();
        $memcached->addServer('localhost', 11211);
        $doctrineCache = new MemcachedCache();
        $doctrineCache->setMemcached($memcached);

        $this->cache = new CachePool($doctrineCache);
    }

    protected function tearDown()
    {
        $this->cache->clear();
    }
}