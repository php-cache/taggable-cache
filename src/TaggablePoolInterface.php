<?php

namespace Cache\Taggable;

/**
 * Lets you add tags to your cache items. Prepend the PSR-6 function arguments with an array of tag names for
 * functions not requiring an CacheItemInterface
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
interface TaggablePoolInterface
{
    public function getItem($key, array $tags = array());
    public function getItems(array $keys = array(), array $tags = array());
    public function hasItem($key, array $tags = array());
    public function clear(array $tags = array());
    public function deleteItem($key, array $tags = array());
    public function deleteItems(array $keys, array $tags = array());
}
