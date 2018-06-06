<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin\Layouts;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class DeleteLayout extends Controller
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
     * Deletes a layout.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(Request $request, Layout $layout)
    {
        if ($request->getMethod() !== Request::METHOD_DELETE) {
            return $this->render(
                '@NetgenBlockManagerAdmin/admin/layouts/form/delete_layout.html.twig',
                [
                    'submitted' => false,
                    'error' => false,
                    'layout' => $layout,
                ]
            );
        }

        $this->layoutService->deleteLayout($layout);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}