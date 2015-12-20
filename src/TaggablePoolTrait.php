<?php

/*
 * This file is part of php-cache\taggable-cache package.
 *
 * (c) 2015-2015 Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Cache\Taggable;

use Psr\Cache\CacheItemInterface;

/**
 * Use this trait with a CacheItemPoolInterface to support tagging.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
trait TaggablePoolTrait
{
    /**
     * From Psr\Cache\CacheItemPoolInterface.
     *
     * @param CacheItemInterface $item
     *
     * @return bool
     */
    abstract public function save(CacheItemInterface $item);

    /**
     * From Psr\Cache\CacheItemPoolInterface.
     * This function should run $this->generateCacheKey to get a key using the tags.
     *
     * @param string $key
     *
     * @return CacheItemInterface
     */
    abstract public function getItem($key);

    /**
     * Return an CacheItemInterface for a tag.
     * This function MUST NOT run $this->generateCacheKey.
     *
     * @param $key
     *
     * @return CacheItemInterface
     */
    abstract protected function getTagItem($key);

    /**
     * Reset the tag and return the new tag identifier.
     *
     * This will not delete anything form cache, only generate a new reference. This is a memory leak.
     *
     * @param string $name
     *
     * @return string
     */
    protected function flushTag($name)
    {
        $item = $this->getTagItem($this->getTagKey($name));

        return $this->generateNewTagId($item);
    }

    /**
     * Generate a good cache key that is dependent of the tags. This key should be the key of the CacheItem.
     *
     * @param string $key
     * @param array  $tags
     *
     * @return string
     */
    protected function generateCacheKey($key, array $tags)
    {
        // We sort the tags because the order should not matter
        sort($tags);

        $tagIds = [];
        foreach ($tags as $tag) {
            $tagIds[] = $this->getTagId($tag);
        }
        $tagsNamespace = sha1(implode('|', $tagIds));

        return $tagsNamespace.':'.$key;
    }

    /**
     * Get the unique tag identifier for a given tag.
     *
     * @param string $name
     *
     * @return string
     */
    private function getTagId($name)
    {
        $item = $this->getTagItem($this->getTagKey($name));

        if ($item->isHit()) {
            return $item->get();
        }

        return $this->generateNewTagId($item);
    }

    /**
     * Get the tag identifier key for a given tag.
     *
     * @param string $name
     *
     * @return string
     */
    private function getTagKey($name)
    {
        return 'tag:'.$name.':key';
    }

    /**
     * A TagId is retrieved from cache using the TagKey.
     *
     * @param \Psr\Cache\CacheItemPoolInterface $storage
     * @param CacheItemInterface                $item
     *
     * @return string
     */
    private function generateNewTagId(CacheItemInterface $item)
    {
        $value = str_replace('.', '', uniqid('', true));
        $item->set($value);
        $item->expiresAfter(null);
        $this->save($item);

        return $value;
    }
}
