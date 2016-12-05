<?php

namespace Netgen\BlockManager\Tests\Core\Values\Collection;

use Netgen\BlockManager\API\Values\Collection\QueryCreateStruct;
use Netgen\BlockManager\Collection\QueryType;
use PHPUnit\Framework\TestCase;

class QueryCreateStructTest extends TestCase
{
    public function testDefaultProperties()
    {
        $queryCreateStruct = new QueryCreateStruct();

        $this->assertNull($queryCreateStruct->identifier);
        $this->assertNull($queryCreateStruct->queryType);
    }

    public function testSetProperties()
    {
        $queryCreateStruct = new QueryCreateStruct(
            array(
                'identifier' => 'my_query',
                'queryType' => new QueryType(),
            )
        );

        $this->assertEquals('my_query', $queryCreateStruct->identifier);
        $this->assertEquals(new QueryType(), $queryCreateStruct->queryType);
    }
}