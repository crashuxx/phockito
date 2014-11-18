<?php

namespace Phockito\internal\Context;


use Phockito\internal\Clazz\ClazzFactory;
use Phockito\internal\Clazz\MethodFactory;
use Phockito\internal\Clazz\ParameterFactory;
use Phockito\Test\SpyMe;

class LegacySpyContextTest extends \PHPUnit_Framework_TestCase
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
        $clazz = $this->classFactory->createFromReflectionClass(new \ReflectionClass(SpyMe::class));
        $context = new LegacySpyContext($clazz, new SpyMe());

        $this->assertEquals($clazz, $context->getClazz());
    }

    public function test()
    {
        $clazz = $this->classFactory->createFromReflectionClass(new \ReflectionClass(SpyMe::class));
        $context = new LegacySpyContext($clazz, new SpyMe());

        $returnValue = $context->call('Baz', ['lorem ipsum']);

        $this->assertFalse($returnValue->invokeParent());
        $this->assertEquals('lorem ipsum', $returnValue->getValue());
    }
}
