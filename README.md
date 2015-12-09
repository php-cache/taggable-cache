# Taggable PSR-6 cache

This repository has one trait and two interfaces that makes a PSR-6 cache implementation taggable. 
Cache tags allow you to tag related items, and then clear all caches tagged with a given name. 

Note: Performance will be best with a driver such as memcached, which automatically purges stale records.

## Usage


To use an implementation of PSR-6 cache that also implement the `TaggablePoolInterface` do like the following code. 
We create three cache items and store them in the cache with different tags. The order of the tags does not matter. 

```php
use Doctrine\Common\Cache\MemcachedCache;
use namespace Cache\Doctrine\CachePool;

$doctrineCache = new MemcachedCache();
$psr6Cache = new CachePool($doctrineCache);

$item = $psr6Cache->getItem('tobias', ['developer', 'speaker']);
$item->set('foobar');
$psr6Cache->save($item);

$item = $psr6Cache->getItem('aaron', ['developer', 'nice guy']);
$item->set('foobar');
$psr6Cache->save($item);

$item = $psr6Cache->getItem('the king of Sweden', ['nice guy', 'king']);
$item->set('foobar');
$psr6Cache->save($item);
```

The following code shows how tags work:

```php
$psr6Cache->getItem('tobias', ['developer', 'speaker'])->isHit(); // true
$psr6Cache->getItem('tobias', ['speaker', 'developer'])->isHit(); // true
$psr6Cache->getItem('tobias', ['developer'])->isHit(); // false
```

To clear the cache you may do like this: 

```php

// Remove everything tagged with 'nice guy'
$psr6Cache->clear(['nice guy']);
$psr6Cache->getItem('tobias', ['developer', 'speaker'])->isHit(); // true
$psr6Cache->getItem('aaron', ['developer', 'nice guy'])->isHit(); // false
$psr6Cache->getItem('the king of Sweden', ['nice guy', 'king'])->isHit(); // false

// To clear everything you do as you usually do
$psr6Cache->clear();
```

## Implementation

If you are writing a PSR-6 implementation you may want to use this library. It is recommended that your object
implementing `CacheItemPoolInterface` also implements `TaggablePoolInterface`. That same object should also use
the `TaggablePoolTrait`. The trait has two protected methods; `generateCacheKey($key, array $tags)` and 
`flushTag($name)`. 

When implementing taggs, all the cache keys will change. We have to generate new cache keys that depends on the tags.
You need to do two changes on the implementation of `CacheItemPoolInterface`. First, you need to generate a new cache key
for all methods in `CacheItemPoolInterface` that not accepts a `CacheItemInterface`. You should of course use the 
`TaggablePoolTrait::generateCacheKey($key, array $tags)` function. Second, you need to implement a protected 
`CachePool::getTagItem($key)` function that does not generare a new cache key. This is used internally to store the tags.
