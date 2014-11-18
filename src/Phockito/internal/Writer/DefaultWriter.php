<?php

namespace Phockito\internal\Writer;


use Phockito\internal\Clazz\Method;

class DefaultWriter implements Writer
{
    private $code = [];

    public function writeNamespace($namespace)
    {
        $this->code[] = 'namespace ' . trim($namespace, " \t\n\r\0\x0B\\") . ';';
    }

    public function writeClassExtend($new, $extend, $markerInterface)
    {
        $this->code[] = 'class ' . $new . ' extends ' . $extend . ' implements \\' . $markerInterface . ' {';
        $this->writeConstructor();
    }

    public function writeInterfaceExtend($new, $implement, $markerInterface)
    {
        $this->code[] = 'class ' . $new . ' implements ' . $implement . ', \\' . $markerInterface . ' {';
        $this->writeConstructor();
    }

    private function writeConstructor()
    {
        $this->code[] = 'public $__phockito_context;';
        $this->code[] = 'public function __construct(\Phockito\internal\Context\Context $context) {';
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

            if ($parameter->getType()->isReference()) {
                $arg .= '&';
            }

            $arg .= '$' . $parameter->getName();

            if ($parameter->getDefaultValue() != null) {
                $arg .= ' = ' . $parameter->getDefaultValue()->exportValue();
            }

            $args[] = $arg;
        }

        $modifiers = array_diff($method->getModifiers(), ['abstract']);

        $methodName = ($method->getReturnType()->isReference() ? '&' : '') . $method->getName();
        $this->code[] = implode(' ', $modifiers) . ' function ' . $methodName . '(' . implode(', ', $args) . ') {';
        $this->code[] = '    $result = $this->__phockito_context->call(__FUNCTION__, func_get_args());';
        $this->code[] = '    $return = $result->invokeParent() ? parent::' . $method->getName() . '() : $result->getValue();';
        $this->code[] = '    return $return;';
        $this->code[] = '}';
    }

    public function writeToStringMethod()
    {
        $this->code[] = '   public function __toString() {';
        $this->code[] = '    $result = $this->__phockito_context->call("__toString", func_get_args());';
        $this->code[] = '    $return = $result->invokeParent() ? parent::__toString() : $result->getValue();';
        $this->code[] = '    return $return;';
        $this->code[] = '}';
    }

    public function writeCallMethod()
    {
        $this->code[] = '   public function __call($name, $args) {';
        $this->code[] = '    $result = $this->__phockito_context->call($name, $args);';
        $this->code[] = '    $return = $result->invokeParent() ? parent::__call($name, $args) : $result->getValue();';
        $this->code[] = '    return $return;';
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