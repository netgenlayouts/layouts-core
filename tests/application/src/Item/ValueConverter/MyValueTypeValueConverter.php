<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\App\Item\ValueConverter;

use Netgen\Layouts\Item\ValueConverterInterface;
use Netgen\Layouts\Tests\App\Item\Value;

final class MyValueTypeValueConverter implements ValueConverterInterface
{
    public function supports(object $object): bool
    {
        return $object instanceof Value;
    }

    public function getValueType(object $object): string
    {
        return 'my_value_type';
    }

    /**
     * @param \Netgen\Layouts\Tests\App\Item\Value $object
     *
     * @return int|string
     */
    public function getId(object $object)
    {
        return $object->id;
    }

    /**
     * @param \Netgen\Layouts\Tests\App\Item\Value $object
     *
     * @return int|string
     */
    public function getRemoteId(object $object)
    {
        return $object->id;
    }

    /**
     * @param \Netgen\Layouts\Tests\App\Item\Value $object
     */
    public function getName(object $object): string
    {
        return 'Value with ID #' . $object->id;
    }

    public function getIsVisible(object $object): bool
    {
        return true;
    }

    public function getObject(object $object): object
    {
        return $object;
    }
}