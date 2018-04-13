<?php

namespace Netgen\BlockManager\Tests\Form\Stubs;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Block\BlockUpdateStruct;
use Netgen\BlockManager\Form\TranslatableType;
use Netgen\BlockManager\Parameters\Form\Type\ParametersType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TranslatableTypeStub extends TranslatableType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired('block');
        $resolver->setAllowedTypes('block', Block::class);
        $resolver->setAllowedTypes('data', BlockUpdateStruct::class);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'name',
            TextType::class,
            array(
                'property_path' => 'name',
            )
        );

        $builder->add(
            'parameters',
            ParametersType::class,
            array(
                'inherit_data' => true,
                'label_prefix' => 'label_prefix',
                'property_path' => 'parameterValues',
                'parameter_collection' => $options['block']->getDefinition(),
            )
        );

        $this->disableFormsOnNonMainLocale($builder);
    }
}