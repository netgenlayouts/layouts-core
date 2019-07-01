<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\ParamConverter\Collection;

use Netgen\Bundle\LayoutsBundle\ParamConverter\ParamConverter;
use Netgen\Layouts\API\Service\CollectionService;
use Netgen\Layouts\API\Values\Collection\Slot;
use Netgen\Layouts\API\Values\Value;
use Ramsey\Uuid\Uuid;

final class SlotParamConverter extends ParamConverter
{
    /**
     * @var \Netgen\Layouts\API\Service\CollectionService
     */
    private $collectionService;

    public function __construct(CollectionService $collectionService)
    {
        $this->collectionService = $collectionService;
    }

    public function getSourceAttributeNames(): array
    {
        return ['slotId'];
    }

    public function getDestinationAttributeName(): string
    {
        return 'slot';
    }

    public function getSupportedClass(): string
    {
        return Slot::class;
    }

    public function loadValue(array $values): Value
    {
        if ($values['status'] === self::STATUS_PUBLISHED) {
            return $this->collectionService->loadSlot(Uuid::fromString($values['slotId']));
        }

        return $this->collectionService->loadSlotDraft(Uuid::fromString($values['slotId']));
    }
}