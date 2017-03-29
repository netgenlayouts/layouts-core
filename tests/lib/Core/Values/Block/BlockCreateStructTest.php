<?php

namespace Netgen\BlockManager\Tests\Core\Values\Block;

use Netgen\BlockManager\API\Values\Block\BlockCreateStruct;
use Netgen\BlockManager\Block\BlockDefinition;
use PHPUnit\Framework\TestCase;

class BlockCreateStructTest extends TestCase
{
    public function testDefaultProperties()
    {
        $blockCreateStruct = new BlockCreateStruct();

        $this->assertNull($blockCreateStruct->definition);
        $this->assertNull($blockCreateStruct->viewType);
        $this->assertNull($blockCreateStruct->itemViewType);
        $this->assertNull($blockCreateStruct->name);
    }

    public function testSetProperties()
    {
        $blockCreateStruct = new BlockCreateStruct(
            array(
                'definition' => new BlockDefinition(),
                'viewType' => 'default',
                'itemViewType' => 'standard',
                'name' => 'My block',
            )
        );

        $this->assertEquals(new BlockDefinition(), $blockCreateStruct->definition);
        $this->assertEquals('default', $blockCreateStruct->viewType);
        $this->assertEquals('standard', $blockCreateStruct->itemViewType);
        $this->assertEquals('My block', $blockCreateStruct->name);
    }
}