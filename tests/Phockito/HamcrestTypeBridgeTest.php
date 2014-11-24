<?php

namespace Phockito;


use Phockito\Test\FinalClass;
use Phockito\Test\HamcrestMe;
use Phockito\Test\MockMeConstructor;
use Phockito\Test\PassMe;
use PHPUnit_Framework_TestCase;

class HamcrestTypeBridgeTest extends PHPUnit_Framework_TestCase
{
    function testCanStubUsingMatchersForTypeHintedObjectArguments()
    {
        $mock = Phockito::mock(HamcrestMe::class);

        Phockito::when($mock->Foo(
            HamcrestTypeBridge::argOfTypeThat(PassMe::class, anInstanceOf(PassMe::class))))
            ->return('PassMe');

        $this->assertEquals($mock->Foo(new PassMe()), 'PassMe');
    }

    function testCanBridgeTypeWithTypeHintedConstructor()
    {
        $mock = Phockito::mock(MockMeConstructor::class);

        Phockito::when($mock->Foo(
            HamcrestTypeBridge::argOfTypeThat(PassMe::class, anInstanceOf(PassMe::class))))
            ->return('PassMe');

        $this->assertEquals($mock->Foo(new PassMe()), 'PassMe');
    }

    /**
     * @expectedException \ReflectionException
     * @ expectedExceptionCode E_USER_ERROR
     * @ expectedExceptionMessage Can't mock non-existent class NotAClass
     */
    function testBridgingInvalidTypeThrowsException()
    {
        $mock = Phockito::mock(HamcrestMe::class);

        Phockito::when($mock->Foo(HamcrestTypeBridge::argOfTypeThat('NotAClass', anInstanceOf('NotAClass'))))->return('PassMe');
    }

    /**
     * @expectedException \Reflection\ProxyException
     */
    function testCannotBridgeFinalType()
    {
        HamcrestTypeBridge::argOfTypeThat(FinalClass::class, anArray());
    }
}