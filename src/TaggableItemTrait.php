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

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
trait TaggableItemTrait
{
    /**
     * @return string
     */
    abstract public function getTaggedKey();

    /**
     * Return the key for this item.
     *
     * @return string
     */
    public function getKey()
    {
        $key = $this->getTaggedKey();
        if (false === $pos = strrpos($key, ':')) {
            return $key;
        }

        return substr($key, $pos + 1);
    }
}
