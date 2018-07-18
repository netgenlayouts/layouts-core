<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Transfer\Output;

use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\Transfer\Output\Visitor\Stubs\ValueVisitor;
use PHPUnit\Framework\TestCase;

final class StatusStringTraitTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Tests\Transfer\Output\Visitor\Stubs\ValueVisitor
     */
    private $visitor;

    public function setUp(): void
    {
        $this->visitor = new ValueVisitor();
    }

    /**
     * @covers \Netgen\BlockManager\Transfer\Output\StatusStringTrait::getStatusString
     * @dataProvider visitProvider
     */
    public function testVisit(int $status, array $visitedValue): void
    {
        $this->assertSame($visitedValue, $this->visitor->visit(Value::fromArray(['status' => $status])));
    }

    /**
     * @covers \Netgen\BlockManager\Transfer\Output\StatusStringTrait::getStatusString
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Unknown status '9999'
     */
    public function testVisitThrowsRuntimeExceptionWithInvalidStatus(): void
    {
        $this->visitor->visit(Value::fromArray(['status' => 9999]));
    }

    public function visitProvider(): array
    {
        return [
            [Value::STATUS_DRAFT, ['status' => 'DRAFT']],
            [Value::STATUS_PUBLISHED, ['status' => 'PUBLISHED']],
            [Value::STATUS_ARCHIVED, ['status' => 'ARCHIVED']],
        ];
    }
}
