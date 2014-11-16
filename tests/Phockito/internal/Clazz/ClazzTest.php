<?php

namespace Phockito\internal\Clazz;


class ClazzTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $clazz = new Clazz('test', Clazz::T_CLASS, []);

        $this->assertEquals('test', $clazz->getName());
        $this->assertEquals(Clazz::T_CLASS, $clazz->getType());
        $this->assertEquals([], $clazz->getMethods());
    }
}
