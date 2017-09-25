<?php

namespace Netgen\BlockManager\View\Provider;

use Netgen\BlockManager\API\Values\Block\Block as APIBlock;
use Netgen\BlockManager\View\View\BlockView;

final class BlockViewProvider implements ViewProviderInterface
{
    public function provideView($valueObject, array $parameters = array())
    {
        $blockView = new BlockView(
            array(
                'block' => $valueObject,
            )
        );

        $httpCacheConfig = $valueObject->getConfig('http_cache');

        $blockView->setIsCacheable(
            $httpCacheConfig->getParameter('use_http_cache')->getValue()
        );

        $blockView->setSharedMaxAge(
            $httpCacheConfig->getParameter('shared_max_age')->getValue()
        );

        return $blockView;
    }

    public function supports($valueObject)
    {
        return $valueObject instanceof APIBlock;
    }
}
