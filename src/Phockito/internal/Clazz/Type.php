<?php

namespace Phockito\internal\Clazz;


use Hamcrest\Matcher;

class Type
{
    /**
     * @var string
     */
    private $value;
    /**
     * @var Matcher
     */
    private $matcher;

    /**
     * @param string $value
     * @param Matcher $matcher
     */
    function __construct($value, Matcher $matcher)
    {
        $this->value = $value;
        $this->matcher = $matcher;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return Matcher
     */
    public function getMatcher()
    {
        return $this->matcher;
    }
}