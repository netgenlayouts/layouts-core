<?php

namespace Netgen\BlockManager\Tests\Validator\Constraint\ConditionType;

use Netgen\BlockManager\Validator\Constraint\ConditionType\Time;
use PHPUnit\Framework\TestCase;

final class TimeTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Validator\Constraint\ConditionType\Time::validatedBy
     */
    public function testValidatedBy()
    {
        $constraint = new Time();
        $this->assertEquals('ngbm_condition_type_time', $constraint->validatedBy());
    }
}