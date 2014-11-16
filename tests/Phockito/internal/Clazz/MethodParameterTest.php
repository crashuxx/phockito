<?php

namespace Phockito\internal\Clazz;


use Hamcrest\Core\IsAnything;

class MethodParameterTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $type = new Type('mixed', new IsAnything());
        $methodParameter = new MethodParameter('test', $type, null);

        $this->assertEquals('test', $methodParameter->getName());
        $this->assertEquals($type, $methodParameter->getType());
        $this->assertEquals(null, $methodParameter->getDefaultValue());
        $this->assertFalse($methodParameter->isReference());
    }
}
 