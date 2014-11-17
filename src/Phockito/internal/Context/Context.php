<?php

namespace Phockito\internal\Context;


interface Context
{
    /**
     * @param string $name
     * @param array $args
     * @return mixed
     */
    function call($name, array $args);
}