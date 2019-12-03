<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Values\Collection;

use Netgen\Layouts\Utils\HydratorTrait;

final class ItemCreateStruct
{
    use HydratorTrait;

    /**
     * Position of the new item in the collection.
     *
     * @var int
     */
    public $position;

    /**
     * Value from CMS for the new item. This is usually the ID of the CMS entity.
     *
     * @var int|string
     */
    public $value;

    /**
     * Type of value from CMS for the new item.
     *
     * @var string
     */
    public $valueType;

    /**
     * View type which will be used to render the new item.
     *
     * @var string|null
     */
    public $viewType;

    /**
     * The item configuration.
     *
     * @var array<string, array<string, mixed>>
     */
    public $config;
}
