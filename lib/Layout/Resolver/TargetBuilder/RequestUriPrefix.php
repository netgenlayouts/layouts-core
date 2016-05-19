<?php

namespace Netgen\BlockManager\Layout\Resolver\TargetBuilder;

use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Netgen\BlockManager\Layout\Resolver\Target;
use Symfony\Component\HttpFoundation\Request;

class RequestUriPrefix implements TargetBuilderInterface
{
    use RequestStackAwareTrait;

    /**
     * Builds the target object that will be used to search for resolver rules.
     *
     * @return \Netgen\BlockManager\Layout\Resolver\Target|null
     */
    public function buildTarget()
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        if (!$currentRequest instanceof Request) {
            return;
        }

        return new Target(
            'request_uri_prefix',
            array($currentRequest->getRequestUri())
        );
    }
}
