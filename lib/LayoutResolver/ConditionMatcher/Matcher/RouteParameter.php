<?php

namespace Netgen\BlockManager\LayoutResolver\ConditionMatcher\Matcher;

use Netgen\BlockManager\LayoutResolver\ConditionMatcher\ConditionMatcherInterface;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Symfony\Component\HttpFoundation\Request;

class RouteParameter implements ConditionMatcherInterface
{
    use RequestStackAwareTrait;

    /**
     * Returns the unique identifier of the condition this matcher matches.
     *
     * @return string
     */
    public function getConditionIdentifier()
    {
        return 'route_parameter';
    }

    /**
     * Returns if this condition matches provided value identifier and values.
     *
     * @param string $valueIdentifier
     * @param array $values
     *
     * @return bool
     */
    public function matches($valueIdentifier, array $values)
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        if (!$currentRequest instanceof Request) {
            return false;
        }

        if (empty($valueIdentifier)) {
            return false;
        }

        if (empty($values)) {
            return false;
        }

        $routeParameters = $currentRequest->attributes->get('_route_params', array());
        if (!isset($routeParameters[$valueIdentifier])) {
            return false;
        }

        return in_array($routeParameters[$valueIdentifier], $values);
    }
}
