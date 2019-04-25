<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\Collection;

use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\API\Values\Collection\CollectionList;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;
use TypeError;

final class CollectionListTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\API\Values\Collection\CollectionList::__construct
     */
    public function testConstructorWithInvalidType(): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(
            sprintf(
                'Argument 1 passed to %s::%s\{closure}() must be an instance of %s, instance of %s given',
                CollectionList::class,
                str_replace('\CollectionList', '', CollectionList::class),
                Collection::class,
                stdClass::class
            )
        );

        new CollectionList([new Collection(), new stdClass(), new Collection()]);
    }

    /**
     * @covers \Netgen\Layouts\API\Values\Collection\CollectionList::__construct
     * @covers \Netgen\Layouts\API\Values\Collection\CollectionList::getCollections
     */
    public function testGetCollections(): void
    {
        $collections = [new Collection(), new Collection()];

        self::assertSame($collections, (new CollectionList($collections))->getCollections());
    }

    /**
     * @covers \Netgen\Layouts\API\Values\Collection\CollectionList::getCollectionIds
     */
    public function testGetCollectionIds(): void
    {
        $uuid1 = Uuid::uuid4();
        $uuid2 = Uuid::uuid4();

        $collections = [Collection::fromArray(['id' => $uuid1]), Collection::fromArray(['id' => $uuid2])];

        self::assertSame([$uuid1, $uuid2], (new CollectionList($collections))->getCollectionIds());
    }
}
