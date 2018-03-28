<?php

namespace Netgen\BlockManager\Collection\Item\ConfigDefinition\Handler;

use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\API\Values\Config\ConfigAwareValue;
use Netgen\BlockManager\Config\ConfigDefinitionHandlerInterface;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use Netgen\BlockManager\Parameters\ParameterType;

/**
 * This handler specifies the model of visibility configuration within
 * the collection items.
 */
final class VisibilityConfigHandler implements ConfigDefinitionHandlerInterface
{
    public function buildParameters(ParameterBuilderInterface $builder)
    {
        $builder->add(
            'visibility_status',
            ParameterType\ChoiceType::class,
            array(
                'expanded' => true,
                'options' => array(
                    'config.collection_item.visibility.visibility_status.visible' => Item::VISIBILITY_VISIBLE,
                    'config.collection_item.visibility.visibility_status.hidden' => Item::VISIBILITY_HIDDEN,
                    'config.collection_item.visibility.visibility_status.scheduled' => Item::VISIBILITY_SCHEDULED,
                ),
                'default_value' => Item::VISIBILITY_VISIBLE,
            )
        );

        $builder->add(
            'visible_from',
            ParameterType\DateTimeType::class
        );

        $builder->add(
            'visible_to',
            ParameterType\DateTimeType::class
        );
    }

    public function isEnabled(ConfigAwareValue $configAwareValue)
    {
        if (!$configAwareValue instanceof Item) {
            return false;
        }

        return true;
    }
}