<?php

namespace Phockito\internal\Clazz;


class Clazz
{
    const T_CLASS = 'class';
    const T_INTERFACE = 'interface';
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $type;
    /**
     * @var Method[]
     */
    private $methods;

    /**
     * @param string $name
     * @param string $type
     * @param Method[] $methods
     */
    function __construct($name, $type, $methods)
    {
        $this->name = $name;
        $this->type = $type;
        $this->methods = $methods;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return Method[]
     */
    public function getMethods()
    {
        return $this->methods;
    }
}