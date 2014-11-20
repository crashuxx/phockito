<?php

namespace Phockito\Test;


use Exception;

class SpyMe {
    public $constructor_arg = false;
    function __construct($arg = true) { $this->constructor_arg = $arg; }

    /**
     * @throws Exception
     * @return null
     */
    function Foo() { throw new Exception('Base method Foo was called'); }
    function Bar() { return $this->Foo(); }
    function Baz($response) { return $response; }
}