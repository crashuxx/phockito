<?php

namespace Phockito;


use Exception;
use Phockito\Test\MockWithoutToString;
use Phockito\Test\MockWithToString;
use PHPUnit_Framework_TestCase;

class ToStringTest extends PHPUnit_Framework_TestCase
{
    function testCanMockAndOverrideExistingToString()
    {
        $mock = Phockito::mock(MockWithToString::class);

        $this->assertEquals('', '' . $mock);

        Phockito::when($mock->__toString())->return('NewReturnValue');
        $this->assertEquals('NewReturnValue', '' . $mock);
    }

    function testCanSpyAndOverrideExistingToString()
    {
        $mock = Phockito::spy(new MockWithToString);

        $this->assertEquals('Foo', '' . $mock);

        //cannot stub spy
        //Phockito::when($mock->__toString())->return('NewReturnValue');
        $this->assertEquals('Foo', '' . $mock);
    }

    function testCanMockAndOverrideUndefinedToString()
    {
        $mock = Phockito::mock(MockWithoutToString::class);

        $this->assertEquals('', '' . $mock);

        Phockito::when($mock->__toString())->return('NewReturnValue');
        $this->assertEquals('NewReturnValue', '' . $mock);
    }

    /**
     * I think this test should not pass
     *
     * This will fail:
     * <code>
     * $object = new MockWithoutToString();
     * echo $object;
     * </code>
     *
     */
    function testCanSpyAndOverrideUndefinedToString()
    {
        $mock = Phockito::spy(new MockWithoutToString());

        Phockito::when($mock)->__toString()->return('NewReturnValue');

        $this->assertEquals('', '' . $mock);
    }
}