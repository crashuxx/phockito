<?php

namespace Phockito\internal\Clazz;


use Hamcrest\MatcherAssert;
use Phockito\Test\FooHasArrayDefaultArgument;
use Phockito\Test\FooHasByReferenceArgument;
use Phockito\Test\FooHasIntegerDefaultArgument;
use Phockito\Test\FooHasTypedArrayDefaultArgument;

class ParameterFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateFromReflectionParameterIntegerDefault()
    {
        $reflectionClass = new \ReflectionClass(FooHasIntegerDefaultArgument::class);
        $reflectionMethod = $reflectionClass->getMethod('Foo');

        $parameterFactory = new ParameterFactory();

        $parameter = $parameterFactory->createFromReflectionParameter($reflectionMethod->getParameters()[0]);

        $this->assertEquals('a', $parameter->getName());
        $this->assertEquals('mixed', $parameter->getType()->getValue());
        $this->assertEquals(1, $parameter->getDefaultValue()->getValue());
        MatcherAssert::assertThat($parameter->getDefaultValue()->getValue(), $parameter->getType()->getMatcher());
    }

    public function testCreateFromReflectionParameterArrayDefault()
    {
        $reflectionClass = new \ReflectionClass(FooHasArrayDefaultArgument::class);
        $reflectionMethod = $reflectionClass->getMethod('Foo');

        $parameterFactory = new ParameterFactory();

        $parameter = $parameterFactory->createFromReflectionParameter($reflectionMethod->getParameters()[0]);

        $this->assertEquals('a', $parameter->getName());
        $this->assertEquals('mixed', $parameter->getType()->getValue());
        $this->assertEquals([1, 2, 3], $parameter->getDefaultValue()->getValue());
        MatcherAssert::assertThat($parameter->getDefaultValue()->getValue(), $parameter->getType()->getMatcher());
    }

    public function testCreateFromReflectionParameterArrayTypeDefault()
    {
        $reflectionClass = new \ReflectionClass(FooHasTypedArrayDefaultArgument::class);
        $reflectionMethod = $reflectionClass->getMethod('Foo');

        $parameterFactory = new ParameterFactory();

        $parameter = $parameterFactory->createFromReflectionParameter($reflectionMethod->getParameters()[0]);

        $this->assertEquals('a', $parameter->getName());
        $this->assertEquals('array', $parameter->getType()->getValue());
        $this->assertEquals([1, 2, 3], $parameter->getDefaultValue()->getValue());
        MatcherAssert::assertThat($parameter->getDefaultValue()->getValue(), $parameter->getType()->getMatcher());
    }

    public function testCreateFromReflectionParameterReference()
    {
        $reflectionClass = new \ReflectionClass(FooHasByReferenceArgument::class);
        $reflectionMethod = $reflectionClass->getMethod('Foo');

        $parameterFactory = new ParameterFactory();

        $parameter = $parameterFactory->createFromReflectionParameter($reflectionMethod->getParameters()[0]);

        $this->assertTrue($parameter->getType()->isReference());
    }
}
 