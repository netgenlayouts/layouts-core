<?php

namespace Netgen\BlockManager\Tests\Persistence\Values;

use Netgen\BlockManager\Persistence\Values\Page\Zone;
use Netgen\BlockManager\Persistence\Values\Value;
use PHPUnit\Framework\TestCase;

class ZoneTest extends TestCase
{
    public function testSetDefaultProperties()
    {
        $zone = new Zone();

        $this->assertNull($zone->identifier);
        $this->assertNull($zone->layoutId);
        $this->assertNull($zone->status);
        $this->assertNull($zone->linkedLayoutId);
        $this->assertNull($zone->linkedZoneIdentifier);
    }

    public function testSetProperties()
    {
        $zone = new Zone(
            array(
                'identifier' => 'left',
                'layoutId' => 84,
                'status' => Value::STATUS_PUBLISHED,
                'linkedLayoutId' => 24,
                'linkedZoneIdentifier' => 'top',
            )
        );

        $this->assertEquals('left', $zone->identifier);
        $this->assertEquals(84, $zone->layoutId);
        $this->assertEquals(Value::STATUS_PUBLISHED, $zone->status);
        $this->assertEquals(24, $zone->linkedLayoutId);
        $this->assertEquals('top', $zone->linkedZoneIdentifier);
    }
}