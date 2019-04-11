<?php

declare(strict_types=1);

namespace Netgen\Layouts\Item;

/**
 * Value converter is used to convert the loaded CMS value to an instance of CmsItemInterface.
 * This is achieved by providing information (ID, name, visibility...) used by
 * the item builder service which actually builds the item.
 */
interface ValueConverterInterface
{
    /**
     * Returns if the converter supports the object.
     */
    public function supports(object $object): bool;

    /**
     * Returns the value type for this object.
     */
    public function getValueType(object $object): string;

    /**
     * Returns the object ID.
     *
     * @param object $object
     *
     * @return int|string
     */
    public function getId(object $object);

    /**
     * Returns the object remote ID.
     *
     * @param object $object
     *
     * @return int|string
     */
    public function getRemoteId(object $object);

    /**
     * Returns the object name.
     */
    public function getName(object $object): string;

    /**
     * Returns if the object is visible.
     */
    public function getIsVisible(object $object): bool;

    /**
     * Returns the object itself.
     *
     * This method can be used to enrich the object before it being rendered.
     */
    public function getObject(object $object): object;
}
