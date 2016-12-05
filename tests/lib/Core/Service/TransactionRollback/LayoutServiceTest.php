<?php

namespace Netgen\BlockManager\Tests\Core\Service\TransactionRollback;

use Exception;
use Netgen\BlockManager\API\Values\Page\LayoutCreateStruct;
use Netgen\BlockManager\API\Values\Page\LayoutUpdateStruct;
use Netgen\BlockManager\Configuration\LayoutType\LayoutType;
use Netgen\BlockManager\Core\Values\Page\Layout;
use Netgen\BlockManager\Core\Values\Page\Zone;
use Netgen\BlockManager\Persistence\Values\Page\Layout as PersistenceLayout;
use Netgen\BlockManager\Persistence\Values\Page\Zone as PersistenceZone;

class LayoutServiceTest extends ServiceTestCase
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    protected $layoutService;

    /**
     * Sets up the tests.
     */
    public function setUp()
    {
        parent::setUp();

        $this->layoutService = $this->createLayoutService();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::linkZone
     * @expectedException \Exception
     */
    public function testLinkZone()
    {
        $this->layoutHandlerMock
            ->expects($this->at(0))
            ->method('loadLayout')
            ->will($this->returnValue(new PersistenceLayout(array('shared' => false))));

        $this->layoutHandlerMock
            ->expects($this->at(1))
            ->method('loadZone')
            ->will($this->returnValue(new PersistenceZone(array('layoutId' => 1))));

        $this->layoutHandlerMock
            ->expects($this->at(2))
            ->method('loadLayout')
            ->will($this->returnValue(new PersistenceLayout(array('shared' => true))));

        $this->layoutHandlerMock
            ->expects($this->at(3))
            ->method('loadZone')
            ->will($this->returnValue(new PersistenceZone(array('layoutId' => 2))));

        $this->layoutHandlerMock
            ->expects($this->at(4))
            ->method('updateZone')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutService->linkZone(
            new Zone(array('published' => false)),
            new Zone(array('published' => true))
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::unlinkZone
     * @expectedException \Exception
     */
    public function testUnlinkZone()
    {
        $this->layoutHandlerMock
            ->expects($this->at(0))
            ->method('loadZone')
            ->will($this->returnValue(new PersistenceZone()));

        $this->layoutHandlerMock
            ->expects($this->at(1))
            ->method('updateZone')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutService->unlinkZone(new Zone(array('published' => false)));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createLayout
     * @expectedException \Exception
     */
    public function testCreateLayout()
    {
        $this->layoutHandlerMock
            ->expects($this->at(0))
            ->method('layoutNameExists')
            ->will($this->returnValue(false));

        $this->layoutHandlerMock
            ->expects($this->at(1))
            ->method('createLayout')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutService->createLayout(
            new LayoutCreateStruct(
                array('layoutType' => new LayoutType())
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::updateLayout
     * @expectedException \Exception
     */
    public function testUpdateLayout()
    {
        $this->layoutHandlerMock
            ->expects($this->at(0))
            ->method('loadLayout')
            ->will($this->returnValue(new PersistenceLayout()));

        $this->layoutHandlerMock
            ->expects($this->at(1))
            ->method('layoutNameExists')
            ->will($this->returnValue(false));

        $this->layoutHandlerMock
            ->expects($this->at(2))
            ->method('updateLayout')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutService->updateLayout(
            new Layout(array('published' => false)),
            new LayoutUpdateStruct(array('name' => 'New name'))
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::copyLayout
     * @expectedException \Exception
     */
    public function testCopyLayout()
    {
        $this->layoutHandlerMock
            ->expects($this->at(0))
            ->method('layoutNameExists')
            ->will($this->returnValue(false));

        $this->layoutHandlerMock
            ->expects($this->at(1))
            ->method('loadLayout')
            ->will($this->returnValue(new PersistenceLayout()));

        $this->layoutHandlerMock
            ->expects($this->at(2))
            ->method('copyLayout')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutService->copyLayout(new Layout(), 'New name');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::createDraft
     * @expectedException \Exception
     */
    public function testCreateDraft()
    {
        $this->layoutHandlerMock
            ->expects($this->at(0))
            ->method('loadLayout')
            ->will($this->returnValue(new PersistenceLayout()));

        $this->layoutHandlerMock
            ->expects($this->at(1))
            ->method('layoutExists')
            ->will($this->returnValue(false));

        $this->layoutHandlerMock
            ->expects($this->at(2))
            ->method('deleteLayout')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutService->createDraft(new Layout(array('published' => true)));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::discardDraft
     * @expectedException \Exception
     */
    public function testDiscardDraft()
    {
        $this->layoutHandlerMock
            ->expects($this->at(0))
            ->method('loadLayout')
            ->will($this->returnValue(new PersistenceLayout()));

        $this->layoutHandlerMock
            ->expects($this->at(1))
            ->method('deleteLayout')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutService->discardDraft(new Layout(array('published' => false)));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::publishLayout
     * @expectedException \Exception
     */
    public function testPublishLayout()
    {
        $this->layoutHandlerMock
            ->expects($this->at(0))
            ->method('loadLayout')
            ->will($this->returnValue(new PersistenceLayout()));

        $this->layoutHandlerMock
            ->expects($this->at(1))
            ->method('deleteLayout')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutService->publishLayout(new Layout(array('published' => false)));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutService::deleteLayout
     * @expectedException \Exception
     */
    public function testDeleteLayout()
    {
        $this->layoutHandlerMock
            ->expects($this->at(0))
            ->method('loadLayout')
            ->will($this->returnValue(new PersistenceLayout()));

        $this->layoutHandlerMock
            ->expects($this->at(1))
            ->method('deleteLayout')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutService->deleteLayout(new Layout());
    }
}