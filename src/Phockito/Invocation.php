<?php

namespace Phockito;


interface Invocation
{
    /**
     * @return string
     */
    function getClassName();

    /**
     * @return string
     */
    function getMethodName();

    /**
     * @return \ArrayObject
     */
    function getParameters();

    /**
     * @return int microtime
     */
    function getTimeDiff();

    /**
     * @return array[]
     */
    function getBackTrace();
}