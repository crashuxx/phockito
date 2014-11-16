<?php

namespace Phockito\internal\Clazz;


use stdClass;

class ParameterScalarValueTest extends \PHPUnit_Framework_TestCase
{
    public function testNullValue()
    {
        $methodDefaultValue = new ParameterScalarValue(null);

        $this->assertEquals(null, $methodDefaultValue->getValue());
        $this->assertEquals('NULL', $methodDefaultValue->exportValue());
    }

    public function testNumericValue()
    {
        $methodDefaultValue = new ParameterScalarValue(123);

        $this->assertEquals(123, $methodDefaultValue->getValue());
        $this->assertEquals('123', $methodDefaultValue->exportValue());
    }

    public function testStringValue()
    {
        $methodDefaultValue = new ParameterScalarValue('lorem isum');

        $this->assertEquals('lorem isum', $methodDefaultValue->getValue());
        $this->assertEquals("'lorem isum'", $methodDefaultValue->exportValue());
    }

    public function testNotArrayValue()
    {
        $methodDefaultValue = new ParameterScalarValue([1, 2, 3]);

        $this->assertEquals([1, 2, 3], $methodDefaultValue->getValue());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testObjectValueThrowsException()
    {
        new ParameterScalarValue(new stdClass);
    }
}
 