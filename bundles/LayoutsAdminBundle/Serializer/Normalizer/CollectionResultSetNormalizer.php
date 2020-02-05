<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer;

use Generator;
use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\Value;
use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\Collection\Result\ManualItem;
use Netgen\Layouts\Collection\Result\ResultSet;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class CollectionResultSetNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    public function normalize($object, $format = null, array $context = []): array
    {
        /** @var \Netgen\Layouts\Collection\Result\ResultSet $resultSet */
        $resultSet = $object->getValue();

        $collection = $resultSet->getCollection();
        $results = $this->buildValues($resultSet);
        $overflowItems = $this->buildValues($this->getOverflowItems($resultSet));

        return [
            'collection_id' => $collection->getId()->toString(),
            'collection_type' => $collection->hasQuery() ? Collection::TYPE_DYNAMIC : Collection::TYPE_MANUAL,
            'offset' => $collection->getOffset(),
            'limit' => $collection->getLimit(),
            'items' => $this->normalizer->normalize($results, $format, $context),
            'overflow_items' => $this->normalizer->normalize($overflowItems, $format, $context),
        ];
    }

    public function supportsNormalization($data, $format = null): bool
    {
        if (!$data instanceof Value) {
            return false;
        }

        return $data->getValue() instanceof ResultSet;
    }

    /**
     * Returns all items from the collection which are overflown. Overflown items
     * are those NOT included in the provided result set, as defined by collection
     * offset and limit.
     *
     * @return \Generator<\Netgen\Layouts\API\Values\Collection\Item>
     */
    private function getOverflowItems(ResultSet $resultSet): Generator
    {
        $includedPositions = [];
        foreach ($resultSet->getResults() as $result) {
            $item = $result->getItem();
            $subItem = $result->getSubItem();

            if ($item instanceof ManualItem) {
                $includedPositions[] = $item->getCollectionItem()->getPosition();
            }

            if ($subItem instanceof ManualItem) {
                $includedPositions[] = $subItem->getCollectionItem()->getPosition();
            }
        }

        foreach ($resultSet->getCollection()->getItems() as $collectionItem) {
            if (!in_array($collectionItem->getPosition(), $includedPositions, true)) {
                yield $collectionItem;
            }
        }
    }

    /**
     * Builds the list of Value objects for provided list of values.
     *
     * @param iterable<object> $values
     *
     * @return \Generator<array-key, \Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\Value>
     */
    private function buildValues(iterable $values): Generator
    {
        foreach ($values as $key => $value) {
            yield $key => new Value($value);
        }
    }
}
