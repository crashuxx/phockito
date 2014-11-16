<?php

namespace Phockito\internal\Clazz;


interface ParameterDefaultValue
{
    /**
     * @return mixed
     */
    public function getValue();

    /**
     * @return string
     */
    public function exportValue();
}