<?php

namespace Cache\Taggable;

interface TaggableItemInterface
{
    /**
     * Return an array of the tags.
     *
     * @return array
     */
    public function getTags();
}
