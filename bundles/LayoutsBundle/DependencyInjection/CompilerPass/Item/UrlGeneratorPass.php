<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Item;

use Netgen\Layouts\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class UrlGeneratorPass implements CompilerPassInterface
{
    private const SERVICE_NAME = 'netgen_block_manager.item.url_generator';
    private const TAG_NAME = 'netgen_block_manager.item.value_url_generator';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        $urlGenerator = $container->findDefinition(self::SERVICE_NAME);

        $valueUrlGenerators = [];
        foreach ($container->findTaggedServiceIds(self::TAG_NAME) as $valueUrlGenerator => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['value_type'])) {
                    throw new RuntimeException(
                        "Value URL generator service definition must have a 'value_type' attribute in its' tag."
                    );
                }

                if (preg_match('/^[A-Za-z]([A-Za-z0-9_])*$/', $tag['value_type']) !== 1) {
                    throw new RuntimeException(
                        'Value type must begin with a letter and be followed by any combination of letters, digits and underscore.'
                    );
                }

                $valueUrlGenerators[$tag['value_type']] = new Reference($valueUrlGenerator);
            }
        }

        $urlGenerator->replaceArgument(0, $valueUrlGenerators);
    }
}
