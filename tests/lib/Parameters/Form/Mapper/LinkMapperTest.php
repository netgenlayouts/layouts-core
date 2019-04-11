<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\Form\Mapper;

use Netgen\Layouts\Item\CmsItemLoaderInterface;
use Netgen\Layouts\Item\Registry\ValueTypeRegistry;
use Netgen\Layouts\Item\ValueType\ValueType;
use Netgen\Layouts\Parameters\Form\Mapper\LinkMapper;
use Netgen\Layouts\Parameters\Form\Type\DataMapper\LinkDataMapper;
use Netgen\Layouts\Parameters\Form\Type\LinkType;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ParameterType\ItemLink\RemoteIdConverter;
use Netgen\Layouts\Parameters\ParameterType\LinkType as LinkParameterType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormFactoryInterface;

final class LinkMapperTest extends TestCase
{
    /**
     * @var \Netgen\Layouts\Item\Registry\ValueTypeRegistryInterface
     */
    private $valueTypeRegistry;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $cmsItemLoaderMock;

    /**
     * @var \Netgen\Layouts\Parameters\ParameterType\LinkType
     */
    private $type;

    /**
     * @var \Netgen\Layouts\Parameters\Form\Mapper\LinkMapper
     */
    private $mapper;

    public function setUp(): void
    {
        $this->valueTypeRegistry = new ValueTypeRegistry(['default' => ValueType::fromArray(['isEnabled' => true])]);

        $this->cmsItemLoaderMock = $this->createMock(CmsItemLoaderInterface::class);

        $this->type = new LinkParameterType(
            $this->valueTypeRegistry,
            new RemoteIdConverter($this->cmsItemLoaderMock)
        );

        $this->mapper = new LinkMapper();
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Mapper\LinkMapper::getFormType
     */
    public function testGetFormType(): void
    {
        self::assertSame(LinkType::class, $this->mapper->getFormType());
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Mapper\LinkMapper::mapOptions
     */
    public function testMapOptions(): void
    {
        $parameterDefinition = ParameterDefinition::fromArray(
            [
                'type' => $this->type,
                'options' => [
                    'value_types' => ['value'],
                ],
            ]
        );

        self::assertSame(
            [
                'label' => false,
                'value_types' => ['value'],
            ],
            $this->mapper->mapOptions($parameterDefinition)
        );
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Mapper\LinkMapper::mapOptions
     */
    public function testMapOptionsWithEmptyValueTypes(): void
    {
        $parameterDefinition = ParameterDefinition::fromArray(
            [
                'type' => $this->type,
                'options' => [
                    'value_types' => ['default'],
                ],
            ]
        );

        self::assertSame(
            [
                'label' => false,
                'value_types' => ['default'],
            ],
            $this->mapper->mapOptions($parameterDefinition)
        );
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Mapper\LinkMapper::handleForm
     */
    public function testHandleForm(): void
    {
        $parameterDefinition = ParameterDefinition::fromArray(
            [
                'type' => $this->type,
            ]
        );

        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $factory = $this->createMock(FormFactoryInterface::class);
        $formBuilder = new FormBuilder('name', null, $dispatcher, $factory);

        $this->mapper->handleForm($formBuilder, $parameterDefinition);

        self::assertInstanceOf(LinkDataMapper::class, $formBuilder->getDataMapper());
    }
}
