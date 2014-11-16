<?php

namespace Phockito\internal\Clazz;


use Hamcrest\Core\IsAnything;
use Hamcrest\Matcher;
use Hamcrest\MatcherAssert;

class MethodParameterTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $methodParameter = new MethodParameter('test', 'array', null, new IsAnything());

        $this->assertEquals('test', $methodParameter->getName());
        $this->assertEquals('array', $methodParameter->getType());
        $this->assertEquals(null, $methodParameter->getDefaultValue());
        $this->assertInstanceOf(Matcher::class, $methodParameter->getMatcher());
        $this->assertFalse($methodParameter->isReference());
    }

    public function testMatcherIsAnything()
    {
        $methodParameter = new MethodParameter('test', null, null, new IsAnything());

        MatcherAssert::assertThat(null, $methodParameter->getMatcher());
    }
}
 