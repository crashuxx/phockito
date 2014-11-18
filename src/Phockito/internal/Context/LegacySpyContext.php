<?php

namespace Phockito\internal\Context;


use Hamcrest\Core\IsAnything;
use Phockito\internal\Clazz\Clazz;
use Phockito\internal\Clazz\Method;
use Phockito\internal\Clazz\Parameter;
use Phockito\internal\Clazz\Type;
use Phockito\Phockito;

class LegacySpyContext implements LegacyContext
{
    /**
     * @var Clazz
     */
    private $clazz;
    /**
     * @var object
     */
    private $object;
    /**
     * @var string
     */
    private $phockito_instanceid;

    /**
     * @param Clazz $clazz
     * @param object $object
     */
    function __construct(Clazz $clazz, $object)
    {
        $this->clazz = $clazz;
        $this->object = $object;

        $this->phockito_instanceid = $this->clazz->getName() . ':' . (++Phockito::$_instanceid_counter);
    }

    /**
     * @param string $name
     * @param array $args
     * @return ReturnValue
     */
    function call($name, array $args)
    {
        try {
            $method = $this->clazz->getMethod($name);
        } catch (\RuntimeException $e) {
            $parameters = [];
            foreach ($args as $k => $arg) {
                $parameters[] = new Parameter($k, new Type('mixed', new IsAnything()), null);
            }
            $method = new Method($name, $parameters, new Type('mixed', new IsAnything()), []);
        }

        $instance = $method->isStatic() ? ('::' . $this->clazz->getName()) : $this->phockito_instanceid;

        Phockito::__called($this->clazz->getName(), $instance, $name, $args);

        if (!method_exists($this->object, $name) && $name == '__toString') {
            $returnValue = new ReturnValue(false, '');
        } else {
            $result = call_user_func_array([$this->object, $name], $args);
            $returnValue = new ReturnValue(false, $result);
        }

        return $returnValue;
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
}