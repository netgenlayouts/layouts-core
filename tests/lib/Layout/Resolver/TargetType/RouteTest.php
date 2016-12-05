<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\TargetType;

use Netgen\BlockManager\Layout\Resolver\TargetType\Route;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Validation;

class RouteTest extends TestCase
{
    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    protected $requestStack;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\TargetType\Route
     */
    protected $targetType;

    public function setUp()
    {
        $request = Request::create('/');
        $request->attributes->set('_route', 'my_cool_route');

        $this->requestStack = new RequestStack();
        $this->requestStack->push($request);

        $this->targetType = new Route();
        $this->targetType->setRequestStack($this->requestStack);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetType\Route::getType
     */
    public function testGetType()
    {
        $this->assertEquals('route', $this->targetType->getType());
    }

    /**
     * @param mixed $value
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetType\Route::getConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($value, $isValid)
    {
        $validator = Validation::createValidator();

        $errors = $validator->validate($value, $this->targetType->getConstraints());
        $this->assertEquals($isValid, $errors->count() === 0);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetType\Route::provideValue
     */
    public function testProvideValue()
    {
        $this->assertEquals(
            'my_cool_route',
            $this->targetType->provideValue()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetType\Route::provideValue
     */
    public function testProvideValueWithNoRequest()
    {
        // Make sure we have no request
        $this->requestStack->pop();

        $this->assertNull($this->targetType->provideValue());
    }

    /**
     * Provider for testing target type validation.
     *
     * @return array
     */
    public function validationProvider()
    {
        return array(
            array('route_name', true),
            array('', false),
            array(null, false),
        );
    }
}