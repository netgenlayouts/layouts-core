<?php

namespace Netgen\BlockManager\Serializer\Values;

interface ViewInterface extends ValueInterface
{
    /**
     * Sets the view parameters.
     *
     * @param array $viewParameters
     */
    public function setViewParameters(array $viewParameters = array());

    /**
     * Returns the parameters transferred to the view.
     *
     * @return array
     */
    public function getViewParameters();
}
