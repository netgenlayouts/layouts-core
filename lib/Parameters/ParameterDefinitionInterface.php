<?php

namespace Netgen\BlockManager\Parameters;

interface ParameterDefinitionInterface
{
    /**
     * Returns the parameter type.
     *
     * @return string
     */
    public function getType();

    /**
     * Returns the parameter options.
     *
     * @return array
     */
    public function getOptions();

    /**
     * Returns if the parameter is required.
     *
     * @return bool
     */
    public function isRequired();

    /**
     * Returns the default parameter value.
     *
     * @return mixed
     */
    public function getDefaultValue();

    /**
     * @return array
     */
    public function getGroups();
}
