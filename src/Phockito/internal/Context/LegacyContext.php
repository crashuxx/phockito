<?php

namespace Phockito\internal\Context;


use Phockito\internal\Clazz\Clazz;

interface LegacyContext extends Context
{
    /**
     * @return Clazz
     */
    public function getClazz();

    /**
     * @return string
     */
    public function getPhockitoInstanceId();
}