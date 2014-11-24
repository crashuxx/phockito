<?php

namespace Phockito\internal\InvocationHandler;


use Phockito\internal\Clazz\ClazzFactory;
use Phockito\internal\Clazz\MethodFactory;
use Phockito\internal\Clazz\ParameterFactory;
use Phockito\Test\MockMe;
use Phockito\Test\SpyMe;
use Reflection\Proxy;

class LegacyMockInvocationHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ClazzFactory
     */
    private $classFactory;

    protected function setUp()
    {
        $this->classFactory = new ClazzFactory(new MethodFactory(new ParameterFactory()));
    }

    /**
     * @test
     */
    public function should_not_call_parent()
    {
        $proxyClass = Proxy::getProxyClass(MockMe::class);
        $clazz = $this->classFactory->createFromReflectionClass(new \ReflectionClass(MockMe::class));
        $context = new LegacyMockInvocationHandler($clazz, $proxyClass, false);

        $returnValue = $context->invoke($proxyClass->newInstance($context), 'Bar', []);

        $this->assertNull($returnValue);
    }

    /**
     * @test
     */
    public function should_call_parent_method()
    {
        $proxyClass = Proxy::getProxyClass(SpyMe::class);
        $clazz = $this->classFactory->createFromReflectionClass(new \ReflectionClass(SpyMe::class));
        $context = new LegacyMockInvocationHandler($clazz, $proxyClass, true);

        $returnValue = $context->invoke($proxyClass->newInstance($context), 'Baz', [true]);

        $this->assertTrue($returnValue);
    }
}
