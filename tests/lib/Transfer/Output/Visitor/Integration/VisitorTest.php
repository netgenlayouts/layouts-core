<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration;

use Closure;
use Coduo\PHPMatcher\Factory\SimpleFactory;
use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Tests\Core\CoreTestCase;
use Netgen\Layouts\Tests\Transfer\Output\Visitor\Stubs\VisitorStub;
use Netgen\Layouts\Transfer\Output\OutputVisitor;
use Netgen\Layouts\Transfer\Output\VisitorInterface;
use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;

/**
 * @template T of object
 */
abstract class VisitorTest extends CoreTestCase
{
    /**
     * @var \Coduo\PHPMatcher\Factory\SimpleFactory
     */
    private $matcherFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->matcherFactory = new SimpleFactory();
    }

    /**
     * @param mixed $value
     * @param bool $accepted
     *
     * @dataProvider acceptDataProvider
     */
    public function testAccept($value, bool $accepted): void
    {
        self::assertSame($accepted, $this->getVisitor()->accept($value));
    }

    /**
     * @param mixed $value
     * @param string $fixturePath
     *
     * @dataProvider visitDataProvider
     */
    public function testVisit($value, string $fixturePath): void
    {
        $fixturePath = __DIR__ . '/../../../_fixtures/output/' . $fixturePath;

        if (!file_exists($fixturePath)) {
            throw new RuntimeException(sprintf('%s file does not exist.', $fixturePath));
        }

        if ($value instanceof Closure) {
            // We're using closures as values in case data providers need dependencies
            // from setUp method, because data providers are executed before the setUp method
            // This rebinds the closure to $this, to get the instantiated dependencies
            // https://github.com/sebastianbergmann/phpunit/issues/3097
            $value = $value->call($this);
        }

        $expectedData = trim((string) file_get_contents($fixturePath));
        $visitedData = $this->getVisitor()->visit($value, new OutputVisitor([new VisitorStub()]));

        $matcher = $this->matcherFactory->createMatcher();
        $matchResult = $matcher->match($visitedData, json_decode($expectedData, true));

        if (!$matchResult) {
            $visitedData = (string) json_encode($visitedData, JSON_PRETTY_PRINT);
            $differ = new Differ(new UnifiedDiffOutputBuilder("--- Expected\n+++ Actual\n", false));
            self::fail($matcher->getError() . PHP_EOL . $differ->diff($expectedData, $visitedData));
        }

        // We fake the assertion count to disable risky warning
        $this->addToAssertionCount(1);
    }

    /**
     * Returns the visitor under test.
     *
     * @return \Netgen\Layouts\Transfer\Output\VisitorInterface<T>
     */
    abstract public function getVisitor(): VisitorInterface;

    /**
     * Provides data for testing VisitorInterface::accept method.
     */
    abstract public function acceptDataProvider(): array;

    /**
     * Provides data for testing VisitorInterface::visit method.
     */
    abstract public function visitDataProvider(): array;
}
