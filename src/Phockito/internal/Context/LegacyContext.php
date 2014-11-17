<?php

namespace Phockito\internal\Context;


use Hamcrest\Core\IsAnything;
use Phockito\internal\Clazz\Clazz;
use Phockito\internal\Clazz\Method;
use Phockito\internal\Clazz\Parameter;
use Phockito\internal\Clazz\Type;
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
        try {
            $method = $this->clazz->getMethod($name);
        } catch (\Exception $e) {

            $parameters = [];
            foreach ($args as $k => $arg) {
                $parameters[] = new Parameter($k, new Type('mixed', new IsAnything()), null);
            }
            $method = new Method($name, $parameters, new Type('mixed', new IsAnything()), []);
        }

        $instance = $method->isStatic() ? ('::' . $this->clazz->getName()) : $this->phockito_instanceid;

        $response = Phockito::__called($this->clazz->getName(), $instance, $name, $args);

        if ($response) {
            $returnValue = new ReturnValue(false, Phockito::__perform_response($response, $args));
        } else {
            if ($name == '__toString') {
                $returnValue = new ReturnValue($this->invokeParentMethod($name), '');
            } else {
                $returnValue = new ReturnValue($this->invokeParentMethod($name), null);
            }
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
    public function getPhockitoInstanceId()
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