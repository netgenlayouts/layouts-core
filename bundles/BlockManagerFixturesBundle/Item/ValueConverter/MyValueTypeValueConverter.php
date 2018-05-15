<?php

namespace Netgen\Bundle\BlockManagerFixturesBundle\Item\ValueConverter;

use Netgen\BlockManager\Item\ValueConverterInterface;
use Netgen\Bundle\BlockManagerFixturesBundle\Item\Value;

final class MyValueTypeValueConverter implements ValueConverterInterface
{
    public function supports($object)
    {
        return $object instanceof Value;
    }

    public function getValueType($object)
    {
        return 'my_value_type';
    }

    /**
     * @param \Netgen\Bundle\BlockManagerFixturesBundle\Item\Value $object
     *
     * @return int|string
     */
    public function getId($object)
    {
        return $object->id;
    }

    /**
     * @param \Netgen\Bundle\BlockManagerFixturesBundle\Item\Value $object
     *
     * @return int|string
     */
    public function getRemoteId($object)
    {
        return $object->id;
    }

    /**
     * @param \Netgen\Bundle\BlockManagerFixturesBundle\Item\Value $object
     *
     * @return string
     */
    public function getName($object)
    {
        return 'Value with ID #' . $object->id;
    }

    public function getIsVisible($object)
    {
        return true;
    }

    public function getObject($object)
    {
        return $object;
    }
}