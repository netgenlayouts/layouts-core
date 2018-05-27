<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller;

use Netgen\BlockManager\View\ViewInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

abstract class Controller extends BaseController
{
    /**
     * Initializes the controller by setting the container and performing basic access checks.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function initialize(ContainerInterface $container)
    {
        $this->setContainer($container);
        $this->checkPermissions();
    }

    /**
     * Performs access checks on the controller.
     */
    protected function checkPermissions()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
    }

    /**
     * Builds the view from provided value.
     *
     * @param mixed $value
     * @param string $context
     * @param array $parameters
     * @param \Symfony\Component\HttpFoundation\Response $response
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    protected function buildView(
        $value,
        $context = ViewInterface::CONTEXT_DEFAULT,
        array $parameters = [],
        Response $response = null
    ) {
        /** @var \Netgen\BlockManager\View\ViewBuilderInterface $viewBuilder */
        $viewBuilder = $this->get('netgen_block_manager.view.view_builder');
        $view = $viewBuilder->buildView($value, $context, $parameters);

        $view->setResponse($response instanceof Response ? $response : new Response());

        return $view;
    }

    /**
     * Returns the specified item definition from the registry.
     *
     * @param string $valueType
     *
     * @return \Netgen\BlockManager\Collection\Item\ItemDefinitionInterface
     */
    protected function getItemDefinition($valueType)
    {
        /** @var \Netgen\BlockManager\Collection\Registry\ItemDefinitionRegistryInterface $itemDefinitionRegistry */
        $itemDefinitionRegistry = $this->get('netgen_block_manager.collection.registry.item_definition');

        return $itemDefinitionRegistry->getItemDefinition($valueType);
    }

    /**
     * Returns the specified query type from the registry.
     *
     * @param string $identifier
     *
     * @return \Netgen\BlockManager\Collection\QueryType\QueryTypeInterface
     */
    protected function getQueryType($identifier)
    {
        /** @var \Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface $queryTypeRegistry */
        $queryTypeRegistry = $this->get('netgen_block_manager.collection.registry.query_type');

        return $queryTypeRegistry->getQueryType($identifier);
    }

    /**
     * Returns the specified layout type from the registry.
     *
     * @param string $identifier
     *
     * @return \Netgen\BlockManager\Layout\Type\LayoutTypeInterface
     */
    protected function getLayoutType($identifier)
    {
        /** @var \Netgen\BlockManager\Layout\Registry\LayoutTypeRegistryInterface $layoutTypeRegistry */
        $layoutTypeRegistry = $this->get('netgen_block_manager.layout.registry.layout_type');

        return $layoutTypeRegistry->getLayoutType($identifier);
    }

    /**
     * Returns the specified block type from the registry.
     *
     * @param string $identifier
     *
     * @return \Netgen\BlockManager\Block\BlockType\BlockType
     */
    protected function getBlockType($identifier)
    {
        /** @var \Netgen\BlockManager\Block\Registry\BlockTypeRegistryInterface $blockTypeRegistry */
        $blockTypeRegistry = $this->get('netgen_block_manager.block.registry.block_type');

        return $blockTypeRegistry->getBlockType($identifier);
    }

    /**
     * Returns if the specified value type exists in the registry.
     *
     * @param string $type
     *
     * @return bool
     */
    protected function hasValueType($type)
    {
        /** @var \Netgen\BlockManager\Item\Registry\ValueTypeRegistryInterface $valueTypeRegistry */
        $valueTypeRegistry = $this->get('netgen_block_manager.item.registry.value_type');

        return $valueTypeRegistry->hasValueType($type);
    }
}
