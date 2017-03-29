<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\Collection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection\SourcePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class SourcePassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection\SourcePass::process
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection\SourcePass::buildSources
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection\SourcePass::validateSources
     */
    public function testProcess()
    {
        $this->setParameter(
            'netgen_block_manager.query_types',
            array(
                'type' => array(),
            )
        );

        $this->setParameter(
            'netgen_block_manager.sources',
            array(
                'test' => array(
                    'enabled' => true,
                    'queries' => array(
                        'query' => array(
                            'query_type' => 'type',
                        ),
                    ),
                ),
            )
        );

        $this->setDefinition('netgen_block_manager.collection.registry.source', new Definition());

        $this->compile();

        $this->assertContainerBuilderHasService('netgen_block_manager.collection.source.test');
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.collection.registry.source',
            'addSource',
            array(
                new Reference('netgen_block_manager.collection.source.test'),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection\SourcePass::process
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection\SourcePass::buildSources
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection\SourcePass::validateSources
     */
    public function testProcessWithDisabledSource()
    {
        $this->setParameter('netgen_block_manager.query_types', array());
        $this->setParameter(
            'netgen_block_manager.sources',
            array(
                'test' => array(
                    'enabled' => false,
                    'queries' => array(),
                ),
            )
        );

        $this->setDefinition('netgen_block_manager.collection.registry.source', new Definition());

        $this->compile();

        $this->assertContainerBuilderNotHasService('netgen_block_manager.collection.source.test');
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection\SourcePass::process
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection\SourcePass::buildSources
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection\SourcePass::validateSources
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Query type "type" used in "test" source does not exist.
     */
    public function testProcessThrowsRuntimeExceptionWithNoQueryType()
    {
        $this->setParameter('netgen_block_manager.query_types', array());
        $this->setParameter(
            'netgen_block_manager.sources',
            array(
                'test' => array(
                    'enabled' => true,
                    'queries' => array(
                        'query' => array(
                            'query_type' => 'type',
                        ),
                    ),
                ),
            )
        );

        $this->setDefinition('netgen_block_manager.collection.registry.source', new Definition());

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection\SourcePass::process
     */
    public function testProcessWithEmptyContainer()
    {
        $this->compile();

        $this->assertEmpty($this->container->getAliases());
        // The container has at least self ("service_container") as the service
        $this->assertCount(1, $this->container->getServiceIds());
        $this->assertEmpty($this->container->getParameterBag()->all());
    }

    /**
     * Register the compiler pass under test.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new SourcePass());
    }
}