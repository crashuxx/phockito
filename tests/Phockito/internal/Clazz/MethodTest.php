<?php

namespace Phockito\internal\Clazz;


use Hamcrest\Core\IsAnything;

class MethodTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $type = new Type('mixed', new IsAnything());
        $method = new Method('test', [], $type);

        $this->assertEquals('test', $method->getName());
        $this->assertEquals([], $method->getParameters());
        $this->assertEquals($type, $method->getReturnType());
    }
}
 