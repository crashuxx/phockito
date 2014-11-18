<?php

namespace Phockito;

use Phockito\Test\MockMe;
use Phockito\Test\SpyMe;
use PHPUnit_Framework_TestCase;

require_once(dirname(dirname(dirname(__FILE__))) . '/src/globals.php');

class GlobalsTest extends PHPUnit_Framework_TestCase
{
    function testCanBuildMock()
    {
        $mock = mock(MockMe::class);
        $this->assertInstanceOf(MockMe::class, $mock);
        $this->assertNull($mock->Foo());
        $this->assertNull($mock->Bar());
    }

    function testCanBuildSpy()
    {
        $spy = spy(new SpyMe());

        $this->assertInstanceOf(SpyMe::class, $spy);
        $this->assertEquals('Foo', $spy->Baz('Foo'));
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Base method Foo was called
     */
    function testSpyMethodThrowsException()
    {
        $spy = spy(new SpyMe());
        $spy->Foo();
    }

    function testCanStub()
    {
        $mock = mock(MockMe::class);

        when($mock->Foo())->return(1);
        $this->assertEquals($mock->Foo(), 1);
    }

    function testCanVerify()
    {
        $mock = mock(MockMe::class);

        $mock->Foo();
        verify($mock)->Foo();
    }
}