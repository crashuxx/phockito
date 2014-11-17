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
     * @var bool
     */
    private $reference;

    /**
     * @param string $value
     * @param Matcher $matcher
     * @param bool $reference
     */
    function __construct($value, Matcher $matcher, $reference = false)
    {
        $this->value = $value;
        $this->matcher = $matcher;
        $this->reference = $reference;
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

    /**
     * @return boolean
     */
    public function isReference()
    {
        return $this->reference;
    }
}