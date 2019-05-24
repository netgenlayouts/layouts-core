<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Block;

use Generator;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\DefinitionClassTrait;
use Netgen\Layouts\Block\BlockDefinition;
use Netgen\Layouts\Block\BlockDefinition\ContainerDefinitionHandlerInterface;
use Netgen\Layouts\Block\BlockDefinition\TwigBlockDefinitionHandlerInterface;
use Netgen\Layouts\Block\ContainerDefinition;
use Netgen\Layouts\Block\TwigBlockDefinition;
use Netgen\Layouts\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class BlockDefinitionPass implements CompilerPassInterface
{
    use DefinitionClassTrait;

    private const SERVICE_NAME = 'netgen_layouts.block.registry.block_definition';
    private const TAG_NAME = 'netgen_layouts.block_definition_handler';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        $blockDefinitionRegistry = $container->findDefinition(self::SERVICE_NAME);
        $blockDefinitionHandlers = $container->findTaggedServiceIds(self::TAG_NAME);
        $blockDefinitionServices = [];

        $blockDefinitions = $container->getParameter('netgen_layouts.block_definitions');
        foreach ($blockDefinitions as $identifier => $blockDefinition) {
            $handlerIdentifier = $blockDefinition['handler'] ?? $identifier;

            $foundHandler = null;
            $handlerClass = null;

            foreach ($blockDefinitionHandlers as $blockDefinitionHandler => $tags) {
                $handlerClass = $this->getDefinitionClass($container, $blockDefinitionHandler);

                foreach ($tags as $tag) {
                    if (isset($tag['identifier']) && $tag['identifier'] === $handlerIdentifier) {
                        $foundHandler = $blockDefinitionHandler;

                        break 2;
                    }
                }

                if (isset($handlerClass::$defaultIdentifier)) {
                    if ($handlerClass::$defaultIdentifier === $handlerIdentifier) {
                        $foundHandler = $blockDefinitionHandler;

                        break;
                    }
                }
            }

            if (!is_string($foundHandler) || !is_string($handlerClass)) {
                throw new RuntimeException(
                    sprintf(
                        'Block definition handler for "%s" block definition does not exist.',
                        $identifier
                    )
                );
            }

            $factoryMethod = 'buildBlockDefinition';
            $definitionClass = BlockDefinition::class;

            if (is_a($handlerClass, ContainerDefinitionHandlerInterface::class, true)) {
                $factoryMethod = 'buildContainerDefinition';
                $definitionClass = ContainerDefinition::class;
            } elseif (is_a($handlerClass, TwigBlockDefinitionHandlerInterface::class, true)) {
                $factoryMethod = 'buildTwigBlockDefinition';
                $definitionClass = TwigBlockDefinition::class;
            }

            $blockDefinitionServiceName = sprintf('netgen_layouts.block.block_definition.%s', $identifier);

            $blockDefinitionService = new Definition($definitionClass);
            $blockDefinitionService->setFactory([new Reference('netgen_layouts.block.block_definition_factory'), $factoryMethod]);

            $blockDefinitionService->setLazy(true);
            $blockDefinitionService->setPublic(false);
            $blockDefinitionService->addArgument($identifier);
            $blockDefinitionService->addArgument(new Reference($foundHandler));
            $blockDefinitionService->addArgument($blockDefinition);
            $blockDefinitionService->addArgument(iterator_to_array($this->getConfigHandlers($container)));

            $container->setDefinition($blockDefinitionServiceName, $blockDefinitionService);

            $blockDefinitionServices[$identifier] = new Reference($blockDefinitionServiceName);
        }

        $blockDefinitionRegistry->replaceArgument(0, $blockDefinitionServices);
    }

    private function getConfigHandlers(ContainerBuilder $container): Generator
    {
        $configHandlerServices = $container->findTaggedServiceIds('netgen_layouts.block_config_handler');
        foreach ($configHandlerServices as $configHandlerService => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['config_key'])) {
                    throw new RuntimeException(
                        "Block config handler definition must have an 'config_key' attribute in its' tag."
                    );
                }

                yield $tag['config_key'] => new Reference($configHandlerService);
            }
        }
    }
}