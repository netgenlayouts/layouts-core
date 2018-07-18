<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\HttpCache\Layout;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\HttpCache\Layout\IdProvider;
use PHPUnit\Framework\TestCase;

final class IdProviderTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $layoutServiceMock;

    /**
     * @var \Netgen\BlockManager\HttpCache\Layout\IdProvider
     */
    private $idProvider;

    public function setUp(): void
    {
        $this->layoutServiceMock = $this->createMock(LayoutService::class);

        $this->idProvider = new IdProvider($this->layoutServiceMock);
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\Layout\IdProvider::__construct
     * @covers \Netgen\BlockManager\HttpCache\Layout\IdProvider::provideIds
     */
    public function testProvideIds(): void
    {
        $this->layoutServiceMock
            ->expects($this->once())
            ->method('loadLayout')
            ->with($this->identicalTo(42))
            ->will(
                $this->returnValue(
                    Layout::fromArray(
                        [
                            'id' => 42,
                            'shared' => false,
                        ]
                    )
                )
            );

        $providedIds = $this->idProvider->provideIds(42);

        $this->assertSame([42], $providedIds);
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\Layout\IdProvider::provideIds
     */
    public function testProvideIdsWithNonExistingLayout(): void
    {
        $this->layoutServiceMock
            ->expects($this->once())
            ->method('loadLayout')
            ->with($this->identicalTo(42))
            ->will(
                $this->throwException(
                    new NotFoundException('layout', 42)
                )
            );

        $providedIds = $this->idProvider->provideIds(42);

        $this->assertSame([42], $providedIds);
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\Layout\IdProvider::provideIds
     */
    public function testProvideIdsWithSharedLayout(): void
    {
        $sharedLayout = Layout::fromArray(
            [
                'id' => 42,
                'shared' => true,
            ]
        );

        $this->layoutServiceMock
            ->expects($this->at(0))
            ->method('loadLayout')
            ->with($this->identicalTo(42))
            ->will($this->returnValue($sharedLayout));

        $this->layoutServiceMock
            ->expects($this->at(1))
            ->method('loadRelatedLayouts')
            ->with($this->identicalTo($sharedLayout))
            ->will(
                $this->returnValue(
                    [
                        Layout::fromArray(
                            [
                                'id' => 43,
                            ]
                        ),
                        Layout::fromArray(
                            [
                                'id' => 44,
                            ]
                        ),
                    ]
                )
            );

        $providedIds = $this->idProvider->provideIds(42);

        $this->assertSame([42, 43, 44], $providedIds);
    }
}
