<?php

namespace Phockito\Test;


use Exception;

class MockMePhp70
{
    function Foo(): string
    {
        throw new Exception('Base method Foo was called');
    }

    function Bar(): MockMe
    {
        throw new Exception('Base method Bar was called');
    }
}