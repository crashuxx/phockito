<?php

namespace Phockito\internal\Clazz;


use Hamcrest\Matcher;

class Type
{
    /**
     * @var string
     */
    private $type;
    /**
     * @var Matcher
     */
    private $matcher;

    /**
     * @param string $type
     * @param Matcher $matcher
     */
    function __construct($type, Matcher $matcher)
    {
        $this->type = $type;
        $this->matcher = $matcher;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return Matcher
     */
    public function getMatcher()
    {
        return $this->matcher;
    }
}