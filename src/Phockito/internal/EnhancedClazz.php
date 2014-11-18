<?php

namespace Phockito\internal;


use Phockito\internal\Clazz\Clazz;
use Phockito\internal\Context\Context;

class EnhancedClazz
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var Clazz
     */
    private $clazz;

    /**
     * @param string $name
     * @param Clazz $clazz
     */
    function __construct($name, Clazz $clazz)
    {
        $this->name = $name;
        $this->clazz = $clazz;
    }

    /**
     * @return Clazz
     */
    public function getClazz()
    {
        return $this->clazz;
    }

    /**
     * @param Context $context
     * @return mixed
     */
    public function newInstance(Context $context)
    {
        return new $this->name($context);
    }
}