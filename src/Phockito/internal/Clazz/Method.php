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
     * @param string $name
     * @param Parameter[] $parameters
     * @param Type $returnType
     */
    function __construct($name, $parameters, Type $returnType)
    {
        $this->name = $name;
        $this->parameters = $parameters;
        $this->returnType = $returnType;
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
}