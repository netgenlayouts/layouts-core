<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNodeInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

final class DesignNode implements ConfigurationNodeInterface
{
    public function getConfigurationNode()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('design', 'scalar');

        $node
            ->defaultValue('standard')
            ->cannotBeEmpty();

        return $node;
    }
}