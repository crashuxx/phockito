<?php

namespace Phockito\internal\Clazz;


use Phockito\Test\FooHasIntegerDefaultArgument;

class ClazzFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testReflectionClass()
    {
        $reflectionClass = new \ReflectionClass(FooHasIntegerDefaultArgument::class);

        $methodFactory = new ClazzFactory(new MethodFactory(new ParameterFactory()));
        $clazz = $methodFactory->createFromReflectionClass($reflectionClass);

        $this->assertEquals(FooHasIntegerDefaultArgument::class, $clazz->getName());
        $this->assertEquals(Clazz::T_CLASS, $clazz->getType());
        $this->assertCount(1, $clazz->getMethods());
        $this->assertContainsOnlyInstancesOf(Method::class, $clazz->getMethods());
    }
}
