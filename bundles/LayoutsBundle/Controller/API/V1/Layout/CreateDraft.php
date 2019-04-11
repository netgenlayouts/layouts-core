<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Controller\API\V1\Layout;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Serializer\Values\View;
use Netgen\Layouts\Serializer\Version;
use Symfony\Component\HttpFoundation\Response;

final class CreateDraft extends AbstractController
{
    /**
     * @var \Netgen\Layouts\API\Service\LayoutService
     */
    private $layoutService;

    public function __construct(LayoutService $layoutService)
    {
        $this->layoutService = $layoutService;
    }

    /**
     * Creates a new layout draft.
     */
    public function __invoke(Layout $layout): View
    {
        $this->denyAccessUnlessGranted('nglayouts:layout:edit', $layout);

        $createdDraft = $this->layoutService->createDraft($layout, true);

        return new View($createdDraft, Version::API_V1, Response::HTTP_CREATED);
    }
}
