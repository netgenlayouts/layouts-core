<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\ParameterType;

use Netgen\Layouts\Parameters\ParameterType\IdentifierType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Validation;

final class IdentifierTypeTest extends TestCase
{
    use ParameterTypeTestTrait;

    public function setUp(): void
    {
        $this->type = new IdentifierType();
    }

    /**
     * @covers \Netgen\Layouts\Parameters\ParameterType\IdentifierType::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        self::assertSame('identifier', $this->type::getIdentifier());
    }

    /**
     * @covers \Netgen\Layouts\Parameters\ParameterType\IdentifierType::configureOptions
     * @dataProvider validOptionsProvider
     */
    public function testValidOptions(array $options, array $resolvedOptions): void
    {
        $parameter = $this->getParameterDefinition($options);
        self::assertSame($resolvedOptions, $parameter->getOptions());
    }

    /**
     * @covers \Netgen\Layouts\Parameters\ParameterType\IdentifierType::configureOptions
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
     * @param bool $required
     * @param bool $isValid
     *
     * @covers \Netgen\Layouts\Parameters\ParameterType\IdentifierType::getValueConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($value, bool $required, bool $isValid): void
    {
        $parameter = $this->getParameterDefinition([], $required);
        $validator = Validation::createValidator();

        $errors = $validator->validate($value, $this->type->getConstraints($parameter, $value));
        self::assertSame($isValid, $errors->count() === 0);
    }

    public function validationProvider(): array
    {
        return [
            ['a123abcASD', true, true],
            ['a123abc_ASD', true, true],
            ['a123abcASD', false, true],
            ['a123abc_ASD', false, true],
            [null, false, true],
            [null, true, false],
            ['a123abc ASD', false, false],
            ['a123a-bcASD', false, false],
            ['a123abc.ASD', false, false],
            ['123abcASD', false, false],
        ];
    }

    /**
     * @param mixed $value
     * @param bool $isEmpty
     *
     * @covers \Netgen\Layouts\Parameters\ParameterType\IdentifierType::isValueEmpty
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
