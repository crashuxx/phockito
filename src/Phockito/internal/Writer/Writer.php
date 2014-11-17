<?php

namespace Phockito\internal\Writer;


use Phockito\internal\Clazz\Method;

interface Writer
{
    /**
     * @param string $namespace
     */
    public function writeNamespace($namespace);

    /**
     * @param string $new
     * @param string $extend
     * @param string $markerInterface
     * @return
     */
    public function writeClassExtend($new, $extend, $markerInterface);

    /**
     * @param string $new
     * @param string $implement
     * @param string $markerInterface
     * @return
     */
    public function writeInterfaceExtend($new, $implement, $markerInterface);

    /**
     * @param Method $method
     */
    public function writeMethod(Method $method);

    /**
     */
    public function writeClose();

    /**
     * @return string
     */
    public function build();
}