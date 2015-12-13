<?php

namespace Cache\Taggable\Tests\Helper;

use Psr\Cache\CacheItemInterface;

class CacheItem implements CacheItemInterface
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $value;

    /**
     * @var boolean
     */
    private $hasValue = false;

    /**
     *
     * @param string $key
     */
    public function __construct($key)
    {
        $this->key = $key;
    }

    /**
     *
     * @return boolean
     */
    public function isHit()
    {
        return $this->hasValue;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function get()
    {
        return $this->value;
    }

    /**
     * @param string $value
     *
     * @return CacheItem
     */
    public function set($value)
    {
        $this->hasValue = true;
        $this->value = $value;

        return $this;
    }

    public function expiresAt($expiration)
    {
        // TODO: Implement expiresAt() method.
    }

    public function expiresAfter($time)
    {
        // TODO: Implement expiresAfter() method.
    }


}