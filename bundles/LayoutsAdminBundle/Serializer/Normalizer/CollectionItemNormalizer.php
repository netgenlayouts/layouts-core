<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer;

use Generator;
use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\Value;
use Netgen\Layouts\API\Values\Collection\Item;
use Netgen\Layouts\Collection\Item\VisibilityResolverInterface;
use Netgen\Layouts\Exception\Item\ItemException;
use Netgen\Layouts\Item\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class CollectionItemNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    /**
     * @var \Netgen\Layouts\Item\UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var \Netgen\Layouts\Collection\Item\VisibilityResolverInterface
     */
    private $visibilityResolver;

    public function __construct(UrlGeneratorInterface $urlGenerator, VisibilityResolverInterface $visibilityResolver)
    {
        $this->urlGenerator = $urlGenerator;
        $this->visibilityResolver = $visibilityResolver;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        /** @var \Netgen\Layouts\API\Values\Collection\Item $collectionItem */
        $collectionItem = $object->getValue();
        $cmsItem = $collectionItem->getCmsItem();

        $configuration = (function () use ($collectionItem): Generator {
            foreach ($collectionItem->getConfigs() as $configKey => $config) {
                yield $configKey => $this->buildValues($config->getParameters());
            }
        })();

        $data = [
            'id' => $collectionItem->getId()->toString(),
            'collection_id' => $collectionItem->getCollectionId()->toString(),
            'position' => $collectionItem->getPosition(),
            'visible' => $this->visibilityResolver->isVisible($collectionItem),
            'value' => $cmsItem->getValue(),
            'value_type' => $cmsItem->getValueType(),
            'item_view_type' => $collectionItem->getViewType(),
            'name' => $cmsItem->getName(),
            'cms_visible' => $cmsItem->isVisible(),
            'cms_url' => '',
            'config' => $this->normalizer->normalize($configuration, $format, $context),
        ];

        try {
            $data['cms_url'] = $this->urlGenerator->generate($cmsItem);
        } catch (ItemException $e) {
            // Do nothing
        }

        return $data;
    }

    public function supportsNormalization($data, $format = null): bool
    {
        if (!$data instanceof Value) {
            return false;
        }

        return $data->getValue() instanceof Item;
    }

    /**
     * Builds the list of Value objects for provided list of values.
     */
    private function buildValues(iterable $values): Generator
    {
        foreach ($values as $key => $value) {
            yield $key => new Value($value);
        }
    }
}