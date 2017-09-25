<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Parameters;

use Netgen\BlockManager\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class FormMapperPass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.parameters.registry.form_mapper';
    const TAG_NAME = 'netgen_block_manager.parameters.form.mapper';

    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        $registry = $container->findDefinition(self::SERVICE_NAME);

        foreach ($container->findTaggedServiceIds(self::TAG_NAME) as $formMapper => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['type'])) {
                    throw new RuntimeException(
                        "Parameter form mapper service definition must have a 'type' attribute in its' tag."
                    );
                }

                $registry->addMethodCall(
                    'addFormMapper',
                    array(
                        $tag['type'],
                        new Reference($formMapper),
                    )
                );
            }
        }
    }
}
