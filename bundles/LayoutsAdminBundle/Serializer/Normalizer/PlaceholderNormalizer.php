<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer;

use Generator;
use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\Value;
use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\View;
use Netgen\Layouts\API\Values\Block\BlockList;
use Netgen\Layouts\API\Values\Block\Placeholder;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class PlaceholderNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    public function normalize($object, $format = null, array $context = []): array
    {
        /** @var \Netgen\Layouts\API\Values\Block\Placeholder $placeholder */
        $placeholder = $object->getValue();

        $blocks = $this->buildViewValues($placeholder->getBlocks());

        return [
            'identifier' => $placeholder->getIdentifier(),
            'blocks' => $this->normalizer->normalize($blocks, $format, $context),
        ];
    }

    public function supportsNormalization($data, $format = null): bool
    {
        if (!$data instanceof Value) {
            return false;
        }

        return $data->getValue() instanceof Placeholder;
    }

    /**
     * Builds the list of View objects for provided list of blocks.
     *
     * @param iterable<object> $values
     *
     * @return \Generator<array-key, \Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\View>
     */
    private function buildViewValues(BlockList $blockList): Generator
    {
        foreach ($blockList as $block) {
            yield $block->getId()->toString() => new View($block);
        }
    }
}
