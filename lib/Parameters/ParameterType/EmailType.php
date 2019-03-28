<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Parameters\ParameterType;

use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Validator\StrictEmailValidatorTrait;
use Symfony\Component\Validator\Constraints;

/**
 * Parameter type used to store and validate an e-mail address.
 */
final class EmailType extends ParameterType
{
    use StrictEmailValidatorTrait;

    public static function getIdentifier(): string
    {
        return 'email';
    }

    public function isValueEmpty(ParameterDefinition $parameterDefinition, $value): bool
    {
        return $value === null || $value === '';
    }

    protected function getValueConstraints(ParameterDefinition $parameterDefinition, $value): array
    {
        return [
            new Constraints\Type(['type' => 'string']),
            new Constraints\Email($this->getStrictEmailValidatorOption()),
        ];
    }
}
