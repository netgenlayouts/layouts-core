<?php

declare(strict_types=1);

namespace Netgen\Layouts\Collection\Result;

use Netgen\Layouts\Item\CmsItemInterface;

/**
 * A result is a wrapper around the item generated by running the collection.
 * Instances of this object are wrapped into a result set, to be used by the block.
 */
final class Result
{
    /**
     * @var int
     */
    private $position;

    /**
     * @var \Netgen\Layouts\Item\CmsItemInterface
     */
    private $item;

    /**
     * @var \Netgen\Layouts\Item\CmsItemInterface|null
     */
    private $subItem;

    public function __construct(int $position, CmsItemInterface $item, ?CmsItemInterface $subItem = null)
    {
        $this->position = $position;
        $this->item = $item;
        $this->subItem = $subItem;
    }

    /**
     * Returns the position of the result in the result set.
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * Returns the item that will be displayed.
     */
    public function getItem(): CmsItemInterface
    {
        return $this->item;
    }

    /**
     * Returns the item that was overridden by item returned by self::getItem or null
     * if no item was overridden.
     *
     * E.g. This can be the manual item that is not visible or is invalid and thus
     * replaced by a dynamic item for display purposes.
     */
    public function getSubItem(): ?CmsItemInterface
    {
        return $this->subItem;
    }
}
