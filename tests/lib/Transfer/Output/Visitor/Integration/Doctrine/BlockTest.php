<?php

namespace Netgen\BlockManager\Tests\Transfer\Output\Visitor\Integration\Doctrine;

use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;
use Netgen\BlockManager\Tests\Transfer\Output\Visitor\Integration\BlockTest as BaseBlockTest;

/**
 * @covers \Netgen\BlockManager\Transfer\Output\Visitor\Block
 */
final class BlockTest extends BaseBlockTest
{
    use TestCaseTrait;

    public function tearDown()
    {
        $this->closeDatabase();
    }

    /**
     * Prepares the persistence handler used in tests.
     */
    public function preparePersistence()
    {
        $this->persistenceHandler = $this->createPersistenceHandler();
    }
}
