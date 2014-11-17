<?php

namespace Phockito\internal\Context;


class ReturnValue
{
    /**
     * @var bool
     */
    private $invokeParent;
    /**
     * @var mixed
     */
    private $value;

    /**
     * @param $invokeParent
     * @param mixed $value
     */
    function __construct($invokeParent, $value = null)
    {
        $this->invokeParent = $invokeParent;
        $this->value = $value;
    }

    /**
     * @return bool
     */
    public function invokeParent()
    {
        return $this->invokeParent;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        if ($this->invokeParent) {
            throw new \RuntimeException();
        }

        return $this->value;
    }
}