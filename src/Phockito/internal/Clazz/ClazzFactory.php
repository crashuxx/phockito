<?php

namespace Phockito\internal\Clazz;


class ClazzFactory
{
    /**
     * @var MethodFactory
     */
    private $methodFactory;

    /**
     * @param MethodFactory $methodFactory
     */
    function __construct(MethodFactory $methodFactory)
    {
        $this->methodFactory = $methodFactory;
    }


    /**
     * @param \ReflectionClass $reflectionClass
     * @return Clazz
     */
    public function createFromReflectionClass(\ReflectionClass $reflectionClass)
    {
        $type = $reflectionClass->isInterface() ? Clazz::T_INTERFACE : Clazz::T_CLASS;

        $methods = [];
        foreach ($reflectionClass->getMethods() as $reflectionMethod) {
            $methods[] = $this->methodFactory->createFromReflectionMethod($reflectionMethod);
        }

        return new Clazz($reflectionClass->getName(), $type, $methods);
    }
}