<?php

declare(strict_types=1);

namespace Netgen\Layouts\Validator\Structs;

use Closure;
use Generator;
use Netgen\Layouts\API\Values\ParameterStruct;
use Netgen\Layouts\Parameters\CompoundParameterDefinition;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ParameterDefinitionCollectionInterface;
use Netgen\Layouts\Validator\Constraint\Structs\ParameterStruct as ParameterStructConstraint;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use function iterator_to_array;

/**
 * Validates the parameters stored inside the value
 * implementing ParameterStruct interface.
 */
final class ParameterStructValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ParameterStructConstraint) {
            throw new UnexpectedTypeException($constraint, ParameterStructConstraint::class);
        }

        if (!$value instanceof ParameterStruct) {
            throw new UnexpectedTypeException($value, ParameterStruct::class);
        }

        /** @var \Symfony\Component\Validator\Validator\ContextualValidatorInterface $validator */
        $validator = $this->context->getValidator()->inContext($this->context);

        // First we validate the value format with constraints coming from the parameter type
        $validator->validate(
            $value->getParameterValues(),
            new Constraints\Collection(
                [
                    'fields' => iterator_to_array($this->buildConstraintFields($value, $constraint)),
                    'allowMissingFields' => $constraint->allowMissingFields,
                ]
            )
        );

        $allParameterValues = iterator_to_array(
            $this->getAllValues($constraint->parameterDefinitions, $value)
        );

        // Then we validate with runtime constraints coming from parameter definition
        // allowing for validation of values dependent on other parameter struct values
        foreach ($constraint->parameterDefinitions->getParameterDefinitions() as $parameterDefinition) {
            $parameterValue = $value->getParameterValue($parameterDefinition->getName());

            $validator->atPath('[' . $parameterDefinition->getName() . ']')->validate(
                $parameterValue,
                iterator_to_array(
                    $this->getRuntimeParameterConstraints(
                        $parameterDefinition,
                        $parameterValue,
                        $allParameterValues
                    )
                )
            );
        }
    }

    /**
     * Builds the "fields" array of the Collection constraint from provided parameters
     * and parameter values.
     *
     * @return \Generator<string, \Symfony\Component\Validator\Constraint[]|\Symfony\Component\Validator\Constraint>
     */
    private function buildConstraintFields(
        ParameterStruct $parameterStruct,
        ParameterStructConstraint $constraint
    ): Generator {
        foreach ($constraint->parameterDefinitions->getParameterDefinitions() as $parameterDefinition) {
            $constraints = $this->getParameterConstraints($parameterDefinition, $parameterStruct);

            if (!$parameterDefinition->isRequired()) {
                $constraints = new Constraints\Optional($constraints);
            }

            yield $parameterDefinition->getName() => $constraints;

            if ($parameterDefinition instanceof CompoundParameterDefinition) {
                foreach ($parameterDefinition->getParameterDefinitions() as $subParameterDefinition) {
                    yield $subParameterDefinition->getName() => new Constraints\Optional(
                        // Sub parameter values are always optional (either missing or set to null)
                        // so we don't have to validate empty values
                        $this->getParameterConstraints($subParameterDefinition, $parameterStruct, false)
                    );
                }
            }
        }
    }

    /**
     * Returns all constraints applied on a parameter coming directly from parameter type.
     *
     * If $validateEmptyValue is false, values equal to null will not be validated
     * and will simply return an empty array of constraints.
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    private function getParameterConstraints(
        ParameterDefinition $parameterDefinition,
        ParameterStruct $parameterStruct,
        bool $validateEmptyValue = true
    ): array {
        $parameterValue = $parameterStruct->getParameterValue(
            $parameterDefinition->getName()
        );

        if (!$validateEmptyValue && $parameterValue === null) {
            return [];
        }

        return $parameterDefinition->getType()->getConstraints(
            $parameterDefinition,
            $parameterValue
        );
    }

    /**
     * Returns all constraints applied on a parameter coming from the parameter definition.
     *
     * @param mixed $parameterValue
     * @param array<string, mixed> $allParameterValues
     *
     * @return \Generator<\Symfony\Component\Validator\Constraint>
     */
    private function getRuntimeParameterConstraints(
        ParameterDefinition $parameterDefinition,
        $parameterValue,
        array $allParameterValues
    ): Generator {
        foreach ($parameterDefinition->getConstraints() as $constraint) {
            if ($constraint instanceof Closure) {
                $constraint = $constraint($parameterValue, $allParameterValues, $parameterDefinition);
            }

            if ($constraint instanceof Constraint) {
                yield $constraint;
            }
        }
    }

    /**
     * @return \Generator<string, mixed>
     */
    private function getAllValues(
        ParameterDefinitionCollectionInterface $definitions,
        ParameterStruct $parameterStruct
    ): Generator {
        foreach ($definitions->getParameterDefinitions() as $parameterDefinition) {
            yield $parameterDefinition->getName() => null;

            if ($parameterDefinition instanceof CompoundParameterDefinition) {
                foreach ($parameterDefinition->getParameterDefinitions() as $subParameterDefinition) {
                    yield $subParameterDefinition->getName() => null;
                }
            }
        }

        yield from $parameterStruct->getParameterValues();
    }
}
