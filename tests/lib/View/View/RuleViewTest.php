<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\View;

use Netgen\BlockManager\Core\Values\LayoutResolver\Rule;
use Netgen\BlockManager\View\View\RuleView;
use PHPUnit\Framework\TestCase;

final class RuleViewTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\API\Values\LayoutResolver\Rule
     */
    private $rule;

    /**
     * @var \Netgen\BlockManager\View\View\RuleViewInterface
     */
    private $view;

    public function setUp(): void
    {
        $this->rule = new Rule(['id' => 42]);

        $this->view = new RuleView(
            [
                'rule' => $this->rule,
            ]
        );

        $this->view->addParameter('param', 'value');
        $this->view->addParameter('rule', 42);
    }

    /**
     * @covers \Netgen\BlockManager\View\View\RuleView::__construct
     * @covers \Netgen\BlockManager\View\View\RuleView::getRule
     */
    public function testGetRule(): void
    {
        $this->assertSame($this->rule, $this->view->getRule());
        $this->assertSame(
            [
                'rule' => $this->rule,
                'param' => 'value',
            ],
            $this->view->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\View\RuleView::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        $this->assertSame('rule', $this->view->getIdentifier());
    }
}
