<?php

namespace Netgen\BlockManager\Core\Service\Mapper;

use Netgen\BlockManager\Parameters\CompoundParameterInterface;
use Netgen\BlockManager\Parameters\ParameterCollectionInterface;
use Netgen\BlockManager\Parameters\ParameterValue;

final class ParameterMapper
{
    /**
     * Maps the parameter values based on provided collection of parameters.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterCollectionInterface $parameterCollection
     * @param array $parameterValues
     *
     * @return array
     */
    public function mapParameters(ParameterCollectionInterface $parameterCollection, array $parameterValues)
    {
        $mappedValues = array();

        foreach ($parameterCollection->getParameters() as $parameter) {
            $parameterName = $parameter->getName();
            $parameterType = $parameter->getType();

            $value = array_key_exists($parameterName, $parameterValues) ?
                $parameterType->fromHash($parameter, $parameterValues[$parameterName]) :
                $parameter->getDefaultValue();

            $mappedValues[$parameterName] = new ParameterValue(
                array(
                    'name' => $parameterName,
                    'parameter' => $parameter,
                    'value' => $value,
                    'isEmpty' => $parameterType->isValueEmpty($parameter, $value),
                )
            );

            if ($parameter instanceof CompoundParameterInterface) {
                $mappedValues = array_merge(
                    $mappedValues,
                    $this->mapParameters($parameter, $parameterValues)
                );
            }
        }

        return $mappedValues;
    }

    /**
     * Serializes the parameter values based on provided collection of parameters.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterCollectionInterface $parameterCollection
     * @param array $parameterValues
     * @param array $fallbackValues
     *
     * @return array
     */
    public function serializeValues(ParameterCollectionInterface $parameterCollection, array $parameterValues, array $fallbackValues = array())
    {
        $serializedValues = array();

        foreach ($parameterCollection->getParameters() as $parameter) {
            $parameterName = $parameter->getName();
            if (!array_key_exists($parameterName, $parameterValues)) {
                continue;
            }

            $serializedValues[$parameterName] = $parameter->getType()->toHash(
                $parameter,
                $parameterValues[$parameterName]
            );

            if ($parameter instanceof CompoundParameterInterface) {
                $serializedValues = array_merge(
                    $serializedValues,
                    $this->serializeValues($parameter, $parameterValues)
                );
            }
        }

        return $serializedValues + $fallbackValues;
    }

    /**
     * @param \Netgen\BlockManager\Parameters\ParameterCollectionInterface $parameterCollection
     * @param array $parameterValues
     *
     * @return array
     */
    public function extractUntranslatableParameters(ParameterCollectionInterface $parameterCollection, array $parameterValues)
    {
        $untranslatableParams = array();

        foreach ($parameterCollection->getParameters() as $paramName => $parameter) {
            if ($parameter->getOption('translatable')) {
                continue;
            }

            $untranslatableParams[$paramName] = isset($parameterValues[$paramName]) ?
                $parameterValues[$paramName] :
                null;

            if ($parameter instanceof CompoundParameterInterface) {
                foreach ($parameter->getParameters() as $subParamName => $subParameter) {
                    $untranslatableParams[$subParamName] = isset($parameterValues[$subParamName]) ?
                        $parameterValues[$subParamName] :
                        null;
                }
            }
        }

        return $untranslatableParams;
    }
}
