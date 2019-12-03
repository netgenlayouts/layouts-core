<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Output;

use Generator;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\Transfer\Descriptor;
use Ramsey\Uuid\Uuid;

/**
 * Serializer serializes domain entities into hash representation, which can be
 * transferred through a plain text format, like JSON or XML.
 *
 * Hash format is either a scalar value, a hash array (associative array),
 * a pure numeric array or a nested combination of these.
 */
final class Serializer implements SerializerInterface
{
    /**
     * @var \Netgen\Layouts\API\Service\LayoutService
     */
    private $layoutService;

    /**
     * @var \Netgen\Layouts\API\Service\LayoutResolverService
     */
    private $layoutResolverService;

    /**
     * @var \Netgen\Layouts\Transfer\Output\OutputVisitor
     */
    private $visitor;

    public function __construct(
        LayoutService $layoutService,
        LayoutResolverService $layoutResolverService,
        OutputVisitor $visitor
    ) {
        $this->layoutService = $layoutService;
        $this->layoutResolverService = $layoutResolverService;
        $this->visitor = $visitor;
    }

    public function serializeLayouts(array $layoutIds): array
    {
        $data = $this->createBasicData();

        foreach ($this->loadLayouts($layoutIds) as $layout) {
            $data['entities'][] = $this->visitor->visit($layout);
        }

        return $data;
    }

    public function serializeRules(array $ruleIds): array
    {
        $data = $this->createBasicData();

        foreach ($this->loadRules($ruleIds) as $rule) {
            $data['entities'][] = $this->visitor->visit($rule);
        }

        return $data;
    }

    /**
     * Loads the layouts for provided UUIDs.
     *
     * @param string[] $layoutIds
     *
     * @return \Generator<\Netgen\Layouts\API\Values\Layout\Layout>
     */
    private function loadLayouts(array $layoutIds): Generator
    {
        foreach ($layoutIds as $layoutId) {
            try {
                yield $this->layoutService->loadLayout(Uuid::fromString($layoutId));
            } catch (NotFoundException $e) {
                continue;
            }
        }
    }

    /**
     * Loads the rules for provided UUIDs.
     *
     * @param string[] $ruleIds
     *
     * @return \Generator<\Netgen\Layouts\API\Values\LayoutResolver\Rule>
     */
    private function loadRules(array $ruleIds): Generator
    {
        foreach ($ruleIds as $ruleId) {
            try {
                yield $this->layoutResolverService->loadRule(Uuid::fromString($ruleId));
            } catch (NotFoundException $e) {
                continue;
            }
        }
    }

    /**
     * Creates the array with basic serialized data from provided type and version.
     *
     * @return array<string, mixed>
     */
    private function createBasicData(): array
    {
        return [
            '__version' => Descriptor::FORMAT_VERSION,
            'entities' => [],
        ];
    }
}
