<?php

namespace Netgen\BlockManager\Parameters\ParameterDefinition\Compound;

use Netgen\BlockManager\Parameters\CompoundParameterDefinition;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Boolean extends CompoundParameterDefinition
{
    /**
     * Returns the parameter type.
     *
     * @return string
     */
    public function getType()
    {
        return 'compound_boolean';
    }

    /**
     * Returns the default parameter value.
     *
     * @return mixed
     */
    public function getDefaultValue()
    {
        if ($this->isRequired && $this->defaultValue === null) {
            return false;
        }

        return parent::getDefaultValue();
    }

    /**
     * Configures the options for this parameter.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $optionsResolver
     */
    protected function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver->setDefault('reverse', false);
        $optionsResolver->setRequired(array('reverse'));
        $optionsResolver->setAllowedTypes('reverse', 'bool');
    }
}
