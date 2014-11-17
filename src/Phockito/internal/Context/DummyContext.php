<?php

namespace Phockito\internal\Context;


class DummyContext implements Context
{
    /**
     * @param string $name
     * @param array $args
     * @return mixed
     */
    function call($name, array $args)
    {
        throw new \RuntimeException("Cannot call method on dummy object!");
    }

}