<?php

namespace Phockito\internal\InvocationHandler;


use Hamcrest\Core\IsAnything;
use Phockito\internal\Clazz\Clazz;
use Phockito\internal\Clazz\Method;
use Phockito\internal\Clazz\Parameter;
use Phockito\internal\Clazz\Type;
use Phockito\Phockito;
use Reflection\InvocationHandler;
use Reflection\ProxyClass;

class LegacySpyInvocationHandler implements InvocationHandler
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
     * @var ProxyClass
     */
    private $proxyClass;

    /**
     * @param Clazz $clazz
     * @param ProxyClass $proxyClass
     * @param object $object
     */
    function __construct(Clazz $clazz, ProxyClass $proxyClass, $object)
    {
        $this->clazz = $clazz;
        $this->object = $object;

        $this->phockito_instanceid = $this->clazz->getName() . ':' . (++Phockito::$_instanceid_counter);
        $this->proxyClass = $proxyClass;
    }

    /**
     * @param object $proxy
     * @param string $method
     * @param mixed[] $args
     * @return mixed
     */
    public function invoke($proxy, $method, $args)
    {
        try {
            $methodObject = $this->clazz->getMethod($method);
        } catch (\RuntimeException $e) {
            $parameters = [];
            foreach ($args as $k => $arg) {
                $parameters[] = new Parameter($k, new Type('mixed', new IsAnything()), null);
            }
            $methodObject = new Method($method, $parameters, new Type('mixed', new IsAnything()), []);
        }

        $instance = $methodObject->isStatic() ? ('::' . $this->clazz->getName()) : $this->phockito_instanceid;

        Phockito::__called($this->clazz->getName(), $instance, $method, $args);

        if (!method_exists($this->object, $method) && $method == '__toString') {
            $returnValue = '';
        } else {
            $returnValue = call_user_func_array([$this->object, $method], $args);
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