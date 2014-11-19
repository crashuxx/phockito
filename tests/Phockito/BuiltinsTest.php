<?php

namespace Phockito;


use Exception;
use Hamcrest\Core\IsInstanceOf;
use Hamcrest\MatcherAssert;
use Hamcrest\Type\IsNumeric;
use Hamcrest\Type\IsString;
use PHPUnit_Framework_TestCase;
use SoapClient;

class BuiltinsTest extends PHPUnit_Framework_TestCase
{
    function testCanCreateBasicMockClassOfBuiltin()
    {
        $mock = Phockito::mock(SoapClient::class);

        $this->assertInstanceOf(SoapClient::class, $mock);
        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertNull($mock->Foo());
        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertNull($mock->Bar());
    }

    public function testMockExceptionClass()
    {
        $mock = Phockito::mock(Exception::class);

        MatcherAssert::assertThat($mock, new IsInstanceOf(Exception::class));
        MatcherAssert::assertThat($mock->getMessage(), new IsString());
        MatcherAssert::assertThat($mock->getCode(), new IsNumeric());
    }
}