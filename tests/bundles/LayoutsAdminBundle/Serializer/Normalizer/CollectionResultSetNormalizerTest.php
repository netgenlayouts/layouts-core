<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Serializer\Normalizer;

use Doctrine\Common\Collections\ArrayCollection;
use Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\ArrayValueNormalizer;
use Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\CollectionResultSetNormalizer;
use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\Value;
use Netgen\Bundle\LayoutsAdminBundle\Tests\Serializer\Stubs\NormalizerStub;
use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\API\Values\Collection\Item;
use Netgen\Layouts\Collection\Result\ManualItem;
use Netgen\Layouts\Collection\Result\Result;
use Netgen\Layouts\Collection\Result\ResultSet;
use Netgen\Layouts\Tests\API\Stubs\Value as APIValue;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Serializer;

final class CollectionResultSetNormalizerTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\CollectionResultSetNormalizer
     */
    private $normalizer;

    protected function setUp(): void
    {
        $this->normalizer = new CollectionResultSetNormalizer();
        $this->normalizer->setNormalizer(new Serializer([new ArrayValueNormalizer(), new NormalizerStub()]));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\CollectionResultSetNormalizer::buildValues
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\CollectionResultSetNormalizer::getOverflowItems
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\CollectionResultSetNormalizer::normalize
     */
    public function testNormalize(): void
    {
        $item1 = Item::fromArray(['position' => 0]);
        $item2 = Item::fromArray(['position' => 1]);
        $item3 = Item::fromArray(['position' => 2]);
        $item4 = Item::fromArray(['position' => 3]);

        $result1 = new Result(0, new ManualItem($item2));
        $result2 = new Result(1, new ManualItem($item3));

        $collection = Collection::fromArray(
            [
                'id' => Uuid::uuid4(),
                'offset' => 3,
                'limit' => 6,
                'items' => new ArrayCollection([$item1, $item2, $item3, $item4]),
            ]
        );

        $result = ResultSet::fromArray(
            [
                'collection' => $collection,
                'results' => [$result1, $result2],
            ]
        );

        self::assertSame(
            [
                'collection_id' => $collection->getId()->toString(),
                'collection_type' => Collection::TYPE_MANUAL,
                'offset' => $collection->getOffset(),
                'limit' => $collection->getLimit(),
                'items' => ['data', 'data'],
                'overflow_items' => ['data', 'data'],
            ],
            $this->normalizer->normalize(new Value($result))
        );
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\CollectionResultSetNormalizer::supportsNormalization
     * @dataProvider supportsNormalizationDataProvider
     */
    public function testSupportsNormalization($data, bool $expected): void
    {
        self::assertSame($expected, $this->normalizer->supportsNormalization($data));
    }

    public function supportsNormalizationDataProvider(): array
    {
        return [
            [null, false],
            [true, false],
            [false, false],
            ['block', false],
            [[], false],
            [42, false],
            [42.12, false],
            [new APIValue(), false],
            [new ResultSet(), false],
            [new Value(new APIValue()), false],
            [new Value(new ResultSet()), true],
        ];
    }
}
