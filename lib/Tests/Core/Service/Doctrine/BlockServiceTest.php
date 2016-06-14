<?php

namespace Netgen\BlockManager\Tests\Core\Service\Doctrine;

use Netgen\BlockManager\Tests\Core\Service\BlockServiceTest as BaseBlockServiceTest;

class BlockServiceTest extends BaseBlockServiceTest
{
    use TestCaseTrait;

    /**
     * Sets up the tests.
     */
    public function setUp()
    {
        $this->preparePersistence();

        parent::setUp();
    }

    public function tearDown()
    {
        $this->closeDatabaseConnection();
    }
}
