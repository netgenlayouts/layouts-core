<?php

namespace Netgen\Bundle\BlockManagerBundle\ParamConverter\Layout;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\Bundle\BlockManagerBundle\ParamConverter\ParamConverter;

class LayoutParamConverter extends ParamConverter
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    protected $layoutService;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\LayoutService $layoutService
     */
    public function __construct(LayoutService $layoutService)
    {
        $this->layoutService = $layoutService;
    }

    /**
     * Returns source attribute name.
     *
     * @return array
     */
    public function getSourceAttributeNames()
    {
        return array('layoutId');
    }

    /**
     * Returns destination attribute name.
     *
     * @return string
     */
    public function getDestinationAttributeName()
    {
        return 'layout';
    }

    /**
     * Returns the supported class.
     *
     * @return string
     */
    public function getSupportedClass()
    {
        return Layout::class;
    }

    /**
     * Returns the value object.
     *
     * @param array $values
     *
     * @return \Netgen\BlockManager\API\Values\Value
     */
    public function loadValueObject(array $values)
    {
        if ($values['published']) {
            return $this->layoutService->loadLayout($values['layoutId']);
        }

        return $this->layoutService->loadLayoutDraft($values['layoutId']);
    }
}