<?php

namespace Phockito;

use Phockito\Test\SpyMe;
use PHPUnit_Framework_TestCase;

class SpiesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Base method Foo was called
     */
    function testCannotPartiallyStub()
    {
        $spy = Phockito::spy(new SpyMe());
        Phockito::when($spy)->Foo()->return(1);

        $this->assertEquals($spy->Foo(), 1);
    }

    function testStubMethodWithArgumentNamedResponse()
    {
        $spy = Phockito::spy(new SpyMe());
        $this->assertEquals($spy->Baz(1), 1);
    }
}