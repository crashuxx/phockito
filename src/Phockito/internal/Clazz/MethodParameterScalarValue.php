<?php

namespace Phockito\internal\Clazz;


class MethodParameterScalarValue implements MethodParameterDefaultValue
{
    private $value;

    /**
     * @param mixed $value
     */
    function __construct($value)
    {
        if (!is_null($value) && !is_scalar($value)) {
            if (is_array($value)) {
                if (!empty($value)) {
                    throw new \InvalidArgumentException('Parameter default value of type array must be empty!');
                }
            } else {
                throw new \InvalidArgumentException('Unsupported parameter type "'.gettype($value).'"!');
            }
        }

        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function exportValue()
    {
        return var_export($this->value, true);
    }
}