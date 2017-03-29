<?php

namespace Netgen\BlockManager\Tests\Block\Stubs;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType;
use Netgen\BlockManager\Block\BlockDefinition\ContainerDefinitionHandlerInterface;
use Netgen\BlockManager\Block\ContainerDefinitionInterface;
use Netgen\BlockManager\Block\PlaceholderDefinition;
use Netgen\BlockManager\Exception\InvalidArgumentException;

class ContainerDefinition implements ContainerDefinitionInterface
{
    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var \Netgen\BlockManager\Tests\Block\Stubs\ContainerDefinitionHandler
     */
    protected $handler;

    /**
     * @var array
     */
    protected $viewTypes;

    /**
     * @var \Netgen\BlockManager\Block\PlaceholderDefinitionInterface[]
     */
    protected $placeholders;

    /**
     * Constructor.
     *
     * @param string $identifier
     * @param array $viewTypes
     * @param \Netgen\BlockManager\Block\BlockDefinition\ContainerDefinitionHandlerInterface $handler
     */
    public function __construct($identifier, array $viewTypes = array(), ContainerDefinitionHandlerInterface $handler = null)
    {
        $this->identifier = $identifier;
        $this->viewTypes = $viewTypes;

        $this->handler = $handler ?: new ContainerDefinitionHandler();

        $this->placeholders = array();

        foreach ($this->handler->getPlaceholderIdentifiers() as $placeholderIdentifier) {
            $this->placeholders[$placeholderIdentifier] = new PlaceholderDefinition(
                array(
                    'identifier' => $placeholderIdentifier,
                    'parameters' => $this->handler->getParameters(),
                )
            );
        }
    }

    /**
     * Returns block definition identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Returns placeholder definitions.
     *
     * @return \Netgen\BlockManager\Block\PlaceholderDefinitionInterface[]
     */
    public function getPlaceholders()
    {
        return $this->placeholders;
    }

    /**
     * Returns a placeholder definition.
     *
     * @param string $placeholderIdentifier
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException if the placeholder does not exist
     *
     * @return \Netgen\BlockManager\Block\PlaceholderDefinitionInterface
     */
    public function getPlaceholder($placeholderIdentifier)
    {
        return $this->placeholders[$placeholderIdentifier];
    }

    /**
     * Returns if block definition has a placeholder definition.
     *
     * @param string $placeholderIdentifier
     *
     * @return bool
     */
    public function hasPlaceholder($placeholderIdentifier)
    {
        return isset($this->placeholders[$placeholderIdentifier]);
    }

    /**
     * Returns dynamic placeholder definition.
     *
     * @return \Netgen\BlockManager\Block\PlaceholderDefinitionInterface
     */
    public function getDynamicPlaceholder()
    {
    }

    /**
     * Returns the list of parameters in the object.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterInterface[]
     */
    public function getParameters()
    {
        return $this->handler->getParameters();
    }

    /**
     * Returns the parameter with provided name.
     *
     * @param string $parameterName
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If parameter with provided name does not exist
     *
     * @return \Netgen\BlockManager\Parameters\ParameterInterface
     */
    public function getParameter($parameterName)
    {
        if ($this->hasParameter($parameterName)) {
            return $this->handler->getParameters()[$parameterName];
        }

        throw new InvalidArgumentException('parameterName', 'Parameter is missing.');
    }

    /**
     * Returns if the parameter with provided name exists in the collection.
     *
     * @param string $parameterName
     *
     * @return bool
     */
    public function hasParameter($parameterName)
    {
        return isset($this->handler->getParameters()[$parameterName]);
    }

    /**
     * Returns the array of dynamic parameters provided by this block definition.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param array $parameters
     *
     * @return array
     */
    public function getDynamicParameters(Block $block, array $parameters = array())
    {
        return $this->handler->getDynamicParameters($block, $parameters);
    }

    /**
     * Returns if this block definition should have a collection.
     *
     * @return bool
     */
    public function hasCollection()
    {
        return $this->handler->hasCollection();
    }

    /**
     * Returns the block definition configuration.
     *
     * @return \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration
     */
    public function getConfig()
    {
        $viewTypes = array();
        foreach ($this->viewTypes as $viewType => $itemTypes) {
            $itemViewTypes = array();
            foreach ($itemTypes as $itemType) {
                $itemViewTypes[$itemType] = new ItemViewType(
                    array(
                        'identifier' => $itemType,
                        'name' => $itemType,
                    )
                );
            }

            $viewTypes[$viewType] = new ViewType(
                array(
                    'identifier' => $viewType,
                    'name' => $viewType,
                    'itemViewTypes' => $itemViewTypes,
                )
            );
        }

        return new Configuration(
            array(
                'identifier' => $this->identifier,
                'viewTypes' => $viewTypes,
            )
        );
    }

    /**
     * Returns if this block definition is a container.
     *
     * @return bool
     */
    public function isContainer()
    {
        return !empty($this->placeholders);
    }

    /**
     * Returns if this block definition is a dynamic container.
     *
     * @return bool
     */
    public function isDynamicContainer()
    {
        return false;
    }
}