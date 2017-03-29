<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block;

use Netgen\BlockManager\Block\BlockType\BlockType;
use Netgen\BlockManager\Block\BlockType\BlockTypeFactory;
use Netgen\BlockManager\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class BlockTypePass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.block.registry.block_type';
    const TAG_NAME = 'netgen_block_manager.block.block_type';

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        $blockTypes = $container->getParameter('netgen_block_manager.block_types');
        $blockDefinitions = $container->getParameter('netgen_block_manager.block_definitions');

        $blockTypes = $this->generateBlockTypeConfig($blockTypes, $blockDefinitions);
        $container->setParameter('netgen_block_manager.block_types', $blockTypes);

        $this->validateBlockTypes($blockTypes, $blockDefinitions);
        $this->buildBlockTypes($container, $blockTypes, $blockDefinitions);

        $registry = $container->findDefinition(self::SERVICE_NAME);
        $blockTypeServices = $container->findTaggedServiceIds(self::TAG_NAME);

        foreach ($blockTypeServices as $blockTypeService => $tag) {
            $registry->addMethodCall(
                'addBlockType',
                array(new Reference($blockTypeService))
            );
        }
    }

    /**
     * Generates the block type configuration from provided block definitions.
     *
     * @param array $blockTypes
     * @param array $blockDefinitions
     *
     * @return array
     */
    protected function generateBlockTypeConfig(array $blockTypes, array $blockDefinitions)
    {
        foreach ($blockDefinitions as $identifier => $blockDefinition) {
            if (
                !empty($blockTypes[$identifier]['definition_identifier']) &&
                $blockTypes[$identifier]['definition_identifier'] !== $identifier
            ) {
                // We skip the block types which have been completely redefined
                // i.e. had the block definition identifier changed
                continue;
            }

            if (!isset($blockTypes[$identifier])) {
                $blockTypes[$identifier] = array(
                    'name' => $blockDefinition['name'],
                    'enabled' => $blockDefinition['enabled'],
                    'definition_identifier' => $identifier,
                    'defaults' => array(),
                );

                continue;
            }

            $blockTypes[$identifier] = $blockTypes[$identifier] + array(
                'name' => $blockDefinition['name'],
                'enabled' => $blockDefinition['enabled'],
                'definition_identifier' => $identifier,
            );
        }

        return $blockTypes;
    }

    /**
     * Builds the block type objects from provided configuration.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param array $blockTypes
     * @param array $blockDefinitions
     */
    protected function buildBlockTypes(ContainerBuilder $container, array $blockTypes, array $blockDefinitions)
    {
        foreach ($blockTypes as $identifier => $blockType) {
            $definitionIdentifier = $blockType['definition_identifier'];
            if (!$blockType['enabled'] || !$blockDefinitions[$definitionIdentifier]['enabled']) {
                continue;
            }

            $serviceIdentifier = sprintf('netgen_block_manager.block.block_type.%s', $identifier);

            $container->register($serviceIdentifier, BlockType::class)
                ->setArguments(
                    array(
                        $identifier,
                        $blockType,
                        new Reference(
                            sprintf(
                                'netgen_block_manager.block.block_definition.%s',
                                $definitionIdentifier
                            )
                        ),
                    )
                )
                ->setLazy(true)
                ->addTag('netgen_block_manager.block.block_type')
                ->setFactory(array(BlockTypeFactory::class, 'buildBlockType'));
        }
    }

    /**
     * Validates block type config.
     *
     * @param array $blockTypes
     * @param array $blockDefinitions
     *
     * @throws \RuntimeException If validation failed
     */
    protected function validateBlockTypes(array $blockTypes, array $blockDefinitions)
    {
        foreach ($blockTypes as $identifier => $blockType) {
            if (!isset($blockDefinitions[$blockType['definition_identifier']])) {
                throw new RuntimeException(
                    sprintf(
                        'Block definition "%s" used in "%s" block type does not exist.',
                        $blockType['definition_identifier'],
                        $identifier
                    )
                );
            }
        }
    }
}