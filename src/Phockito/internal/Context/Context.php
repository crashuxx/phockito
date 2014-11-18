<?php

namespace Phockito\internal\Context;


use Phockito\internal\Clazz\Clazz;

interface Context
{
    /**
     * @param string $name
     * @param array $args
     * @return ReturnValue
     */
    function call($name, array $args);
}