<?php

namespace Phockito\internal\InvocationHandler;


use Hamcrest\Core\IsAnything;
use Phockito\internal\Clazz\Clazz;
use Phockito\internal\Clazz\Method;
use Phockito\internal\Clazz\Parameter;
use Phockito\internal\Clazz\Type;
use Phockito\Phockito;
use Reflection\InvocationHandler;
use Reflection\InvocationHandler\DummyInvocationHandler;
use Reflection\Proxy;
use Reflection\ProxyClass;

class LegacyMockInvocationHandler implements InvocationHandler
{
    /**
     * @var Clazz
     */
    private $clazz;
    /**
     * @var ProxyClass
     */
    private $proxyClass;
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
     * @param ProxyClass $proxyClass
     * @param bool $partial
     */
    public function __construct(Clazz $clazz, ProxyClass $proxyClass, $partial = false)
    {
        $this->clazz = $clazz;
        $this->partial = $partial;

        $this->phockito_instanceid = $this->clazz->getName() . ':' . (++Phockito::$_instanceid_counter);
        $this->proxyClass = $proxyClass;
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

    /**
     * @param object $proxy
     * @param string $method
     * @param mixed[] $args
     * @return mixed
     */
    function invoke($proxy, $method, $args)
    {
        try {
            $methodObj = $this->clazz->getMethod($method);
        } catch (\Exception $e) {

            $parameters = [];
            foreach ($args as $k => $arg) {
                $parameters[] = new Parameter($k, new Type('mixed', new IsAnything()), null);
            }
            $methodObj = new Method($method, $parameters, new Type('mixed', new IsAnything()), []);
        }

        $instance = $methodObj->isStatic() ? ('::' . $this->clazz->getName()) : $this->phockito_instanceid;

        $response = Phockito::__called($this->clazz->getName(), $instance, $method, $args);

        if ($response) {
            $returnValue = Phockito::__perform_response($response, $args);
        } else {
            if ($method == '__toString') {
                $returnValue = '';
            } else {
                $parentClass = $this->proxyClass->getParentClass();

                if ($parentClass->hasMethod($method) && $this->invokeParentMethod($method)) {
                    $returnValue = $this->proxyClass->getParentClass()->getMethod($method)->invokeArgs($proxy, $args);
                } else {
                    // @fixme
                    $returnType = $methodObj->getReturnType();
                    if ($returnType->getValue() == 'mixed') {
                        $returnValue = null;
                    } else if ($returnType->getValue() == 'string') {
                        $returnValue = '';
                    } else {
                        $returnValue = Proxy::newProxyInstance($returnType->getValue(), new DummyInvocationHandler());
                    }
                }
            }
        }

        return $returnValue;
    }
}