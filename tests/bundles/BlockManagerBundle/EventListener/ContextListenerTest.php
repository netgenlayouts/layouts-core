<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\EventListener;

use Netgen\BlockManager\Context\Context;
use Netgen\BlockManager\Context\ContextBuilderInterface;
use Netgen\Bundle\BlockManagerBundle\EventListener\ContextListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\UriSigner;

class ContextListenerTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Context\ContextInterface
     */
    private $context;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $contextBuilderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $uriSignerMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\EventListener\ContextListener
     */
    private $listener;

    public function setUp()
    {
        $this->context = new Context();

        $this->contextBuilderMock = $this->createMock(ContextBuilderInterface::class);
        $this->uriSignerMock = $this->createMock(UriSigner::class);

        $this->listener = new ContextListener(
            $this->context,
            $this->contextBuilderMock,
            $this->uriSignerMock
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ContextListener::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ContextListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        $this->assertEquals(
            array(KernelEvents::REQUEST => 'onKernelRequest'),
            $this->listener->getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ContextListener::onKernelRequest
     */
    public function testOnKernelRequest()
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $this->contextBuilderMock
            ->expects($this->once())
            ->method('buildContext')
            ->with($this->equalTo($this->context));

        $this->uriSignerMock
            ->expects($this->never())
            ->method('check');

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ContextListener::onKernelRequest
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ContextListener::getUriContext
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ContextListener::getUri
     */
    public function testOnKernelRequestWithContextFromUri()
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $request->query->set('ngbmContext', array('var' => 'value'));

        $this->contextBuilderMock
            ->expects($this->never())
            ->method('buildContext');

        $this->uriSignerMock
            ->expects($this->once())
            ->method('check')
            ->with($this->equalTo($request->getRequestUri()))
            ->will($this->returnValue(true));

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);

        $this->assertEquals(array('var' => 'value'), $this->context->all());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ContextListener::onKernelRequest
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ContextListener::getUriContext
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ContextListener::getUri
     */
    public function testOnKernelRequestWithContextFromRequestAttribute()
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $request->attributes->set('ngbmContextUri', '/?ngbmContext%5Bvar%5D=value');
        $request->query->set('ngbmContext', array('var' => 'value'));

        $this->contextBuilderMock
            ->expects($this->never())
            ->method('buildContext');

        $this->uriSignerMock
            ->expects($this->once())
            ->method('check')
            ->with($this->equalTo($request->attributes->get('ngbmContextUri')))
            ->will($this->returnValue(true));

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);

        $this->assertEquals(array('var' => 'value'), $this->context->all());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ContextListener::onKernelRequest
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ContextListener::getUriContext
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ContextListener::getUri
     */
    public function testOnKernelRequestWithContextFromUriAndFailedHashCheck()
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $request->query->set('ngbmContext', array('var' => 'value'));

        $this->contextBuilderMock
            ->expects($this->never())
            ->method('buildContext');

        $this->uriSignerMock
            ->expects($this->once())
            ->method('check')
            ->with($this->equalTo($request->getRequestUri()))
            ->will($this->returnValue(false));

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);

        $this->assertEquals(array(), $this->context->all());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ContextListener::onKernelRequest
     */
    public function testOnKernelRequestInSubRequest()
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $request->query->set('ngbmContext', array('var' => 'value'));

        $this->contextBuilderMock
            ->expects($this->never())
            ->method('buildContext');

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::SUB_REQUEST);
        $this->listener->onKernelRequest($event);

        $this->assertEquals(array(), $this->context->all());
    }
}