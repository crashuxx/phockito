<?php

namespace Phockito;


interface WhenBuilder
{
    /**
     * @param mixed|null $value
     * @return WhenBuilder
     */
    function thenReturn($value);

    /**
     * @param \Exception|string $exception
     * @return WhenBuilder
     */
    function thenThrow($exception);

    /**
     * @param callable|\Closure $callback
     * @return WhenBuilder
     */
    function thenCallback($callback);

    /* *
     * @TODO
     * @return WhenBuilder
     */
    //function thenCallRealMethod();
}