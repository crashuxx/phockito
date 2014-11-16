<?php

namespace Phockito\internal\Clazz;


interface MethodParameterDefaultValue 
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