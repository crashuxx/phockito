<?php

namespace Phockito\internal\Clazz;


use stdClass;

class MethodParameterScalarValueTest extends \PHPUnit_Framework_TestCase
{
    public function testNullValue()
    {
        $methodDefaultValue = new MethodParameterScalarValue(null);

        $this->assertEquals(null, $methodDefaultValue->getValue());
        $this->assertEquals('NULL', $methodDefaultValue->exportValue());
    }

    public function testNumericValue()
    {
        $methodDefaultValue = new MethodParameterScalarValue(123);

        $this->assertEquals(123, $methodDefaultValue->getValue());
        $this->assertEquals('123', $methodDefaultValue->exportValue());
    }

    public function testStringValue()
    {
        $methodDefaultValue = new MethodParameterScalarValue('lorem isum');

        $this->assertEquals('lorem isum', $methodDefaultValue->getValue());
        $this->assertEquals("'lorem isum'", $methodDefaultValue->exportValue());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNotEmptyValueThrowsException()
    {
        new MethodParameterScalarValue([1]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testObjectValueThrowsException()
    {
        new MethodParameterScalarValue(new stdClass);
    }
}
 