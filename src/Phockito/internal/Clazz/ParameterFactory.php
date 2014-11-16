<?php

namespace Phockito\internal\Clazz;


use Hamcrest\Core\IsAnything;
use Hamcrest\Core\IsInstanceOf;
use Hamcrest\Type\IsArray;

class ParameterFactory
{
    /**
     * @param \ReflectionParameter $reflectionParameter
     * @return Parameter
     */
    public function createFromReflectionParameter(\ReflectionParameter $reflectionParameter)
    {
        if ($reflectionParameter->isArray()) {
            $type = new Type('array', new IsArray());
        } else if ($reflectionParameter->getClass() !== null) {
            $className = '\\' . $reflectionParameter->getClass()->getName();
            $type = new Type($className, new IsInstanceOf($className));
        } else {
            $type = new Type('mixed', new IsAnything());
        }

        $defaultValue = null;
        if ($reflectionParameter->isDefaultValueAvailable()) {
            if ($reflectionParameter->isDefaultValueConstant()) {
                throw new \RuntimeException("isDefaultValueConstant not implemented");
            } else {
                $defaultValue = new ParameterScalarValue($reflectionParameter->getDefaultValue());
            }
        }

        return new Parameter($reflectionParameter->getName(), $type, $defaultValue, $reflectionParameter->isPassedByReference());
    }
} 