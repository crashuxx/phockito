<?php

namespace Phockito\Test;


class FooHasTypedArrayDefaultArgument
{
    /**
     * @param array $a
     * @return null
     */
    function Foo(array $a = [1, 2, 3])
    {
    }
}