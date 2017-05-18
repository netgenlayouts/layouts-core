<?php

namespace Netgen\Bundle\BlockManagerBundle;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class NetgenBlockManagerBundle extends Bundle
{
    /**
     * Builds the bundle.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new CompilerPass\Config\ConfigDefinitionPass());
        $container->addCompilerPass(new CompilerPass\Block\BlockDefinitionPass());
        $container->addCompilerPass(new CompilerPass\LayoutResolver\TargetTypeRegistryPass());
        $container->addCompilerPass(new CompilerPass\LayoutResolver\ConditionTypeRegistryPass());
        $container->addCompilerPass(new CompilerPass\LayoutResolver\DoctrineTargetHandlerPass());
        $container->addCompilerPass(new CompilerPass\LayoutResolver\Form\ConditionTypePass());
        $container->addCompilerPass(new CompilerPass\LayoutResolver\Form\TargetTypePass());
        $container->addCompilerPass(new CompilerPass\View\TemplateResolverPass());
        $container->addCompilerPass(new CompilerPass\View\ViewBuilderPass());
        $container->addCompilerPass(new CompilerPass\View\FragmentRendererPass());
        $container->addCompilerPass(new CompilerPass\View\DefaultViewTemplatesPass());
        $container->addCompilerPass(new CompilerPass\Parameters\FormMapperRegistryPass());
        $container->addCompilerPass(new CompilerPass\Parameters\ParameterFilterRegistryPass());
        $container->addCompilerPass(new CompilerPass\Parameters\ParameterTypeRegistryPass());
        $container->addCompilerPass(new CompilerPass\Collection\QueryTypePass());
        $container->addCompilerPass(new CompilerPass\Item\ValueTypePass());
        $container->addCompilerPass(new CompilerPass\Item\ItemLoaderPass());
        $container->addCompilerPass(new CompilerPass\Item\ItemBuilderPass());
        $container->addCompilerPass(new CompilerPass\Item\UrlBuilderPass());
        $container->addCompilerPass(new CompilerPass\Collection\SourcePass());
        $container->addCompilerPass(new CompilerPass\Layout\LayoutTypePass());
        $container->addCompilerPass(new CompilerPass\Block\BlockTypePass());
        $container->addCompilerPass(new CompilerPass\Block\BlockTypeGroupPass());
        $container->addCompilerPass(new CompilerPass\HttpCache\CacheManagerPass());
        $container->addCompilerPass(new CompilerPass\HttpCache\ConfigureHttpCachePass());
        $container->addCompilerPass(new CompilerPass\HttpCache\Block\CacheableResolverPass());
    }
}
