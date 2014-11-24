<?php

namespace Phockito\internal\InvocationHandler;


use Phockito\internal\Clazz\ClazzFactory;
use Phockito\internal\Clazz\MethodFactory;
use Phockito\internal\Clazz\ParameterFactory;
use Phockito\Test\SpyMe;
use Reflection\Proxy;

class LegacySpyInvocationHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ClazzFactory
     */
    private $classFactory;

    protected function setUp()
    {
        $this->classFactory = new ClazzFactory(new MethodFactory(new ParameterFactory()));
    }

    public function testGetters()
    {
        $proxyClass = Proxy::getProxyClass(SpyMe::class);
        $clazz = $this->classFactory->createFromReflectionClass(new \ReflectionClass(SpyMe::class));
        $context = new LegacySpyInvocationHandler($clazz, $proxyClass, new SpyMe());

        $this->assertEquals($clazz, $context->getClazz());
    }

    public function test()
    {
        $proxyClass = Proxy::getProxyClass(SpyMe::class);
        $clazz = $this->classFactory->createFromReflectionClass(new \ReflectionClass(SpyMe::class));
        $context = new LegacySpyInvocationHandler($clazz, $proxyClass, new SpyMe());

        $returnValue = $context->invoke($proxyClass->newInstance($context), 'Baz', ['lorem ipsum']);

        $this->assertEquals('lorem ipsum', $returnValue);
    }
}
