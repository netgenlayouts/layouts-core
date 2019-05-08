<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Controller\API\V1\Layout;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\Exception\BadStateException;
use Netgen\Layouts\Exception\Layout\LayoutTypeException;
use Netgen\Layouts\Layout\Registry\LayoutTypeRegistry;
use Netgen\Layouts\Serializer\Values\View;
use Netgen\Layouts\Serializer\Version;
use Netgen\Layouts\Validator\Constraint\Locale as LocaleConstraint;
use Netgen\Layouts\Validator\ValidatorTrait;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints;

final class Create extends AbstractController
{
    use ValidatorTrait;

    /**
     * @var \Netgen\Layouts\API\Service\LayoutService
     */
    private $layoutService;

    /**
     * @var \Netgen\Layouts\Layout\Registry\LayoutTypeRegistry
     */
    private $layoutTypeRegistry;

    public function __construct(
        LayoutService $layoutService,
        LayoutTypeRegistry $layoutTypeRegistry
    ) {
        $this->layoutService = $layoutService;
        $this->layoutTypeRegistry = $layoutTypeRegistry;
    }

    /**
     * Creates the layout.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If layout type does not exist
     */
    public function __invoke(Request $request): View
    {
        $this->denyAccessUnlessGranted('nglayouts:layout:add');

        $requestData = $request->attributes->get('data');

        $this->validateCreateLayout($requestData);

        try {
            $layoutType = $this->layoutTypeRegistry->getLayoutType($requestData->get('layout_type'));
        } catch (LayoutTypeException $e) {
            throw new BadStateException('layout_type', 'Layout type does not exist.', $e);
        }

        $layoutCreateStruct = $this->layoutService->newLayoutCreateStruct(
            $layoutType,
            $requestData->get('name'),
            $requestData->get('locale')
        );

        $layoutCreateStruct->description = $requestData->get('description');

        $createdLayout = $this->layoutService->createLayout($layoutCreateStruct);

        return new View($createdLayout, Version::API_V1, Response::HTTP_CREATED);
    }

    /**
     * Validates layout creation parameters from the request.
     *
     * @throws \Netgen\Layouts\Exception\Validation\ValidationException If validation failed
     */
    private function validateCreateLayout(ParameterBag $data): void
    {
        $this->validate(
            $data->get('layout_type'),
            [
                new Constraints\NotBlank(),
                new Constraints\Type(['type' => 'string']),
            ],
            'layout_type'
        );

        $this->validate(
            $data->get('locale'),
            [
                new Constraints\NotBlank(),
                new Constraints\Type(['type' => 'string']),
                new LocaleConstraint(),
            ],
            'locale'
        );
    }
}
