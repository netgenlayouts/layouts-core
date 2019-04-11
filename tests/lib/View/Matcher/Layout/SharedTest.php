<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\Matcher\Layout;

use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Tests\View\Stubs\View;
use Netgen\Layouts\View\Matcher\Layout\Shared;
use Netgen\Layouts\View\View\LayoutView;
use PHPUnit\Framework\TestCase;

final class SharedTest extends TestCase
{
    /**
     * @var \Netgen\Layouts\View\Matcher\MatcherInterface
     */
    private $matcher;

    public function setUp(): void
    {
        $this->matcher = new Shared();
    }

    /**
     * @covers \Netgen\Layouts\View\Matcher\Layout\Shared::match
     * @dataProvider matchProvider
     */
    public function testMatch(array $config, bool $expected): void
    {
        $layout = Layout::fromArray(
            [
                'shared' => true,
            ]
        );

        $view = new LayoutView($layout);

        self::assertSame($expected, $this->matcher->match($view, $config));
    }

    public function matchProvider(): array
    {
        return [
            [[], true],
            [[true], true],
            [[false], false],
            [['something_else'], false],
        ];
    }

    /**
     * @covers \Netgen\Layouts\View\Matcher\Layout\Shared::match
     */
    public function testMatchWithNoLayoutView(): void
    {
        self::assertFalse($this->matcher->match(new View(new Value()), []));
    }
}
