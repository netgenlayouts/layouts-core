<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\ParamConverter\Layout;

use Netgen\Bundle\LayoutsBundle\ParamConverter\ParamConverter;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Value;
use Ramsey\Uuid\Uuid;

final class LayoutParamConverter extends ParamConverter
{
    /**
     * @var \Netgen\Layouts\API\Service\LayoutService
     */
    private $layoutService;

    public function __construct(LayoutService $layoutService)
    {
        $this->layoutService = $layoutService;
    }

    public function getSourceAttributeNames(): array
    {
        return ['layoutId'];
    }

    public function getDestinationAttributeName(): string
    {
        return 'layout';
    }

    public function getSupportedClass(): string
    {
        return Layout::class;
    }

    public function loadValue(array $values): Value
    {
        if ($values['status'] === self::STATUS_PUBLISHED) {
            return $this->layoutService->loadLayout(Uuid::fromString($values['layoutId']));
        }

        if ($values['status'] === self::STATUS_ARCHIVED) {
            return $this->layoutService->loadLayoutArchive(Uuid::fromString($values['layoutId']));
        }

        return $this->layoutService->loadLayoutDraft(Uuid::fromString($values['layoutId']));
    }
}