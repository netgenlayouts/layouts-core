<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\EventListener\LayoutView;

use Netgen\Layouts\Event\CollectViewParametersEvent;
use Netgen\Layouts\Event\LayoutsEvents;
use Netgen\Layouts\HttpCache\ClientInterface;
use Netgen\Layouts\HttpCache\NullClient;
use Netgen\Layouts\View\View\LayoutViewInterface;
use Netgen\Layouts\View\ViewInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class CacheEnabledListener implements EventSubscriberInterface
{
    /**
     * @var bool
     */
    private $cacheEnabled;

    public function __construct(ClientInterface $httpCacheClient)
    {
        $this->cacheEnabled = !$httpCacheClient instanceof NullClient;
    }

    public static function getSubscribedEvents(): array
    {
        return [sprintf('%s.%s', LayoutsEvents::BUILD_VIEW, 'layout') => 'onBuildView'];
    }

    /**
     * Injects if the HTTP cache clearing is enabled or not.
     */
    public function onBuildView(CollectViewParametersEvent $event): void
    {
        $view = $event->getView();
        if (!$view instanceof LayoutViewInterface) {
            return;
        }

        if ($view->getContext() !== ViewInterface::CONTEXT_ADMIN) {
            return;
        }

        $event->addParameter('http_cache_enabled', $this->cacheEnabled);
    }
}