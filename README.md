# Taggable PSR-6 cache
[![Build Status](https://travis-ci.org/php-cache/taggable-cache.svg)](https://travis-ci.org/php-cache/taggable-cache)

This repository has one trait and one interfaces that makes a PSR-6 cache implementation taggable. Using tags allow you 
to tag related items, and then clear the cached data for that tag only.

*Note: Performance will be best with a driver such as memcached, which automatically purges stale records.*

## Usage

To use an implementation of PSR-6 cache that also implement the `TaggablePoolInterface` do like the following code. 
We create three cache items and store them in the cache with different tags. The order of the tags does not matter. 

```php
// $pool is an PSR-6 cache that implements TaggablePoolInterface

$item = $pool->getItem('tobias', ['developer', 'speaker']);
$item->set('foobar');
$pool->save($item);

$item = $pool->getItem('aaron', ['developer', 'nice guy']);
$item->set('foobar');
$pool->save($item);

$item = $pool->getItem('the king of Sweden', ['nice guy', 'king']);
$item->set('foobar');
$pool->save($item);
```

The following code shows how tags work:

```php
$pool->getItem('tobias', ['developer', 'speaker'])->isHit(); // true
$pool->getItem('tobias', ['speaker', 'developer'])->isHit(); // true
$pool->getItem('tobias', ['developer'])->isHit(); // false
$pool->getItem('tobias', ['king'])->isHit(); // false
$pool->getItem('tobias')->isHit(); // false
```

To clear the cache you may do like this: 

```php

// Remove everything tagged with 'nice guy'
$pool->clear(['nice guy']);
$pool->getItem('tobias', ['developer', 'speaker'])->isHit(); // true
$pool->getItem('aaron', ['developer', 'nice guy'])->isHit(); // false
$pool->getItem('the king of Sweden', ['nice guy', 'king'])->isHit(); // false

// To clear everything you do as you usually do
$pool->clear();
```

## Implementation

If you are writing a PSR-6 implementation you may want to use this library. It is recommended that your object
implementing `CacheItemPoolInterface` also implements `TaggablePoolInterface`. That same object should also use
the `TaggablePoolTrait`. The trait has two protected methods; `generateCacheKey($key, array $tags)` and 
`flushTag($name)`. 

When implementing tags, all the cache keys will change because we have to generate new cache keys that depends on the 
tags. You need to do two changes on the implementation of `CacheItemPoolInterface`. First, when ever you have a key and 
an array of tags you need to generate a new cache key using the `TaggablePoolTrait::generateCacheKey($key, array $tags)` 
function. Second, you need to implement a protected `CachePool::getTagItem($key)` function that does not generare a 
new cache key. This is used internally to store the tags.
