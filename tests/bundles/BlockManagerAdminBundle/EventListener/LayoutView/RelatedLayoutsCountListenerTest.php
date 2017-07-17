<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Tests\EventListener\LayoutView;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Event\BlockManagerEvents;
use Netgen\BlockManager\Event\CollectViewParametersEvent;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\View\LayoutView;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerAdminBundle\EventListener\LayoutView\RelatedLayoutsCountListener;
use PHPUnit\Framework\TestCase;

class RelatedLayoutsCountListenerTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutServiceMock;

    /**
     * @var \Netgen\Bundle\BlockManagerAdminBundle\EventListener\LayoutView\RelatedLayoutsCountListener
     */
    protected $listener;

    /**
     * Sets up the test.
     */
    public function setUp()
    {
        $this->layoutServiceMock = $this->createMock(LayoutService::class);

        $this->listener = new RelatedLayoutsCountListener($this->layoutServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\LayoutView\RelatedLayoutsCountListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        $this->assertEquals(
            array(BlockManagerEvents::BUILD_VIEW => 'onBuildView'),
            $this->listener->getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\LayoutView\RelatedLayoutsCountListener::__construct
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\LayoutView\RelatedLayoutsCountListener::onBuildView
     */
    public function testOnBuildView()
    {
        $view = new LayoutView(array('layout' => new Layout(array('shared' => true, 'published' => true))));
        $view->setContext(ViewInterface::CONTEXT_ADMIN);
        $event = new CollectViewParametersEvent($view);

        $this->layoutServiceMock
            ->expects($this->once())
            ->method('getRelatedLayoutsCount')
            ->with($this->equalTo(new Layout(array('shared' => true, 'published' => true))))
            ->will($this->returnValue(3));

        $this->listener->onBuildView($event);

        $this->assertEquals(
            array(
                'related_layouts_count' => 3,
            ),
            $event->getParameters()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\LayoutView\RelatedLayoutsCountListener::onBuildView
     */
    public function testOnBuildViewWithDraftLayout()
    {
        $view = new LayoutView(array('layout' => new Layout(array('shared' => true, 'published' => false))));
        $view->setContext(ViewInterface::CONTEXT_ADMIN);
        $event = new CollectViewParametersEvent($view);

        $this->layoutServiceMock
            ->expects($this->never())
            ->method('getRelatedLayoutsCount');

        $this->listener->onBuildView($event);

        $this->assertEquals(
            array(
                'related_layouts_count' => 0,
            ),
            $event->getParameters()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\LayoutView\RelatedLayoutsCountListener::onBuildView
     */
    public function testOnBuildViewWithNonSharedLayout()
    {
        $view = new LayoutView(array('layout' => new Layout(array('shared' => false, 'published' => true))));
        $view->setContext(ViewInterface::CONTEXT_ADMIN);
        $event = new CollectViewParametersEvent($view);

        $this->layoutServiceMock
            ->expects($this->never())
            ->method('getRelatedLayoutsCount');

        $this->listener->onBuildView($event);

        $this->assertEquals(
            array(
                'related_layouts_count' => 0,
            ),
            $event->getParameters()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\LayoutView\RelatedLayoutsCountListener::onBuildView
     */
    public function testOnBuildViewWithNoLayoutView()
    {
        $view = new View(array('value' => new Value()));
        $event = new CollectViewParametersEvent($view);
        $this->listener->onBuildView($event);

        $this->assertEquals(array(), $event->getParameters());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\LayoutView\RelatedLayoutsCountListener::onBuildView
     */
    public function testOnBuildViewWithWrongContext()
    {
        $view = new LayoutView(array('layout' => new Layout()));
        $view->setContext(ViewInterface::CONTEXT_API);
        $event = new CollectViewParametersEvent($view);

        $this->listener->onBuildView($event);

        $this->assertEquals(array(), $event->getParameters());
    }
}