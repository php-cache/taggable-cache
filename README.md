# Taggable PSR-6 cache
[![Build Status](https://travis-ci.org/php-cache/taggable-cache.svg)](https://travis-ci.org/php-cache/taggable-cache) [![codecov.io](https://codecov.io/github/php-cache/taggable-cache/coverage.svg?branch=master)](https://codecov.io/github/php-cache/taggable-cache?branch=master)

This repository has one trait and one interfaces that makes a PSR-6 cache implementation taggable. Using tags allow you 
to tag related items, and then clear the cached data for that tag only.

*Note: Performance will be best with a driver such as memcached or redis, which automatically purges stale records.*

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

If you are writing a PSR-6 implementation you may want to use this library. The implementation is easy and will work
with all PSR-6 caches. 

**Warning: All the cache keys will change because we have to generate new cache keys that 
depends on the tags. This will include keys that do not use tags.**

You need to do three changes on the implementation of `CacheItemPoolInterface`. 

* Implement `TaggablePoolInterface` and use `TaggablePoolTrait`
* Use `TaggablePoolTrait::generateCacheKey($key, array $tags)` 
* Implement `CachePool::getTagItem($key)`


### Implement interface and use trait

The trait has two protected methods; `generateCacheKey($key, array $tags)` and `flushTag($name)`.

```php
class Pool implements CacheItemPoolInterface, TaggablePoolInterface
{
  use TaggablePoolTrait;
  
  // ...
}
```

### Generate cache key

Your cache pool's getItem probably look like this: 
```php
public function getItem($key)
{
  $item = $this->storage->fetch($key);
  if (false === $item || !$item instanceof CacheItemInterface) {
    $item = new CacheItem($key);
  }

  return $item;
}
```

You need to generate a new cache key that depends on the tags. 

```php
public function getItem($key, array $tags = [])
{
  $taggedKey = $this->generateCacheKey($key, $tags);
  $item = $this->storage->fetch($taggedKey);
  if (false === $item || !$item instanceof CacheItemInterface) {
    $item = new CacheItem($key);
  }

  return $item;
}
```

You need to do the same with all functions that takes a cache key as argument. 

```php
public function hasItem($key, array $tags = [])
{
  $taggedKey = $this->generateCacheKey($key, $tags);
  return $this->storage->exists($taggedKey);
}
```

Here is the list of functions you need to change: 
 
* getItem
* getItems
* hasItem
* clear
* deleteItem
* deleteItems

### Implement CachePool::getTagItem($key)

The trait uses the cache as a key value store. The key is the tag name and the value is a random id created by 
`uniqid()`. The way to access the cache is by a `getTagItem($key)`. This function will be very similar to your 
`getItem($key, array $tags = [])`. The only difference is that the latter will call `generateCacheKey`. 

Consider your new `getItem($key, array $tags = [])`:

```php
public function getItem($key, array $tags = [])
{
  $taggedKey = $this->generateCacheKey($key, $tags);
  $item = $this->storage->fetch($taggedKey);
  if (false === $item || !$item instanceof CacheItemInterface) {
    $item = new CacheItem($key);
  }

  return $item;
}
```

You would need `getTagItem($key)` to look like this: 
```php
public function getTagItem($key)
{
  $item = $this->storage->fetch($key);
  if (false === $item || !$item instanceof CacheItemInterface) {
    $item = new CacheItem($key);
  }

  return $item;
}
```

You could refactor it so the final result will look like this: 
```php
public function getItem($key, array $tags = [])
{
  $taggedKey = $this->generateCacheKey($key, $tags);
  
  return $this->getTagItem($taggedKey);
}

public function getTagItem($key)
{
  $item = $this->storage->fetch($key);
  if (false === $item || !$item instanceof CacheItemInterface) {
    $item = new CacheItem($key);
  }

  return $item;
}
```


### Deleting tags

When you want to delete all keys with a specific tag you use the `TaggablePoolTrait::flushTag($name)`.

```php
public function clear(array $tags = [])
{
  if (empty($tags)) {
    // Flush everything
    return $this->storage->flush();
  }
  
  foreach ($tags as $tag) {
    $this->flushTag($tag);
  }
 
  return true;  
}
```

The `TaggablePoolTrait::flushTag($name)` changes the tag cache key so next time you run 
`TaggablePoolTrait::generateCacheKey($key, array $tags)` you will get a different cache key back. This will not remove
the items from the cache, which introduce a memory leak. That is why it is important to use memcached or redis, which 
automatically purges stale records.
