<?php

namespace Netgen\BlockManager\Persistence\Values\Collection;

use Netgen\BlockManager\ValueObject;

class CollectionCreateStruct extends ValueObject
{
    /**
     * @var int
     */
    public $status;

    /**
     * @var bool
     */
    public $alwaysAvailable;

    /**
     * @var bool
     */
    public $isTranslatable;

    /**
     * @var string
     */
    public $mainLocale;
}
