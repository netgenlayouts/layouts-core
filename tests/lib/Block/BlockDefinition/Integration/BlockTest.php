<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Block\BlockDefinition\Integration;

use Netgen\Layouts\Block\BlockDefinition;
use Netgen\Layouts\Block\BlockDefinition\BlockDefinitionHandlerInterface;
use Netgen\Layouts\Block\BlockDefinition\Configuration\Collection;
use Netgen\Layouts\Block\BlockDefinition\Configuration\ItemViewType;
use Netgen\Layouts\Block\BlockDefinition\Configuration\ViewType;
use Netgen\Layouts\Block\BlockDefinitionInterface;
use Netgen\Layouts\Block\Registry\BlockDefinitionRegistry;
use Netgen\Layouts\Exception\Validation\ValidationException;
use Netgen\Layouts\Parameters\TranslatableParameterBuilderFactory;
use Netgen\Layouts\Tests\Core\CoreTestCase;
use Netgen\Layouts\Tests\TestCase\ValidatorFactory;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class BlockTest extends CoreTestCase
{
    /**
     * @dataProvider parametersDataProvider
     */
    public function testCreateBlock(array $parameters, array $expectedParameters): void
    {
        $blockDefinition = $this->createBlockDefinition(array_keys($expectedParameters));

        // We need to recreate the service due to recreating the block definition
        // registry in $this->createBlockDefinition() call
        $this->blockService = $this->createBlockService();

        $blockCreateStruct = $this->blockService->newBlockCreateStruct($blockDefinition);
        $blockCreateStruct->viewType = 'default';
        $blockCreateStruct->itemViewType = 'standard';
        $blockCreateStruct->fillParametersFromHash($parameters);

        $zone = $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'))->getZone('left');
        $createdBlock = $this->blockService->createBlockInZone($blockCreateStruct, $zone);

        $createdParameters = [];
        foreach ($createdBlock->getParameters() as $parameterName => $parameter) {
            $createdParameters[$parameterName] = $parameter->getValue();
        }

        self::assertSame(array_keys($expectedParameters), array_keys($createdParameters));

        foreach ($expectedParameters as $key => $expectedParameter) {
            self::assertContainsEquals($expectedParameter, $createdParameters);
        }
    }

    /**
     * @dataProvider invalidParametersDataProvider
     */
    public function testCreateBlockWithInvalidParameters(array $parameters, array $testedParams = []): void
    {
        $this->expectException(ValidationException::class);

        if (count($parameters) === 0) {
            throw ValidationException::validationFailed('parameters', 'Invalid parameters');
        }

        $blockDefinition = $this->createBlockDefinition(
            count($testedParams) > 0 ? $testedParams : array_keys($parameters)
        );

        // We need to recreate the service due to recreating the block definition
        // registry in $this->createBlockDefinition() call
        $this->blockService = $this->createBlockService();

        $blockCreateStruct = $this->blockService->newBlockCreateStruct($blockDefinition);
        $blockCreateStruct->viewType = 'default';
        $blockCreateStruct->itemViewType = 'standard';
        $blockCreateStruct->fillParametersFromHash($parameters);

        $zone = $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'))->getZone('left');
        $this->blockService->createBlockInZone($blockCreateStruct, $zone);
    }

    public function hasCollection(): bool
    {
        return false;
    }

    abstract public function parametersDataProvider(): array;

    abstract public function invalidParametersDataProvider(): array;

    abstract protected function createBlockDefinitionHandler(): BlockDefinitionHandlerInterface;

    protected function createValidator(): ValidatorInterface
    {
        return Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new ValidatorFactory($this))
            ->getValidator();
    }

    private function createBlockDefinition(array $parameterNames = []): BlockDefinitionInterface
    {
        $handler = $this->createBlockDefinitionHandler();

        $builderFactory = new TranslatableParameterBuilderFactory($this->parameterTypeRegistry);
        $parameterBuilder = $builderFactory->createParameterBuilder();
        $handler->buildParameters($parameterBuilder);
        $parameterDefinitions = $parameterBuilder->buildParameterDefinitions();

        $filteredParameterDefinitions = [];
        if (count($parameterNames) > 0) {
            foreach ($parameterDefinitions as $parameterName => $parameterDefinition) {
                if (in_array($parameterName, $parameterNames, true)) {
                    $filteredParameterDefinitions[$parameterName] = $parameterDefinition;
                }
            }
        }

        $collections = [];
        if ($this->hasCollection()) {
            $collections['default'] = Collection::fromArray(
                [
                    'identifier' => 'default',
                ]
            );
        }

        $blockDefinition = BlockDefinition::fromArray(
            [
                'identifier' => 'definition',
                'handler' => $handler,
                'viewTypes' => [
                    'default' => ViewType::fromArray(
                        [
                            'itemViewTypes' => [
                                'standard' => new ItemViewType(),
                            ],
                        ]
                    ),
                ],
                'collections' => $collections,
                'parameterDefinitions' => $filteredParameterDefinitions,
                'configDefinitions' => [],
            ]
        );

        $allBlockDefinitions = $this->blockDefinitionRegistry->getBlockDefinitions();
        $allBlockDefinitions['definition'] = $blockDefinition;

        $this->blockDefinitionRegistry = new BlockDefinitionRegistry($allBlockDefinitions);

        return $blockDefinition;
    }
}
