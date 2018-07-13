<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerAdminBundle\Form\Admin\Type;

use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\Form\AbstractType;
use Netgen\BlockManager\Form\ChoicesAsValuesTrait;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

final class ClearLayoutsCacheType extends AbstractType
{
    use ChoicesAsValuesTrait;

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setRequired(['layouts']);
        $resolver->setAllowedTypes('layouts', 'array');

        // @deprecated Replace with "Layout[]" allowed type when support for Symfony 2.8 ends
        $resolver->setAllowedValues(
            'layouts',
            function (array $layouts): bool {
                foreach ($layouts as $layout) {
                    if (!$layout instanceof Layout) {
                        return false;
                    }
                }

                return true;
            }
        );
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'layouts',
            ChoiceType::class,
            [
                'choices' => $options['layouts'],
                'choice_value' => 'id',
                'choice_label' => 'name',
                'translation_domain' => false,
                'choice_translation_domain' => false,
                'required' => true,
                'multiple' => true,
                'expanded' => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ] + $this->getChoicesAsValuesOption()
        );
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $layouts = [];

        foreach ($options['layouts'] as $layout) {
            $layouts[$layout->getId()] = $layout;
        }

        $view->vars['layouts'] = $layouts;
    }
}
