<?php

namespace Phockito\internal\Context;


use Phockito\internal\Clazz\Clazz;

class DummyContext implements Context
{
    /**
     * @param string $name
     * @param array $args
     * @return mixed
     */
    function call($name, array $args)
    {
        throw new \RuntimeException("Cannot call method on dummy object!");
    }

    public function getClazz()
    {
        // TODO: Implement getClazz() method.
    }

    public function getPhockitoInstanceId()
    {
        // TODO: Implement getPhockitoInstanceId() method.
    }

}