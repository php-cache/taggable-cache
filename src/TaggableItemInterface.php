<?php

namespace Cache\Taggable;

interface TaggableItemInterface
{
    /**
     * An array with strings. If you attempt to change tags once already set an exception must be thrown.
     *
     * @param array $tags
     *
     * @return $this
     */
    public function setTags(array $tags);

    /**
     * Return an array of the tags.
     *
     * @return array
     */
    public function getTags();
}
