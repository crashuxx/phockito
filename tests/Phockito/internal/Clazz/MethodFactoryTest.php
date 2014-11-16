<?php

namespace Phockito\internal\Clazz;


use Phockito\Test\FooHasIntegerDefaultArgument;

class MethodFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testReflectionMethod()
    {
        $reflectionMethod = (new \ReflectionClass(FooHasIntegerDefaultArgument::class))->getMethod('Foo');

        $methodFactory = new MethodFactory(new ParameterFactory());
        $method = $methodFactory->createFromReflectionMethod($reflectionMethod);

        $this->assertEquals('Foo', $method->getName());
        $this->assertCount(1, $method->getParameters());
        $this->assertContainsOnlyInstancesOf(Parameter::class, $method->getParameters());
        $this->assertInstanceOf(Type::class, $method->getReturnType());
    }
}
 