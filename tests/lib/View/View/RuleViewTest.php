<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\View;

use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\View\View\RuleView;
use PHPUnit\Framework\TestCase;

final class RuleViewTest extends TestCase
{
    /**
     * @var \Netgen\Layouts\API\Values\LayoutResolver\Rule
     */
    private $rule;

    /**
     * @var \Netgen\Layouts\View\View\RuleViewInterface
     */
    private $view;

    public function setUp(): void
    {
        $this->rule = Rule::fromArray(['id' => 42]);

        $this->view = new RuleView($this->rule);

        $this->view->addParameter('param', 'value');
        $this->view->addParameter('rule', 42);
    }

    /**
     * @covers \Netgen\Layouts\View\View\RuleView::__construct
     * @covers \Netgen\Layouts\View\View\RuleView::getRule
     */
    public function testGetRule(): void
    {
        self::assertSame($this->rule, $this->view->getRule());
        self::assertSame(
            [
                'rule' => $this->rule,
                'param' => 'value',
            ],
            $this->view->getParameters()
        );
    }

    /**
     * @covers \Netgen\Layouts\View\View\RuleView::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        self::assertSame('rule', $this->view::getIdentifier());
    }
}
