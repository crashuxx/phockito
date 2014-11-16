<?php

namespace Phockito\internal\Clazz;


use Hamcrest\Core\IsAnything;

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

        return new Method($reflectionMethod->getName(), $parameters, new Type('mixed', new IsAnything()));
    }
}