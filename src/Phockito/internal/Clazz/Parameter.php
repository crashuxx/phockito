<?php

namespace Phockito\internal\Clazz;


use Hamcrest\Matcher;

class Parameter
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
     * @var null|ParameterScalarValue
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
     * @param ParameterScalarValue|null $defaultValue
     * @param bool $asReference
     */
    function __construct($name, Type $type, ParameterScalarValue $defaultValue = null, $asReference = false)
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
     * @return null|ParameterScalarValue
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