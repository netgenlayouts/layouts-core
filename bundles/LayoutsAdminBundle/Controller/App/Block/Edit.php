<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\App\Block;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\BlockService;
use Netgen\Layouts\API\Service\CollectionService;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\Collection\Form\CollectionEditType;
use Netgen\Layouts\Collection\Form\QueryEditType;
use Symfony\Component\HttpFoundation\Response;

final class Edit extends AbstractController
{
    /**
     * @var \Netgen\Layouts\API\Service\BlockService
     */
    private $blockService;

    /**
     * @var \Netgen\Layouts\API\Service\CollectionService
     */
    private $collectionService;

    public function __construct(BlockService $blockService, CollectionService $collectionService)
    {
        $this->blockService = $blockService;
        $this->collectionService = $collectionService;
    }

    /**
     * Displays block edit interface.
     */
    public function __invoke(Block $block): Response
    {
        $this->denyAccessUnlessGranted('nglayouts:api:read');

        $forms = $this->buildBlockForms($block);

        if ($block->hasCollection('default')) {
            $forms += $this->buildCollectionForms($block->getCollection('default'));
        }

        return $this->render(
            '@NetgenLayoutsAdmin/app/block/edit.html.twig',
            [
                'block' => $block,
                'forms' => $forms,
            ]
        );
    }

    private function buildBlockForms(Block $block): array
    {
        $blockDefinition = $block->getDefinition();
        $updateStruct = $this->blockService->newBlockUpdateStruct($block->getLocale(), $block);

        $forms = [];

        if ($blockDefinition->hasForm('full')) {
            $forms['full'] = $this->createForm(
                $blockDefinition->getForm('full')->getType(),
                $updateStruct,
                ['block' => $block]
            );
        } elseif ($blockDefinition->hasForm('content') && $blockDefinition->hasForm('design')) {
            $forms['content'] = $this->createForm(
                $blockDefinition->getForm('content')->getType(),
                $updateStruct,
                ['block' => $block]
            );

            $forms['design'] = $this->createForm(
                $blockDefinition->getForm('design')->getType(),
                $updateStruct,
                ['block' => $block]
            );
        }

        return $forms;
    }

    private function buildCollectionForms(Collection $collection): array
    {
        $forms = [];

        $forms['collection'] = $this->createForm(
            CollectionEditType::class,
            $this->collectionService->newCollectionUpdateStruct($collection),
            ['collection' => $collection]
        );

        $query = $collection->getQuery();
        if ($query instanceof Query) {
            $forms['query'] = $this->createForm(
                QueryEditType::class,
                $this->collectionService->newQueryUpdateStruct($query->getLocale(), $query),
                ['query' => $query]
            );
        }

        return $forms;
    }
}
