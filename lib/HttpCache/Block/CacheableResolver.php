<?php

namespace Netgen\BlockManager\HttpCache\Block;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\HttpCache\Block\CacheableResolver\VoterInterface;

class CacheableResolver implements CacheableResolverInterface
{
    /**
     * @var \Netgen\BlockManager\HttpCache\Block\CacheableResolver\VoterInterface[]
     */
    protected $voters = array();

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\HttpCache\Block\CacheableResolver\VoterInterface[] $voters
     */
    public function __construct(array $voters = array())
    {
        foreach ($voters as $voter) {
            if (!$voter instanceof VoterInterface) {
                throw new RuntimeException(
                    sprintf(
                        'Voter %s needs to implement %s interface.',
                        get_class($voter),
                        VoterInterface::class
                    )
                );
            }
        }

        $this->voters = $voters;
    }

    /**
     * Returns if the block is cacheable by HTTP caches.
     *
     * In all other caches, block is cacheable.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @return mixed
     */
    public function isCacheable(Block $block)
    {
        foreach ($this->voters as $voter) {
            $result = $voter->vote($block);
            if ($result !== VoterInterface::ABSTAIN) {
                return (bool) $result;
            }
        }

        return true;
    }
}
