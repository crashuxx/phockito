<?php

namespace Phockito\internal;


use Phockito\internal\Clazz\Method;

class MockWriter
{
    private $code = [];

    public function writeNamespace($namespace)
    {
        $this->code[] = 'namespace ' . trim($namespace, " \t\n\r\0\x0B\\") . ';';
    }

    public function writeClassExtend($new, $extend)
    {
        $this->code[] = 'class ' . $new . ' extends ' . $extend . ' implements \Phockito\internal\Marker\MockMarker {';
        $this->writeConstructor();
    }

    public function writeInterfaceExtend($new, $implement)
    {
        $this->code[] = 'class ' . $new . ' implements ' . $implement . ', \Phockito\internal\Marker\MockMarker {';
        $this->writeConstructor();
    }

    private function writeConstructor()
    {
        $this->code[] = 'public $__phockito_context;';
        $this->code[] = 'public function __construct(\Phockito\internal\Context $context) {';
        $this->code[] = '    $this->__phockito_context = $context;';
        $this->code[] = '}';
    }

    public function writeMethod(Method $method)
    {
        $args = [];
        foreach ($method->getParameters() as $parameter) {
            $arg = '';

            if ($parameter->getType()->getValue() != 'mixed') {
                $arg .= $parameter->getType()->getValue() . ' ';
            }

            $arg .= '$' . $parameter->getName();

            if ($parameter->getDefaultValue() != null) {
                $arg .= ' = ' . $parameter->getDefaultValue()->exportValue();
            }

            $args[] = $arg;
        }

        $this->code[] = 'function ' . $method->getName() . '(' . implode(', ', $args) . ') {';
        $this->code[] = '    return $this->context->call(__FUNCTION__, func_get_args());';
        $this->code[] = '}';
    }

    public function writeClose()
    {
        $this->code[] = '}';
    }

    /**
     * @return string
     */
    public function build()
    {
        return implode("\n", $this->code);
    }
}