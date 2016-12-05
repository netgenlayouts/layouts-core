<?php

namespace Netgen\BlockManager\Serializer\Normalizer;

use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Values\View;
use Netgen\BlockManager\Traits\SerializerAwareTrait;
use Netgen\BlockManager\View\RendererInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;

class ViewNormalizer implements NormalizerInterface, SerializerAwareInterface
{
    use SerializerAwareTrait;

    /**
     * @var \Netgen\BlockManager\View\RendererInterface
     */
    protected $viewRenderer;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\View\RendererInterface $viewRenderer
     */
    public function __construct(RendererInterface $viewRenderer)
    {
        $this->viewRenderer = $viewRenderer;
    }

    /**
     * Normalizes an object into a set of arrays/scalars.
     *
     * @param \Netgen\BlockManager\Serializer\Values\View $object
     * @param string $format
     * @param array $context
     *
     * @return array
     */
    public function normalize($object, $format = null, array $context = array())
    {
        $normalizedData = $this->serializer->normalize(
            new VersionedValue(
                $object->getValue(),
                $object->getVersion(),
                $object->getStatusCode()
            ),
            $format,
            $context
        );

        if (!isset($context['disable_html']) || $context['disable_html'] !== true) {
            $normalizedData['html'] = $this->viewRenderer->renderValueObject(
                $object->getValue(),
                array(
                    'api_version' => $object->getVersion(),
                ) + $object->getViewParameters(),
                $object->getContext()
            );
        }

        return $normalizedData;
    }

    /**
     * Checks whether the given class is supported for normalization by this normalizer.
     *
     * @param mixed $data
     * @param string $format
     *
     * @return bool
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof View;
    }
}
