<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Controller\API\V1\Block;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\BlockService;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Block\Block;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class MoveToZone extends AbstractController
{
    /**
     * @var \Netgen\Layouts\API\Service\BlockService
     */
    private $blockService;

    /**
     * @var \Netgen\Layouts\API\Service\LayoutService
     */
    private $layoutService;

    public function __construct(BlockService $blockService, LayoutService $layoutService)
    {
        $this->blockService = $blockService;
        $this->layoutService = $layoutService;
    }

    /**
     * Moves the block draft to specified zone.
     */
    public function __invoke(Block $block, Request $request): Response
    {
        $this->denyAccessUnlessGranted('nglayouts:block:reorder', ['layout' => $block->getLayoutId()]);

        $requestData = $request->attributes->get('data');

        $zone = $this->layoutService->loadZoneDraft(
            $requestData->get('layout_id'),
            $requestData->get('zone_identifier')
        );

        $this->blockService->moveBlockToZone(
            $block,
            $zone,
            $requestData->get('parent_position')
        );

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
