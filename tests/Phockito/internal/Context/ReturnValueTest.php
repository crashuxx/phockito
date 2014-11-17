<?php

namespace Phockito\internal\Context;


class ReturnValueTest extends \PHPUnit_Framework_TestCase
{
    public function testGetValue()
    {
        $returnValue = new ReturnValue(false, 'test');

        $this->assertFalse($returnValue->invokeParent());
        $this->assertEquals('test', $returnValue->getValue());
    }

    /**
     * @expectedException \Exception
     */
    public function testGetValueOnInvokeParent()
    {
        $returnValue = new ReturnValue(true);

        $returnValue->getValue();
    }
}
