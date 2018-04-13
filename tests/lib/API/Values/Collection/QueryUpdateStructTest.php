<?php

namespace Netgen\BlockManager\Tests\API\Values\Collection;

use Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct;
use Netgen\BlockManager\Collection\QueryType;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Parameters\CompoundParameterDefinition;
use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType;
use PHPUnit\Framework\TestCase;

final class QueryUpdateStructTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct
     */
    private $struct;

    public function setUp()
    {
        $this->struct = new QueryUpdateStruct();
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct::fillParameters
     */
    public function testFillParameters()
    {
        $queryType = $this->buildQueryType();

        $initialValues = array(
            'css_class' => 'css',
            'css_id' => 'id',
            'compound' => false,
            'inner' => 'inner',
        );

        $this->struct->fillParameters($queryType, $initialValues);

        $this->assertEquals(
            array(
                'css_class' => 'css',
                'css_id' => 'id',
                'compound' => false,
                'inner' => 'inner',
            ),
            $this->struct->getParameterValues()
        );
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct::fillParameters
     */
    public function testFillParametersWithMissingValues()
    {
        $queryType = $this->buildQueryType();

        $initialValues = array(
            'css_class' => 'css',
            'inner' => 'inner',
        );

        $this->struct->fillParameters($queryType, $initialValues);

        $this->assertEquals(
            array(
                'css_class' => 'css',
                'css_id' => 'id_default',
                'compound' => true,
                'inner' => 'inner',
            ),
            $this->struct->getParameterValues()
        );
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct::fillParametersFromQuery
     */
    public function testFillParametersFromQuery()
    {
        $queryType = $this->buildQueryType();

        $query = new Query(
            array(
                'queryType' => $queryType,
                'parameters' => array(
                    'css_class' => new Parameter(
                        array(
                            'value' => 'css',
                            'parameterDefinition' => $queryType->getParameterDefinition('css_class'),
                        )
                    ),
                    'inner' => new Parameter(
                        array(
                            'value' => 'inner',
                            'parameterDefinition' => $queryType->getParameterDefinition('compound')->getParameterDefinition('inner'),
                        )
                    ),
                ),
            )
        );

        $this->struct->fillParametersFromQuery($query);

        $this->assertEquals(
            array(
                'css_class' => 'css',
                'css_id' => null,
                'compound' => null,
                'inner' => 'inner',
            ),
            $this->struct->getParameterValues()
        );
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct::fillParametersFromHash
     */
    public function testFillParametersFromHash()
    {
        $queryType = $this->buildQueryType();

        $initialValues = array(
            'css_class' => 'css',
            'css_id' => 'id',
            'compound' => false,
            'inner' => 'inner',
        );

        $this->struct->fillParametersFromHash($queryType, $initialValues);

        $this->assertEquals(
            array(
                'css_class' => 'css',
                'css_id' => 'id',
                'compound' => false,
                'inner' => 'inner',
            ),
            $this->struct->getParameterValues()
        );
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct::fillParametersFromHash
     */
    public function testFillParametersFromHashWithMissingValues()
    {
        $queryType = $this->buildQueryType();

        $initialValues = array(
            'css_class' => 'css',
            'inner' => 'inner',
        );

        $this->struct->fillParametersFromHash($queryType, $initialValues);

        $this->assertEquals(
            array(
                'css_class' => 'css',
                'css_id' => 'id_default',
                'compound' => true,
                'inner' => 'inner',
            ),
            $this->struct->getParameterValues()
        );
    }

    /**
     * @return \Netgen\BlockManager\Collection\QueryTypeInterface
     */
    private function buildQueryType()
    {
        $compoundParameter = new CompoundParameterDefinition(
            array(
                'name' => 'compound',
                'type' => new ParameterType\Compound\BooleanType(),
                'defaultValue' => true,
                'parameterDefinitions' => array(
                    'inner' => new ParameterDefinition(
                        array(
                            'name' => 'inner',
                            'type' => new ParameterType\TextLineType(),
                            'defaultValue' => 'inner_default',
                        )
                    ),
                ),
            )
        );

        $parameterDefinitions = array(
            'css_class' => new ParameterDefinition(
                array(
                    'name' => 'css_class',
                    'type' => new ParameterType\TextLineType(),
                    'defaultValue' => 'css_default',
                )
            ),
            'css_id' => new ParameterDefinition(
                array(
                    'name' => 'css_id',
                    'type' => new ParameterType\TextLineType(),
                    'defaultValue' => 'id_default',
                )
            ),
            'compound' => $compoundParameter,
        );

        return new QueryType(array('parameterDefinitions' => $parameterDefinitions));
    }
}