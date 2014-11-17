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
     * @var Matcher
     */
    private $matcher;

    /**
     * @param string $name
     * @param Type $type
     * @param ParameterScalarValue|null $defaultValue
     */
    function __construct($name, Type $type, ParameterScalarValue $defaultValue = null)
    {
        $this->name = $name;
        $this->type = $type;
        $this->defaultValue = $defaultValue;
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
}