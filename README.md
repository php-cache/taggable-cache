# Taggable PSR-6 cache

This repository has one trait and two interfaces that makes a PSR-6 cache implementation taggable. 
Cache tags allow you to tag related items, and then clear all caches tagged with a given name. 

Note: Performance will be best with a driver such as memcached, which automatically purges stale records.

## Usage


To use an implementation of PSR-6 cache that also implement the `TaggablePoolInterface` do like the following code. 
We create three cache items and store them in the cache with different tags. The order of the tag matters. 

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
$psr6Cache->getItem('tobias', ['speaker', 'developer'])->isHit(); // false
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
