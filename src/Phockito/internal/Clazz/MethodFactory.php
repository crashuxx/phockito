<?php

namespace Phockito\internal\Clazz;


use Hamcrest\Core\IsAnything;
use Reflection;
use ReflectionMethod;

class MethodFactory
{
    /**
     * @var ParameterFactory
     */
    private $parameterFactory;

    /**
     * @param ParameterFactory $parameterFactory
     */
    function __construct(ParameterFactory $parameterFactory)
    {
        $this->parameterFactory = $parameterFactory;
    }

    /**
     * @param \ReflectionMethod $reflectionMethod
     * @return Method
     */
    public function createFromReflectionMethod(\ReflectionMethod $reflectionMethod)
    {
        $parameters = [];
        foreach ($reflectionMethod->getParameters() as $parameter) {
            $parameters[] = $this->parameterFactory->createFromReflectionParameter($parameter);
        }

        $modifiers = Reflection::getModifierNames($reflectionMethod->getModifiers());

        if (PHP_MAJOR_VERSION >= 7 && $reflectionMethod->hasReturnType()) {
            $type = $reflectionMethod->getReturnType();
            if ($type->isBuiltin()) {
                $returnType = (string)$type;
            } else {
                $returnType = '\\'.$type;
            }
        } else {
            $returnType = 'mixed';
        }

        return new Method($reflectionMethod->getName(), $parameters, new Type($returnType, new IsAnything(), $reflectionMethod->returnsReference()), $modifiers);
    }
}