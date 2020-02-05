<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Serializer\Normalizer;

use Doctrine\Common\Collections\ArrayCollection;
use Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\PlaceholderNormalizer;
use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\Value;
use Netgen\Bundle\LayoutsAdminBundle\Tests\Serializer\Stubs\NormalizerStub;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Block\Placeholder;
use Netgen\Layouts\Tests\API\Stubs\Value as APIValue;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Serializer;

final class PlaceholderNormalizerTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\PlaceholderNormalizer
     */
    private $normalizer;

    protected function setUp(): void
    {
        $this->normalizer = new PlaceholderNormalizer();
        $this->normalizer->setNormalizer(new Serializer([new NormalizerStub()]));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\PlaceholderNormalizer::buildViewValues
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\PlaceholderNormalizer::normalize
     */
    public function testNormalize(): void
    {
        $blockUuid = Uuid::uuid4();
        $block = Block::fromArray(['id' => $blockUuid]);

        $placeholder = Placeholder::fromArray(
            [
                'identifier' => 'main',
                'blocks' => new ArrayCollection([$block]),
            ]
        );

        self::assertSame(
            [
                'identifier' => 'main',
                'blocks' => [$blockUuid->toString() => 'data'],
            ],
            $this->normalizer->normalize(new Value($placeholder))
        );
    }

    /**
     * @param mixed $data
     * @param bool $expected
     *
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer\PlaceholderNormalizer::supportsNormalization
     * @dataProvider supportsNormalizationDataProvider
     */
    public function testSupportsNormalization($data, bool $expected): void
    {
        self::assertSame($expected, $this->normalizer->supportsNormalization($data));
    }

    public function supportsNormalizationDataProvider(): array
    {
        return [
            [null, false],
            [true, false],
            [false, false],
            ['placeholder', false],
            [[], false],
            [42, false],
            [42.12, false],
            [new APIValue(), false],
            [new Placeholder(), false],
            [new Value(new APIValue()), false],
            [new Value(new Placeholder()), true],
        ];
    }
}
