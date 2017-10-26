<?php

namespace Netgen\BlockManager\Tests\View\Fragment;

use Netgen\BlockManager\Context\ContextInterface;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\HttpCache\Block\CacheableResolverInterface;
use Netgen\BlockManager\View\Fragment\BlockViewRenderer;
use Netgen\BlockManager\View\View\BlockView;
use Netgen\BlockManager\View\View\LayoutView;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Controller\ControllerReference;

class BlockViewRendererTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $contextMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $cacheableResolverMock;

    /**
     * @var \Netgen\BlockManager\View\Fragment\BlockViewRenderer
     */
    private $blockViewRenderer;

    public function setUp()
    {
        $this->contextMock = $this->createMock(ContextInterface::class);
        $this->cacheableResolverMock = $this->createMock(CacheableResolverInterface::class);

        $this->blockViewRenderer = new BlockViewRenderer(
            $this->contextMock,
            $this->cacheableResolverMock,
            'block_controller',
            array('default', 'api')
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\Fragment\BlockViewRenderer::__construct
     * @covers \Netgen\BlockManager\View\Fragment\BlockViewRenderer::supportsView
     */
    public function testSupportsView()
    {
        $view = new BlockView(array('block' => new Block()));
        $view->setContext('default');

        $this->cacheableResolverMock
            ->expects($this->any())
            ->method('isCacheable')
            ->with($this->equalTo(new Block()))
            ->will($this->returnValue(true));

        $this->assertTrue($this->blockViewRenderer->supportsView($view));
    }

    /**
     * @covers \Netgen\BlockManager\View\Fragment\BlockViewRenderer::supportsView
     */
    public function testSupportsViewWithNoBlockView()
    {
        $view = new LayoutView();

        $this->cacheableResolverMock
            ->expects($this->never())
            ->method('isCacheable');

        $this->assertFalse($this->blockViewRenderer->supportsView($view));
    }

    /**
     * @covers \Netgen\BlockManager\View\Fragment\BlockViewRenderer::supportsView
     */
    public function testSupportsViewWithNonSupportedContext()
    {
        $view = new BlockView();
        $view->setContext('unsupported');

        $this->cacheableResolverMock
            ->expects($this->never())
            ->method('isCacheable');

        $this->assertFalse($this->blockViewRenderer->supportsView($view));
    }

    /**
     * @covers \Netgen\BlockManager\View\Fragment\BlockViewRenderer::getController
     */
    public function testGetController()
    {
        $this->contextMock->expects($this->once())
            ->method('all')
            ->will($this->returnValue(array('var' => 'value')));

        $block = new Block(
            array(
                'id' => 42,
                'availableLocales' => array('en'),
                'locale' => 'en',
            )
        );

        $view = new BlockView(array('block' => $block));
        $view->setContext('default');

        $controller = $this->blockViewRenderer->getController($view);

        $this->assertInstanceOf(ControllerReference::class, $controller);
        $this->assertEquals('block_controller', $controller->controller);

        $this->assertEquals(
            array(
                'blockId' => 42,
                'locale' => 'en',
                'viewContext' => 'default',
                'ngbmContext' => array('var' => 'value'),
                '_ngbm_status' => 'published',
            ),
            $controller->attributes
        );
    }
}
