<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\Admin\SharedLayouts;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class Index extends AbstractController
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    private $layoutService;

    public function __construct(LayoutService $layoutService)
    {
        $this->layoutService = $layoutService;
    }

    /**
     * Displays the index page of shared layouts admin interface.
     */
    public function __invoke(): Response
    {
        $this->denyAccessUnlessGranted('nglayouts:ui:access');

        return $this->render(
            '@NetgenLayoutsAdmin/admin/shared_layouts/index.html.twig',
            [
                'shared_layouts' => $this->layoutService->loadSharedLayouts(true),
            ]
        );
    }
}
