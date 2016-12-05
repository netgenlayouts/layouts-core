<?php

namespace Netgen\BlockManager\Tests\Validator\Constraint;

use Netgen\BlockManager\Validator\Constraint\LayoutName;
use PHPUnit\Framework\TestCase;

class LayoutNameTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Validator\Constraint\LayoutName::validatedBy
     */
    public function testValidatedBy()
    {
        $constraint = new LayoutName();
        $this->assertEquals('ngbm_layout_name', $constraint->validatedBy());
    }
}