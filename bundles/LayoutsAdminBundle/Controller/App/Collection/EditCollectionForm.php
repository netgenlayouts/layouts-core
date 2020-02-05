<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\App\Collection;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\CollectionService;
use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\Collection\Form\CollectionEditType;
use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\View\ViewInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class EditCollectionForm extends AbstractController
{
    /**
     * @var \Netgen\Layouts\API\Service\CollectionService
     */
    private $collectionService;

    public function __construct(CollectionService $collectionService)
    {
        $this->collectionService = $collectionService;
    }

    /**
     * Displays and processes collection draft edit form.
     *
     * @return \Netgen\Layouts\View\ViewInterface|\Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(Collection $collection, Request $request)
    {
        $form = $this->createForm(
            CollectionEditType::class,
            $this->collectionService->newCollectionUpdateStruct($collection),
            ['collection' => $collection]
        );

        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            throw new RuntimeException('Form not submitted.');
        }

        $this->denyAccessUnlessGranted('nglayouts:collection:edit');

        if ($form->isValid()) {
            $this->collectionService->updateCollection($collection, $form->getData());

            return new Response(null, Response::HTTP_NO_CONTENT);
        }

        return $this->buildView(
            $form,
            ViewInterface::CONTEXT_APP,
            [],
            new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
