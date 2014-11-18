<?php

namespace Phockito\internal\Context;


use Phockito\internal\Clazz\ClazzFactory;
use Phockito\internal\Clazz\MethodFactory;
use Phockito\internal\Clazz\ParameterFactory;
use Phockito\Test\MockMe;

class LegacyMockContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ClazzFactory
     */
    private $classFactory;

    protected function setUp()
    {
        $this->classFactory = new ClazzFactory(new MethodFactory(new ParameterFactory()));
    }

    public function test()
    {
        $clazz = $this->classFactory->createFromReflectionClass(new \ReflectionClass(MockMe::class));
        $context = new LegacyMockContext($clazz, false);

        $returnValue = $context->call('Bar', []);

        $this->assertFalse($returnValue->invokeParent());
        $this->assertNull($returnValue->getValue());
    }

    public function testPartial()
    {
        $clazz = $this->classFactory->createFromReflectionClass(new \ReflectionClass(MockMe::class));
        $context = new LegacyMockContext($clazz, true);

        $returnValue = $context->call('Foo', []);

        $this->assertTrue($returnValue->invokeParent());
    }
}
