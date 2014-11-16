<?php

namespace Phockito\internal;


interface Context 
{
    /**
     * @param string $name
     * @param array $args
     * @return mixed
     */
    function call($name, array $args);
}