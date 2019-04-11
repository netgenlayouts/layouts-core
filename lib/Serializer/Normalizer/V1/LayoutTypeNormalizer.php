<?php

declare(strict_types=1);

namespace Netgen\Layouts\Serializer\Normalizer\V1;

use Generator;
use Netgen\Layouts\Layout\Type\LayoutTypeInterface;
use Netgen\Layouts\Serializer\Values\VersionedValue;
use Netgen\Layouts\Serializer\Version;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class LayoutTypeNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    public function normalize($object, $format = null, array $context = [])
    {
        /** @var \Netgen\Layouts\Layout\Type\LayoutTypeInterface $layoutType */
        $layoutType = $object->getValue();

        return [
            'identifier' => $layoutType->getIdentifier(),
            'name' => $layoutType->getName(),
            'icon' => $layoutType->getIcon(),
            'zones' => $this->normalizer->normalize($this->getZones($layoutType), $format, $context),
        ];
    }

    public function supportsNormalization($data, $format = null): bool
    {
        if (!$data instanceof VersionedValue) {
            return false;
        }

        return $data->getValue() instanceof LayoutTypeInterface && $data->getVersion() === Version::API_V1;
    }

    /**
     * Returns the array with layout type zones.
     */
    private function getZones(LayoutTypeInterface $layoutType): Generator
    {
        foreach ($layoutType->getZones() as $zone) {
            $allowedBlockDefinitions = $zone->getAllowedBlockDefinitions();

            yield [
                'identifier' => $zone->getIdentifier(),
                'name' => $zone->getName(),
                'allowed_block_definitions' => count($allowedBlockDefinitions) > 0 ?
                    $allowedBlockDefinitions :
                    true,
            ];
        }
    }
}
