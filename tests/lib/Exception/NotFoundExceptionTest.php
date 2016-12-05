<?php

namespace Netgen\BlockManager\Tests\Exception;

use Netgen\BlockManager\Exception\NotFoundException;
use PHPUnit\Framework\TestCase;

class NotFoundExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Exception\NotFoundException::__construct
     */
    public function testExceptionMessage()
    {
        $exception = new NotFoundException('test', 1);

        $this->assertEquals(
            'Could not find test with identifier "1"',
            $exception->getMessage()
        );
    }
}