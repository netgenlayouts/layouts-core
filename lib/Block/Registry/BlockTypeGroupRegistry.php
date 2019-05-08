<?php

declare(strict_types=1);

namespace Netgen\Layouts\Block\Registry;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use ArrayIterator;
use Netgen\Layouts\Block\BlockType\BlockTypeGroup;
use Netgen\Layouts\Exception\Block\BlockTypeException;
use Netgen\Layouts\Exception\RuntimeException;
use Traversable;

final class BlockTypeGroupRegistry implements IteratorAggregate, Countable, ArrayAccess
{
    /**
     * @var \Netgen\Layouts\Block\BlockType\BlockTypeGroup[]
     */
    private $blockTypeGroups;

    /**
     * @param \Netgen\Layouts\Block\BlockType\BlockTypeGroup[] $blockTypeGroups
     */
    public function __construct(array $blockTypeGroups)
    {
        $this->blockTypeGroups = array_filter(
            $blockTypeGroups,
            static function (BlockTypeGroup $blockTypeGroup): bool {
                return true;
            }
        );
    }

    /**
     * Returns if registry has a block type group.
     */
    public function hasBlockTypeGroup(string $identifier): bool
    {
        return isset($this->blockTypeGroups[$identifier]);
    }

    /**
     * Returns the block type group with provided identifier.
     *
     * @throws \Netgen\Layouts\Exception\Block\BlockTypeException If block type group with provided identifier does not exist
     */
    public function getBlockTypeGroup(string $identifier): BlockTypeGroup
    {
        if (!$this->hasBlockTypeGroup($identifier)) {
            throw BlockTypeException::noBlockTypeGroup($identifier);
        }

        return $this->blockTypeGroups[$identifier];
    }

    /**
     * Returns all block type groups.
     *
     * @param bool $onlyEnabled
     *
     * @return \Netgen\Layouts\Block\BlockType\BlockTypeGroup[]
     */
    public function getBlockTypeGroups(bool $onlyEnabled = false): array
    {
        if (!$onlyEnabled) {
            return $this->blockTypeGroups;
        }

        return array_filter(
            $this->blockTypeGroups,
            static function (BlockTypeGroup $blockTypeGroup): bool {
                return $blockTypeGroup->isEnabled();
            }
        );
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->blockTypeGroups);
    }

    public function count(): int
    {
        return count($this->blockTypeGroups);
    }

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return $this->hasBlockTypeGroup($offset);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->getBlockTypeGroup($offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        throw new RuntimeException('Method call not supported.');
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset): void
    {
        throw new RuntimeException('Method call not supported.');
    }
}
