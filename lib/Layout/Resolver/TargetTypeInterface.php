<?php

namespace Netgen\BlockManager\Layout\Resolver;

interface TargetTypeInterface
{
    /**
     * Returns the target type identifier.
     *
     * @return string
     */
    public function getIdentifier();

    /**
     * Returns the constraints that will be used to validate the target value.
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    public function getConstraints();

    /**
     * Provides the value for the target to be used in matching process.
     *
     * @return mixed
     */
    public function provideValue();
}
