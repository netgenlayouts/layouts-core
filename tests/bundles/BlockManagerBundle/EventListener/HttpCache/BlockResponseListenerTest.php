<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\EventListener\HttpCache;

use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\HttpCache\TaggerInterface;
use Netgen\BlockManager\View\View\BlockView;
use Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\BlockResponseListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class BlockResponseListenerTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $taggerMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\BlockResponseListener
     */
    private $listener;

    public function setUp()
    {
        $this->taggerMock = $this->createMock(TaggerInterface::class);

        $this->listener = new BlockResponseListener($this->taggerMock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\BlockResponseListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        $this->assertEquals(
            [KernelEvents::RESPONSE => ['onKernelResponse', -255]],
            $this->listener->getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\BlockResponseListener::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\BlockResponseListener::onKernelResponse
     */
    public function testOnKernelResponse()
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $request->attributes->set('ngbmView', new BlockView(['block' => new Block()]));

        $event = new FilterResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new Response()
        );

        $this->taggerMock
            ->expects($this->once())
            ->method('tagBlock')
            ->with($this->equalTo(new Response()), $this->equalTo(new Block()));

        $this->listener->onKernelResponse($event);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\BlockResponseListener::onKernelResponse
     */
    public function testOnKernelResponseWithSubRequest()
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $request->attributes->set('ngbmView', new BlockView());

        $event = new FilterResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::SUB_REQUEST,
            new Response()
        );

        $this->taggerMock
            ->expects($this->never())
            ->method('tagBlock');

        $this->listener->onKernelResponse($event);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\BlockResponseListener::onKernelResponse
     */
    public function testOnKernelResponseWithoutSupportedValue()
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $request->attributes->set('ngbmView', 42);

        $event = new FilterResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new Response()
        );

        $this->taggerMock
            ->expects($this->never())
            ->method('tagBlock');

        $this->listener->onKernelResponse($event);
    }
}
