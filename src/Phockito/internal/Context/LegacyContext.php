<?php

namespace Phockito\internal\Context;


use Phockito\internal\Clazz\Clazz;
use Phockito\Phockito;

class LegacyContext implements Context
{
    /**
     * @var Clazz
     */
    private $clazz;
    /**
     * @var string
     */
    private $phockito_instanceid;
    /**
     * @var bool
     */
    private $partial;

    /**
     * @param Clazz $clazz
     * @param bool $partial
     */
    public function __construct(Clazz $clazz, $partial = false)
    {
        $this->clazz = $clazz;
        $this->partial = $partial;

        $this->phockito_instanceid = $this->clazz->getName() . ':' . (++Phockito::$_instanceid_counter);
    }

    /**
     * @param string $name
     * @param array $args
     * @return ReturnValue
     */
    public function call($name, array $args)
    {
        $method = $this->clazz->getMethod($name);
        $instance = $method->isStatic() ? ('::' . $this->clazz->getName()) : $this->phockito_instanceid;

        $response = Phockito::__called($this->clazz->getName(), $instance, $name, $args);

        if ($response) {
            $returnValue = new ReturnValue(false, Phockito::__perform_response($response, $args));
        } else {
            $returnValue = new ReturnValue($this->invokeParentMethod($name), null);
        }

        return $returnValue;
    }

    /**
     * @param string $name
     * @return bool
     */
    private function invokeParentMethod($name)
    {
        return $this->partial && !$this->clazz->isInterface() && !$this->clazz->getMethod($name)->isAbstract();
    }

    /**
     * @return Clazz
     */
    public function getClazz()
    {
        return $this->clazz;
    }

    /**
     * @return string
     */
    public function getPhockitoInstanceid()
    {
        return $this->phockito_instanceid;
    }

    /**
     * @return boolean
     */
    public function isPartial()
    {
        return $this->partial;
    }
}