<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output;

use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Tests\Transfer\Output\Visitor\Stubs\ValueVisitor;
use PHPUnit\Framework\TestCase;

final class StatusStringTraitTest extends TestCase
{
    /**
     * @var \Netgen\Layouts\Tests\Transfer\Output\Visitor\Stubs\ValueVisitor
     */
    private $visitor;

    public function setUp(): void
    {
        $this->visitor = new ValueVisitor();
    }

    /**
     * @covers \Netgen\Layouts\Transfer\Output\StatusStringTrait::getStatusString
     * @dataProvider visitProvider
     */
    public function testVisit(int $status, array $visitedValue): void
    {
        self::assertSame($visitedValue, $this->visitor->visit(Value::fromArray(['status' => $status])));
    }

    /**
     * @covers \Netgen\Layouts\Transfer\Output\StatusStringTrait::getStatusString
     */
    public function testVisitThrowsRuntimeExceptionWithInvalidStatus(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unknown status \'9999\'');

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
