<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Parameters\ParameterType;

use Netgen\BlockManager\Parameters\ParameterType\TextType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Validation;

final class TextTypeTest extends TestCase
{
    use ParameterTypeTestTrait;

    public function setUp(): void
    {
        $this->type = new TextType();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\TextType::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        self::assertSame('text', $this->type::getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\TextType::configureOptions
     * @dataProvider validOptionsProvider
     */
    public function testValidOptions(array $options, array $resolvedOptions): void
    {
        $parameter = $this->getParameterDefinition($options);
        self::assertSame($resolvedOptions, $parameter->getOptions());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\TextType::configureOptions
     * @dataProvider invalidOptionsProvider
     */
    public function testInvalidOptions(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->getParameterDefinition($options);
    }

    public function validOptionsProvider(): array
    {
        return [
            [
                [],
                [],
            ],
        ];
    }

    public function invalidOptionsProvider(): array
    {
        return [
            [
                [
                    'undefined_value' => 'Value',
                ],
            ],
        ];
    }

    /**
     * @param mixed $value
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Parameters\ParameterType\TextType::getRequiredConstraints
     * @covers \Netgen\BlockManager\Parameters\ParameterType\TextType::getValueConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($value, bool $isValid): void
    {
        $parameter = $this->getParameterDefinition();
        $validator = Validation::createValidator();

        $errors = $validator->validate($value, $this->type->getConstraints($parameter, $value));
        self::assertSame($isValid, $errors->count() === 0);
    }

    public function validationProvider(): array
    {
        return [
            ['test', true],
            [null, true],
            [12.3, false],
            [12, false],
            [true, false],
            [false, false],
            [[], false],
        ];
    }

    /**
     * @param mixed $value
     * @param bool $isEmpty
     *
     * @covers \Netgen\BlockManager\Parameters\ParameterType\TextType::isValueEmpty
     * @dataProvider emptyProvider
     */
    public function testIsValueEmpty($value, bool $isEmpty): void
    {
        self::assertSame($isEmpty, $this->type->isValueEmpty($this->getParameterDefinition(), $value));
    }

    public function emptyProvider(): array
    {
        return [
            [null, true],
            ['foo', false],
            ['', true],
        ];
    }
}
