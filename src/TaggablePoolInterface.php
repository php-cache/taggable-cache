<?php

/*
 * This file is part of php-cache\taggable-cache package.
 *
 * (c) 2015-2015 Aaron Scherer <aequasi@gmail.com>, Tobias Nyholm <tobias.nyholm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Cache\Taggable;

use Psr\Cache\CacheItemPoolInterface;

/**
 * This is a CacheItemPoolInterface tha has TagSupportInterface.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
interface TaggablePoolInterface extends CacheItemPoolInterface, HasTagSupportInterface
{
}
