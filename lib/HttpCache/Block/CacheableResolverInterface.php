<?php

namespace Netgen\BlockManager\HttpCache\Block;

use Netgen\BlockManager\API\Values\Block\Block;

interface CacheableResolverInterface
{
    /**
     * Returns if the block is cacheable by HTTP caches.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @return mixed
     */
    public function isCacheable(Block $block);
}