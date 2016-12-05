<?php

namespace Netgen\BlockManager\Traits;

use Symfony\Component\Serializer\SerializerInterface;

/**
 * @deprecated Replace with NormalizerAwareTrait from Symfony when support for Symfony 2.8 ends.
 */
trait SerializerAwareTrait
{
    /**
     * @var \Symfony\Component\Serializer\SerializerInterface
     */
    protected $serializer;

    /**
     * Sets the serializer.
     *
     * @param \Symfony\Component\Serializer\SerializerInterface $serializer
     */
    public function setSerializer(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }
}
