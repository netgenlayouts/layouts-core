<?php

namespace Netgen\BlockManager\Tests\Core\Service\Doctrine;

use Netgen\BlockManager\Tests\Core\Service\LayoutResolverServiceTest as BaseLayoutResolverServiceTest;
use Netgen\BlockManager\Tests\Persistence\Doctrine\TestCaseTrait;

class LayoutResolverServiceTest extends BaseLayoutResolverServiceTest
{
    use TestCaseTrait;

    public function tearDown()
    {
        $this->closeDatabase();
    }

    /**
     * Prepares the prerequisites for using services in tests.
     */
    public function preparePersistence()
    {
        $this->persistenceHandler = $this->createPersistenceHandler();
    }
}