<?php

namespace Netgen\BlockManager\Tests\Validator\Structs;

use Netgen\BlockManager\API\Values\Block\BlockUpdateStruct;
use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinitionHandler;
use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Netgen\BlockManager\Validator\Constraint\Structs\BlockUpdateStruct as BlockUpdateStructConstraint;
use Netgen\BlockManager\Validator\Structs\BlockUpdateStructValidator;
use stdClass;
use Symfony\Component\Validator\Constraints\NotBlank;

final class BlockUpdateStructValidatorTest extends ValidatorTestCase
{
    public function setUp()
    {
        $this->constraint = new BlockUpdateStructConstraint();

        $handler = new BlockDefinitionHandler();
        $this->constraint->payload = new Block(
            [
                'viewType' => 'large',
                'mainLocale' => 'en',
                'definition' => new BlockDefinition(
                    [
                        'parameterDefinitions' => $handler->getParameterDefinitions(),
                        'viewTypes' => [
                            'large' => new ViewType(
                                [
                                    'itemViewTypes' => [
                                        'standard' => new ItemViewType(),
                                    ],
                                ]
                            ),
                        ],
                    ]
                ),
            ]
        );

        parent::setUp();
    }

    /**
     * @return \Symfony\Component\Validator\ConstraintValidator
     */
    public function getValidator()
    {
        return new BlockUpdateStructValidator();
    }

    /**
     * @param array $value
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Validator\Structs\BlockUpdateStructValidator::validate
     * @dataProvider validateDataProvider
     */
    public function testValidate($value, $isValid)
    {
        $this->assertValid($isValid, new BlockUpdateStruct($value));
    }

    /**
     * @covers \Netgen\BlockManager\Validator\Structs\BlockUpdateStructValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "Netgen\BlockManager\Validator\Constraint\Structs\BlockUpdateStruct", "Symfony\Component\Validator\Constraints\NotBlank" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint()
    {
        $this->constraint = new NotBlank();
        $this->assertValid(true, new BlockUpdateStruct());
    }

    /**
     * @covers \Netgen\BlockManager\Validator\Structs\BlockUpdateStructValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "Netgen\BlockManager\API\Values\Block\Block", "stdClass" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidBlock()
    {
        $this->constraint->payload = new stdClass();
        $this->assertValid(true, new BlockUpdateStruct());
    }

    /**
     * @covers \Netgen\BlockManager\Validator\Structs\BlockUpdateStructValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "Netgen\BlockManager\API\Values\Block\BlockUpdateStruct", "integer" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue()
    {
        $this->constraint->payload = new Block();
        $this->assertValid(true, 42);
    }

    public function validateDataProvider()
    {
        return [
            [
                [
                    'alwaysAvailable' => true,
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                false,
            ],
            [
                [
                    'locale' => null,
                    'alwaysAvailable' => true,
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                false,
            ],
            [
                [
                    'locale' => '',
                    'alwaysAvailable' => true,
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                false,
            ],
            [
                [
                    'locale' => 'en-US',
                    'alwaysAvailable' => true,
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                false,
            ],
            [
                [
                    'locale' => 'en_US.utf8',
                    'alwaysAvailable' => true,
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                false,
            ],
            [
                [
                    'locale' => 'nonexistent',
                    'alwaysAvailable' => true,
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                false,
            ],
            [
                [
                    'locale' => 'en',
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                true,
            ],
            [
                [
                    'locale' => 'en',
                    'alwaysAvailable' => 42,
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                false,
            ],
            [
                [
                    'locale' => 'en',
                    'alwaysAvailable' => true,
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                true,
            ],
            [
                [
                    'locale' => 'en',
                    'alwaysAvailable' => true,
                    'viewType' => null,
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                true,
            ],
            [
                [
                    'locale' => 'en',
                    'alwaysAvailable' => true,
                    'viewType' => null,
                    'itemViewType' => null,
                    'name' => 'My block',
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                true,
            ],
            [
                [
                    'locale' => 'en',
                    'alwaysAvailable' => true,
                    'viewType' => '',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                false,
            ],
            [
                [
                    'locale' => 'en',
                    'alwaysAvailable' => true,
                    'viewType' => 'large',
                    'itemViewType' => '',
                    'name' => 'My block',
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                false,
            ],
            [
                [
                    'locale' => 'en',
                    'alwaysAvailable' => true,
                    'viewType' => 'large',
                    'itemViewType' => 'nonexistent',
                    'name' => 'My block',
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                false,
            ],
            [
                [
                    'locale' => 'en',
                    'alwaysAvailable' => true,
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => null,
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                true,
            ],
            [
                [
                    'locale' => 'en',
                    'alwaysAvailable' => true,
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => '',
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                true,
            ],
            [
                [
                    'locale' => 'en',
                    'alwaysAvailable' => true,
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 42,
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                false,
            ],
            [
                [
                    'locale' => 'en',
                    'alwaysAvailable' => true,
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => [
                        'css_class' => '',
                        'css_id' => 'id',
                    ],
                ],
                true,
            ],
            [
                [
                    'locale' => 'en',
                    'alwaysAvailable' => true,
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => [
                        'css_class' => null,
                        'css_id' => 'id',
                    ],
                ],
                true,
            ],
            [
                [
                    'locale' => 'en',
                    'alwaysAvailable' => true,
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => [
                        'css_id' => 'id',
                    ],
                ],
                true,
            ],
            [
                [
                    'locale' => 'en',
                    'alwaysAvailable' => true,
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => '',
                    ],
                ],
                true,
            ],
            [
                [
                    'locale' => 'en',
                    'alwaysAvailable' => true,
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => null,
                    ],
                ],
                true,
            ],
            [
                [
                    'locale' => 'en',
                    'alwaysAvailable' => true,
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => [
                        'css_class' => 'class',
                    ],
                ],
                true,
            ],
        ];
    }
}
