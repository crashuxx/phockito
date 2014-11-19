<?php

namespace Phockito;


use Exception;
use Hamcrest\Core\IsInstanceOf;
use Hamcrest\MatcherAssert;
use Hamcrest\Type\IsString;
use PHPUnit_Framework_TestCase;
use SoapClient;

class BuiltinsTest extends PHPUnit_Framework_TestCase
{
    function testCanCreateBasicMockClassOfBuiltin()
    {
        $mock = Phockito::mock(SoapClient::class);

        $this->assertInstanceOf(SoapClient::class, $mock);
        $this->assertNull($mock->Foo());
        $this->assertNull($mock->Bar());
    }

    public function testMockExceptionClass()
    {
        $this->markTestSkipped('class contains final methods');

        $mock = Phockito::mock(Exception::class);

        MatcherAssert::assertThat($mock, new IsInstanceOf(Exception::class));
        MatcherAssert::assertThat($mock->getMessage(), new IsString());
        MatcherAssert::assertThat($mock->getCode(), new IsString());
    }
}