<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Persistence\Values\Collection;

use Netgen\BlockManager\Persistence\Values\Value;
use Netgen\BlockManager\Utils\HydratorTrait;

final class Item extends Value
{
    use HydratorTrait;

    /**
     * Denotes that the item is manual. Manual items are injected in between items fetched from queries.
     */
    public const TYPE_MANUAL = 0;

    /**
     * Denotes that the item is an override. Override items replace the items fetched from queries at specified position.
     */
    public const TYPE_OVERRIDE = 1;

    /**
     * Item ID.
     *
     * @var int|string
     */
    public $id;

    /**
     * ID of the collection to which this item belongs.
     *
     * @var int|string
     */
    public $collectionId;

    /**
     * Position of item within the collection.
     *
     * @var int
     */
    public $position;

    /**
     * Type of the item. One of self::TYPE_* flags.
     *
     * @var int
     */
    public $type;

    /**
     * Value from CMS this item wraps. This is usually the ID of the CMS entity.
     *
     * @var int|string
     */
    public $value;

    /**
     * Type of value from CMS this item wraps.
     *
     * @var string
     */
    public $valueType;

    /**
     * Item configuration.
     *
     * @var array
     */
    public $config;
}
