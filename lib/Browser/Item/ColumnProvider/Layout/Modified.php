<?php

namespace Netgen\BlockManager\Browser\Item\ColumnProvider\Layout;

use Netgen\ContentBrowser\Item\ColumnProvider\ColumnValueProviderInterface;
use Netgen\ContentBrowser\Item\ItemInterface;

final class Modified implements ColumnValueProviderInterface
{
    /**
     * @var string
     */
    private $dateFormat;

    /**
     * Constructor.
     *
     * @param string $dateFormat
     */
    public function __construct($dateFormat)
    {
        $this->dateFormat = $dateFormat;
    }

    public function getValue(ItemInterface $item)
    {
        return $item->getLayout()->getModified()->format(
            $this->dateFormat
        );
    }
}
