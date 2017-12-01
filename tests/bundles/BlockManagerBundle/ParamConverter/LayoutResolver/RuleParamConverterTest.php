<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\ParamConverter\LayoutResolver;

use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\BlockManager\API\Values\LayoutResolver\Rule as APIRule;
use Netgen\BlockManager\Core\Values\LayoutResolver\Rule;
use Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\RuleParamConverter;
use PHPUnit\Framework\TestCase;

class RuleParamConverterTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $layoutResolverServiceMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\RuleParamConverter
     */
    private $paramConverter;

    public function setUp()
    {
        $this->layoutResolverServiceMock = $this->createMock(LayoutResolverService::class);

        $this->paramConverter = new RuleParamConverter($this->layoutResolverServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\RuleParamConverter::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\RuleParamConverter::getSourceAttributeNames
     */
    public function testGetSourceAttributeName()
    {
        $this->assertEquals(array('ruleId'), $this->paramConverter->getSourceAttributeNames());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\RuleParamConverter::getDestinationAttributeName
     */
    public function testGetDestinationAttributeName()
    {
        $this->assertEquals('rule', $this->paramConverter->getDestinationAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\RuleParamConverter::getSupportedClass
     */
    public function testGetSupportedClass()
    {
        $this->assertEquals(APIRule::class, $this->paramConverter->getSupportedClass());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\RuleParamConverter::loadValueObject
     */
    public function testLoadValueObject()
    {
        $rule = new Rule();

        $this->layoutResolverServiceMock
            ->expects($this->once())
            ->method('loadRule')
            ->with($this->equalTo(42))
            ->will($this->returnValue($rule));

        $this->assertEquals(
            $rule,
            $this->paramConverter->loadValueObject(
                array(
                    'ruleId' => 42,
                    'published' => true,
                )
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\RuleParamConverter::loadValueObject
     */
    public function testLoadValueObjectDraft()
    {
        $rule = new Rule();

        $this->layoutResolverServiceMock
            ->expects($this->once())
            ->method('loadRuleDraft')
            ->with($this->equalTo(42))
            ->will($this->returnValue($rule));

        $this->assertEquals(
            $rule,
            $this->paramConverter->loadValueObject(
                array(
                    'ruleId' => 42,
                    'published' => false,
                )
            )
        );
    }
}
