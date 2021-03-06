<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;
use Twig\Extension\ExtensionInterface;
use function class_exists;
use function is_a;

/**
 * @final
 */
class TwigExtensionsListener implements EventSubscriberInterface
{
    private Environment $twig;

    /**
     * @var string[]
     */
    private array $extensions;

    /**
     * @param string[] $extensions
     */
    public function __construct(Environment $twig, array $extensions)
    {
        $this->twig = $twig;
        $this->extensions = $extensions;
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::REQUEST => 'onKernelRequest'];
    }

    /**
     * Adds the Twig extensions to the environment if they don't already exist.
     *
     * @param \Symfony\Component\HttpKernel\Event\RequestEvent $event
     */
    public function onKernelRequest($event): void
    {
        foreach ($this->extensions as $extension) {
            if (!class_exists($extension) || !is_a($extension, ExtensionInterface::class, true)) {
                continue;
            }

            if ($this->twig->hasExtension($extension)) {
                continue;
            }

            $this->twig->addExtension(new $extension());
        }
    }
}
