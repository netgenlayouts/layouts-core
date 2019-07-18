<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\App\Block;

use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\View;
use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\BlockService;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\View\ViewInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class EditForm extends AbstractController
{
    /**
     * @var \Netgen\Layouts\API\Service\BlockService
     */
    private $blockService;

    public function __construct(BlockService $blockService)
    {
        $this->blockService = $blockService;
    }

    /**
     * Displays and processes block draft edit form.
     *
     * @return \Netgen\Layouts\View\ViewInterface|\Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\View|\Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(Block $block, string $locale, string $formName, Request $request)
    {
        $form = $this->createForm(
            $block->getDefinition()->getForm($formName)->getType(),
            $this->blockService->newBlockUpdateStruct($locale, $block),
            ['block' => $block]
        );

        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            throw new RuntimeException('Form not submitted.');
        }

        $this->denyAccessUnlessGranted(
            'nglayouts:block:edit',
            [
                'block_definition' => $block->getDefinition(),
                'layout' => $block->getLayoutId()->toString(),
            ]
        );

        if ($form->isValid()) {
            $updatedBlock = $this->blockService->updateBlock($block, $form->getData());

            return new View($updatedBlock);
        }

        return $this->buildView(
            $form,
            ViewInterface::CONTEXT_APP,
            [],
            new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
