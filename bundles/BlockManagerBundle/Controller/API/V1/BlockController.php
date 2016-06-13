<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1;

use Netgen\BlockManager\API\Service\CollectionService;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Collection\CollectionDraft;
use Netgen\BlockManager\Exception\InvalidArgumentException;
use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Page\CollectionReference;
use Netgen\BlockManager\Serializer\Values\FormView;
use Netgen\BlockManager\Serializer\Values\ValueArray;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Values\View;
use Netgen\BlockManager\Serializer\Version;
use Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockValidator;
use Netgen\Bundle\BlockManagerBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Netgen\BlockManager\API\Values\Page\BlockDraft;
use Netgen\BlockManager\Exception\BadStateException;
use Netgen\BlockManager\Exception\NotFoundException;

class BlockController extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Service\BlockService
     */
    protected $blockService;

    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    protected $layoutService;

    /**
     * @var \Netgen\BlockManager\API\Service\CollectionService
     */
    protected $collectionService;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockValidator
     */
    protected $validator;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\BlockService $blockService
     * @param \Netgen\BlockManager\API\Service\LayoutService $layoutService
     * @param \Netgen\BlockManager\API\Service\CollectionService $collectionService
     * @param \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockValidator $validator
     */
    public function __construct(
        BlockService $blockService,
        LayoutService $layoutService,
        CollectionService $collectionService,
        BlockValidator $validator
    ) {
        $this->blockService = $blockService;
        $this->layoutService = $layoutService;
        $this->collectionService = $collectionService;
        $this->validator = $validator;
    }

    /**
     * Loads a block draft.
     *
     * @param \Netgen\BlockManager\API\Values\Page\BlockDraft $block
     *
     * @return \Netgen\BlockManager\Serializer\Values\View
     */
    public function view(BlockDraft $block)
    {
        return new View($block, Version::API_V1);
    }

    /**
     * Loads all block draft collections.
     *
     * @param \Netgen\BlockManager\API\Values\Page\BlockDraft $block
     *
     * @return \Netgen\BlockManager\Serializer\Values\ValueArray
     */
    public function loadCollections(BlockDraft $block)
    {
        $collections = array_map(
            function (CollectionReference $collection) {
                return new VersionedValue($collection, Version::API_V1);
            },
            $this->blockService->loadCollectionReferences($block)
        );

        return new ValueArray($collections);
    }

    /**
     * Changes the collection type within the block.
     *
     * @param \Netgen\BlockManager\API\Values\Page\BlockDraft $block
     * @param \Netgen\BlockManager\API\Values\Collection\CollectionDraft $collection
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function changeCollectionType(BlockDraft $block, CollectionDraft $collection, Request $request)
    {
        $newType = $request->request->get('new_type');

        if (!in_array($newType, array(Collection::TYPE_MANUAL, Collection::TYPE_DYNAMIC, Collection::TYPE_NAMED))) {
            throw new InvalidArgumentException(
                'new_type',
                'Specified type is not valid.'
            );
        }

        if ($collection->getType() === $newType) {
            throw new BadStateException('new_type', 'New collection type cannot be equal to old collection type.');
        }

        // @TODO Validate that collection belongs to block

        if ($collection->getType() === Collection::TYPE_NAMED) {
            return new Response(null, Response::HTTP_NO_CONTENT);
        }

        if ($newType === Collection::TYPE_MANUAL) {
            $this->collectionService->changeCollectionType($collection, $newType);
        } elseif ($newType === Collection::TYPE_DYNAMIC) {
            $queryType = $this->getQueryType($request->request->get('query_type'));

            $queryCreateStruct = $this->collectionService->newQueryCreateStruct(
                'default',
                $queryType->getType()
            );

            $queryCreateStruct->setParameters($queryType->getConfig()->getDefaultQueryParameters());

            $this->collectionService->changeCollectionType($collection, $newType, $queryCreateStruct);
        } elseif ($newType === Collection::TYPE_NAMED) {
            return new Response(null, Response::HTTP_NO_CONTENT);
        }

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Creates the block.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If block type or block definition does not exist
     *                                                          If layout with specified ID does not exist
     *
     * @return \Netgen\BlockManager\Serializer\Values\View
     */
    public function create(Request $request)
    {
        $this->validator->validateCreateBlock($request);

        $blockType = $this->getBlockType($request->request->get('block_type'));

        try {
            $layout = $this->layoutService->loadLayoutDraft($request->request->get('layout_id'));
        } catch (NotFoundException $e) {
            throw new BadStateException('layout_id', 'Layout draft does not exist.', $e);
        }

        $createdBlock = $this->blockService->createBlock(
            $this->blockService->newBlockCreateStruct($blockType),
            $layout,
            $request->request->get('zone_identifier'),
            $request->request->get('position')
        );

        return new View($createdBlock, Version::API_V1, Response::HTTP_CREATED);
    }

    /**
     * Moves the block draft.
     *
     * @param \Netgen\BlockManager\API\Values\Page\BlockDraft $block
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function move(BlockDraft $block, Request $request)
    {
        $this->blockService->moveBlock(
            $block,
            $request->request->get('position'),
            $request->request->get('zone_identifier')
        );

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Displays and processes block draft edit form.
     *
     * @param \Netgen\BlockManager\API\Values\Page\BlockDraft $block
     * @param string $formName
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If block does not support the specified form
     *
     * @return \Netgen\BlockManager\Serializer\Values\View
     */
    public function form(BlockDraft $block, $formName, Request $request)
    {
        $blockDefinition = $this->getBlockDefinition($block->getDefinitionIdentifier());

        if (!$blockDefinition->getConfig()->hasForm($formName)) {
            throw new InvalidArgumentException('form', 'Block does not support specified form.');
        }

        $updateStruct = $this->blockService->newBlockUpdateStruct();
        $updateStruct->setParameters($block->getParameters());
        $updateStruct->viewType = $block->getViewType();
        $updateStruct->itemViewType = $block->getItemViewType();
        $updateStruct->name = $block->getName();

        $form = $this->createForm(
            $blockDefinition->getConfig()->getForm($formName)->getType(),
            $updateStruct,
            array(
                'blockDefinition' => $blockDefinition,
                'action' => $this->generateUrl(
                    'netgen_block_manager_api_v1_block_form',
                    array(
                        'blockId' => $block->getId(),
                        'formName' => $formName,
                    )
                ),
            )
        );

        $form->handleRequest($request);

        if ($request->getMethod() !== Request::METHOD_POST) {
            return new FormView($form, Version::API_V1);
        }

        if ($form->isValid()) {
            $updatedBlock = $this->blockService->updateBlock($block, $form->getData());

            return new View($updatedBlock, Version::API_V1);
        }

        return new FormView($form, Version::API_V1, Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Restores the block draft to the published state.
     *
     * @param \Netgen\BlockManager\API\Values\Page\BlockDraft $block
     *
     * @return \Netgen\BlockManager\Serializer\Values\View
     */
    public function restore(BlockDraft $block)
    {
        $restoredBlock = $this->blockService->restoreBlock($block);

        return new View($restoredBlock, Version::API_V1);
    }

    /**
     * Deletes the block draft.
     *
     * @param \Netgen\BlockManager\API\Values\Page\BlockDraft $block
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete(BlockDraft $block)
    {
        $this->blockService->deleteBlock($block);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
