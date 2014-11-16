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
     * @var Type
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
     * @param Type $type
     * @param MethodParameterScalarValue|null $defaultValue
     * @param bool $asReference
     */
    function __construct($name, Type $type, MethodParameterScalarValue $defaultValue = null, $asReference = false)
    {
        $this->name = $name;
        $this->type = $type;
        $this->defaultValue = $defaultValue;
        $this->asReference = $asReference;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Type
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
     * @return boolean
     */
    public function isReference()
    {
        return $this->asReference;
    }
}