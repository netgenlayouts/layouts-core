<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Block\BlockDefinition\Configuration;

use Netgen\BlockManager\Block\BlockDefinition\Configuration\Collection;
use PHPUnit\Framework\TestCase;

final class CollectionTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\Configuration\Collection
     */
    private $collection;

    public function setUp(): void
    {
        $this->collection = Collection::fromArray(
            [
                'identifier' => 'collection',
                'validItemTypes' => ['item'],
                'validQueryTypes' => ['query'],
            ]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Collection::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        $this->assertSame('collection', $this->collection->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Collection::getValidQueryTypes
     */
    public function testGetValidQueryTypes(): void
    {
        $this->assertSame(['query'], $this->collection->getValidQueryTypes());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Collection::isValidQueryType
     */
    public function testIsValidQueryType(): void
    {
        $this->assertTrue($this->collection->isValidQueryType('query'));
        $this->assertFalse($this->collection->isValidQueryType('query2'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Collection::isValidQueryType
     */
    public function testIsValidQueryTypeWithAllValidTypes(): void
    {
        $this->collection = Collection::fromArray(
            [
                'validQueryTypes' => null,
            ]
        );

        $this->assertTrue($this->collection->isValidQueryType('query'));
        $this->assertTrue($this->collection->isValidQueryType('query2'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Collection::isValidQueryType
     */
    public function testIsValidQueryTypeWithNoValidTypes(): void
    {
        $this->collection = Collection::fromArray(
            [
                'validQueryTypes' => [],
            ]
        );

        $this->assertFalse($this->collection->isValidQueryType('query'));
        $this->assertFalse($this->collection->isValidQueryType('query2'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Collection::getValidItemTypes
     */
    public function testGetValidItemTypes(): void
    {
        $this->assertSame(['item'], $this->collection->getValidItemTypes());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Collection::isValidItemType
     */
    public function testIsValidItemType(): void
    {
        $this->assertTrue($this->collection->isValidItemType('item'));
        $this->assertFalse($this->collection->isValidItemType('item2'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Collection::isValidItemType
     */
    public function testIsValidItemTypeWithAllValidTypes(): void
    {
        $this->collection = Collection::fromArray(
            [
                'validItemTypes' => null,
            ]
        );

        $this->assertTrue($this->collection->isValidItemType('item'));
        $this->assertTrue($this->collection->isValidItemType('item2'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Collection::isValidItemType
     */
    public function testIsValidItemTypeWithNoValidTypes(): void
    {
        $this->collection = Collection::fromArray(
            [
                'validItemTypes' => [],
            ]
        );

        $this->assertFalse($this->collection->isValidItemType('item'));
        $this->assertFalse($this->collection->isValidItemType('item2'));
    }
}
