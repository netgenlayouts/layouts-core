<?php

namespace Netgen\BlockManager\HttpCache;

class NullClient implements ClientInterface
{
    /**
     * Invalidates all provided layouts.
     *
     * @param int[]|string[] $layoutIds
     */
    public function invalidateLayouts(array $layoutIds)
    {
    }

    /**
     * Invalidates all layouts.
     */
    public function invalidateAllLayouts()
    {
    }

    /**
     * Invalidates all provided blocks.
     *
     * @param int[]|string[] $blockIds
     */
    public function invalidateBlocks(array $blockIds)
    {
    }

    /**
     * Invalidates all blocks.
     */
    public function invalidateAllBlocks()
    {
    }
}
