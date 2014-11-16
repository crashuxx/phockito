<?php

namespace Phockito\internal\Clazz;


use Hamcrest\Core\IsAnything;

class TypeTest extends \PHPUnit_Framework_TestCase
{
    public function testBasic()
    {
        $type = new Type('string', new IsAnything());

        $this->assertEquals('string', $type->getType());
        $this->assertInstanceOf(IsAnything::class, $type->getMatcher());
    }
}
 