<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters\Form\Type\DataMapper;

use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\Value\LinkValue;
use Symfony\Component\Form\DataMapperInterface;

/**
 * Mapper used to convert to and from the LinkValue object to the Symfony form structure.
 */
final class LinkDataMapper implements DataMapperInterface
{
    /**
     * @var \Netgen\Layouts\Parameters\ParameterDefinition
     */
    private $parameterDefinition;

    public function __construct(ParameterDefinition $parameterDefinition)
    {
        $this->parameterDefinition = $parameterDefinition;
    }

    public function mapDataToForms($viewData, $forms): void
    {
        if (!$viewData instanceof LinkValue) {
            return;
        }

        $forms = iterator_to_array($forms);

        $forms['link_type']->setData($viewData->getLinkType());
        $forms['link_suffix']->setData($viewData->getLinkSuffix());
        $forms['new_window']->setData($viewData->getNewWindow());

        if (isset($forms[$viewData->getLinkType()])) {
            $forms[$viewData->getLinkType()]->setData($viewData->getLink());
        }
    }

    public function mapFormsToData($forms, &$viewData): void
    {
        $forms = iterator_to_array($forms);
        $linkType = $forms['link_type']->getData() ?? '';

        $viewData = null;
        if ($linkType !== '') {
            $viewData = [
                'link_type' => $linkType,
                'link' => isset($forms[$linkType]) ? $forms[$linkType]->getData() : null,
                'link_suffix' => $forms['link_suffix']->getData(),
                'new_window' => (bool) $forms['new_window']->getData(),
            ];
        }

        $viewData = $this->parameterDefinition->getType()->fromHash($this->parameterDefinition, $viewData);
    }
}
