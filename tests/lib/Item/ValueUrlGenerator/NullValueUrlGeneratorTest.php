<?php

namespace Netgen\BlockManager\Tests\Item\ValueUrlGenerator;

use Netgen\BlockManager\Item\ValueUrlGenerator\NullValueUrlGenerator;
use PHPUnit\Framework\TestCase;
use stdClass;

final class NullValueUrlGeneratorTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Item\ValueUrlGenerator\NullValueUrlGenerator
     */
    private $urlGenerator;

    public function setUp()
    {
        $this->urlGenerator = new NullValueUrlGenerator();
    }

    /**
     * @covers \Netgen\BlockManager\Item\ValueUrlGenerator\NullValueUrlGenerator::generate
     */
    public function testGenerate()
    {
        $this->assertEquals('', $this->urlGenerator->generate(new stdClass()));
    }
}