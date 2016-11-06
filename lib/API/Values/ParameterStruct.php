<?php

namespace Netgen\BlockManager\API\Values;

use Netgen\BlockManager\Exception\InvalidArgumentException;
use Netgen\BlockManager\Parameters\CompoundParameterInterface;
use Netgen\BlockManager\Parameters\ParameterCollectionInterface;
use Netgen\BlockManager\Parameters\ParameterValue;
use Netgen\BlockManager\ValueObject;

abstract class ParameterStruct extends ValueObject
{
    /**
     * @var array
     */
    protected $parameters = array();

    /**
     * Sets the parameters to the struct.
     *
     * @param array $parameters
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * Sets the parameter to the struct.
     *
     * @param string $parameterName
     * @param mixed $parameterValue
     */
    public function setParameter($parameterName, $parameterValue)
    {
        $this->parameters[$parameterName] = $parameterValue;
    }

    /**
     * Returns all parameters from the struct.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Returns the parameter with provided name.
     *
     * @param string $parameterName
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If parameter does not exist
     *
     * @return mixed
     */
    public function getParameter($parameterName)
    {
        if (!$this->hasParameter($parameterName)) {
            throw new InvalidArgumentException(
                'parameterName',
                sprintf(
                    'Parameter "%s" does not exist in the struct.',
                    $parameterName
                )
            );
        }

        return $this->parameters[$parameterName];
    }

    /**
     * Returns if the struct has a parameter with provided name.
     *
     * @param string $parameterName
     *
     * @return bool
     */
    public function hasParameter($parameterName)
    {
        return array_key_exists($parameterName, $this->parameters);
    }

    /**
     * Fills the struct values based on provided list of parameters and values.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterCollectionInterface $parameterCollection
     * @param array $values
     * @param bool $useDefaults
     */
    public function fillValues(ParameterCollectionInterface $parameterCollection, $values = array(), $useDefaults = true)
    {
        foreach ($parameterCollection->getParameters() as $parameter) {
            $parameterName = $parameter->getName();
            $value = $useDefaults ? $parameter->getDefaultValue() : null;
            if (array_key_exists($parameterName, $values)) {
                $value = $values[$parameterName] instanceof ParameterValue ?
                    $values[$parameterName]->getValue() :
                    $values[$parameterName];
            }

            $this->setParameter($parameterName, is_object($value) ? clone $value : $value);

            if ($parameter instanceof CompoundParameterInterface) {
                $this->fillValues($parameter, $values, $useDefaults);
            }
        }
    }
}
