<?php

namespace Netgen\BlockManager\View\Provider;

use Netgen\BlockManager\Layout\Type\LayoutType;
use Netgen\BlockManager\View\View\LayoutTypeView;

final class LayoutTypeViewProvider implements ViewProviderInterface
{
    public function provideView($value, array $parameters = array())
    {
        return new LayoutTypeView(
            array(
                'layoutType' => $value,
            )
        );
    }

    public function supports($value)
    {
        return $value instanceof LayoutType;
    }
}