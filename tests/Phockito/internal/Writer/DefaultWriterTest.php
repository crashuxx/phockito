<?php

namespace Phockito\internal\Writer;


use Hamcrest\Core\IsAnything;
use Phockito\internal\Clazz\Method;
use Phockito\internal\Clazz\Parameter;
use Phockito\internal\Clazz\Type;
use Phockito\internal\Marker\MockMarker;

class DefaultWriterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DefaultWriter
     */
    private $writer;

    public function testWriteNamespace()
    {
        $this->writer->writeNamespace(__NAMESPACE__);

        $this->assertEquals('namespace Phockito\internal\Writer;', $this->writer->build());
    }

    public function testWriteClassExtend()
    {
        $this->writer->writeClassExtend('MockWriterTestExtended', 'MockWriterTest', MockMarker::class);

        $this->assertStringStartsWith('class MockWriterTestExtended extends MockWriterTest implements \Phockito\internal\Marker\MockMarker', $this->writer->build());
    }

    public function testWriteInterfaceExtend()
    {
        $this->writer->writeInterfaceExtend('MockWriterTestExtended', 'MockWriterTest', MockMarker::class);

        $this->assertStringStartsWith('class MockWriterTestExtended implements MockWriterTest, \Phockito\internal\Marker\MockMarker', $this->writer->build());
    }

    public function testFull()
    {
        $method = new Method('Foo', [new Parameter('a', new Type('array', new IsAnything()), null)], new Type('mixed', new IsAnything()), ['public', 'abstract']);

        $this->writer->writeNamespace(__NAMESPACE__);
        $this->writer->writeClassExtend('MockWriterTestExtendedFull', 'DefaultWriterTest', MockMarker::class);
        $this->writer->writeMethod($method);
        $this->writer->writeClose();

        $this->assertRegExp('/public/', $this->writer->build());
        $this->assertNotRegExp('/abstract/', $this->writer->build());
        $this->assertNotRegExp('/&Foo/', $this->writer->build());
        eval($this->writer->build());
    }

    public function testAbstractReturnType()
    {
        $method = new Method('Foo', [new Parameter('a', new Type('array', new IsAnything()), null)], new Type('mixed', new IsAnything(), true), ['public', 'abstract']);

        $this->writer->writeNamespace(__NAMESPACE__);
        $this->writer->writeClassExtend('MockWriterTestExtendedAbstractReturnType', 'DefaultWriterTest', MockMarker::class);
        $this->writer->writeMethod($method);
        $this->writer->writeClose();

        $this->assertRegExp('/&Foo/', $this->writer->build());
        eval($this->writer->build());
    }

    public function testParameterAsReference()
    {
        $method = new Method('Foo', [new Parameter('a', new Type('array', new IsAnything(), true), null)], new Type('mixed', new IsAnything()), []);

        $this->writer->writeNamespace(__NAMESPACE__);
        $this->writer->writeClassExtend('MockWriterTestExtendedParameterAsReference', 'DefaultWriterTest', MockMarker::class);
        $this->writer->writeMethod($method);
        $this->writer->writeClose();

        $this->assertRegExp('/&\\$a/', $this->writer->build());
        eval($this->writer->build());
    }

    protected function setUp()
    {
        $this->writer = new DefaultWriter();
    }
}
