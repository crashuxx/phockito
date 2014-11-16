<?php

namespace Phockito\internal\Clazz;


use Hamcrest\Matcher;

class MethodParameter
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var null|string
     */
    private $type;
    /**
     * @var null|MethodParameterScalarValue
     */
    private $defaultValue;
    /**
     * @var bool
     */
    private $asReference;
    /**
     * @var Matcher
     */
    private $matcher;

    /**
     * @param string $name
     * @param string|null $type
     * @param MethodParameterScalarValue|null $defaultValue
     * @param Matcher $matcher
     * @param bool $asReference
     */
    function __construct($name, $type, MethodParameterScalarValue $defaultValue = null, Matcher $matcher, $asReference = false)
    {
        $this->name = $name;
        $this->type = $type;
        $this->defaultValue = $defaultValue;
        $this->asReference = $asReference;
        $this->matcher = $matcher;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return null|string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return null|MethodParameterScalarValue
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
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
        return $this->asReference;
    }
}