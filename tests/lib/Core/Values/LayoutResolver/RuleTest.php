<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Values\LayoutResolver;

use Doctrine\Common\Collections\ArrayCollection;
use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Core\Values\LayoutResolver\Condition;
use Netgen\BlockManager\Core\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Core\Values\LayoutResolver\Target;
use PHPUnit\Framework\TestCase;

final class RuleTest extends TestCase
{
    public function testInstance(): void
    {
        $this->assertInstanceOf(Value::class, new Rule());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::__construct
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::getConditions
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::getTargets
     */
    public function testSetDefaultProperties(): void
    {
        $rule = new Rule();

        $this->assertSame([], $rule->getTargets());
        $this->assertSame([], $rule->getConditions());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::__construct
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::getComment
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::getConditions
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::getId
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::getLayout
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::getPriority
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::getTargets
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::isEnabled
     */
    public function testSetProperties(): void
    {
        $layout = Layout::fromArray(['id' => 24]);

        $rule = Rule::fromArray(
            [
                'id' => 42,
                'layout' => $layout,
                'priority' => 13,
                'enabled' => true,
                'comment' => 'Comment',
                'targets' => new ArrayCollection([new Target(), new Target()]),
                'conditions' => new ArrayCollection([new Condition()]),
            ]
        );

        $this->assertSame(42, $rule->getId());
        $this->assertSame($layout, $rule->getLayout());
        $this->assertSame(13, $rule->getPriority());
        $this->assertTrue($rule->isEnabled());
        $this->assertSame('Comment', $rule->getComment());
        $this->assertCount(2, $rule->getTargets());
        $this->assertCount(1, $rule->getConditions());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::canBeEnabled
     */
    public function testCanBeEnabled(): void
    {
        $rule = Rule::fromArray(
            [
                'status' => Rule::STATUS_PUBLISHED,
                'layout' => Layout::fromArray(['id' => 24]),
                'targets' => new ArrayCollection([new Target(), new Target()]),
            ]
        );

        $this->assertTrue($rule->canBeEnabled());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::canBeEnabled
     */
    public function testCanBeEnabledWhenNotPublished(): void
    {
        $rule = Rule::fromArray(
            [
                'status' => Rule::STATUS_DRAFT,
                'layout' => Layout::fromArray(['id' => 24]),
                'targets' => new ArrayCollection([new Target(), new Target()]),
            ]
        );

        $this->assertFalse($rule->canBeEnabled());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::canBeEnabled
     */
    public function testCanBeEnabledWithNoLayout(): void
    {
        $rule = Rule::fromArray(
            [
                'status' => Rule::STATUS_PUBLISHED,
                'layout' => null,
                'targets' => new ArrayCollection([new Target(), new Target()]),
            ]
        );

        $this->assertFalse($rule->canBeEnabled());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Rule::canBeEnabled
     */
    public function testCanBeEnabledWithNoTargets(): void
    {
        $rule = Rule::fromArray(
            [
                'status' => Rule::STATUS_PUBLISHED,
                'layout' => Layout::fromArray(['id' => 24]),
                'targets' => new ArrayCollection(),
            ]
        );

        $this->assertFalse($rule->canBeEnabled());
    }
}
