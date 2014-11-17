<?php

namespace Phockito\internal\Clazz;


class Method
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var Parameter[]
     */
    private $parameters;
    /**
     * @var Type
     */
    private $returnType;
    /**
     * @var string[]
     */
    private $modifiers;

    /**
     * @param string $name
     * @param Parameter[] $parameters
     * @param Type $returnType
     * @param string[] $modifiers
     */
    function __construct($name, $parameters, Type $returnType, $modifiers)
    {
        $this->name = $name;
        $this->parameters = $parameters;
        $this->returnType = $returnType;
        $this->modifiers = $modifiers;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Parameter[]
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @return Type
     */
    public function getReturnType()
    {
        return $this->returnType;
    }

    /**
     * @return string[]
     */
    public function getModifiers()
    {
        return $this->modifiers;
    }
}