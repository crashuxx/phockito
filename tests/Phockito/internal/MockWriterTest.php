<?php

namespace Phockito\internal;


use Hamcrest\Core\IsAnything;
use Phockito\internal\Clazz\Method;
use Phockito\internal\Clazz\Parameter;
use Phockito\internal\Clazz\Type;

class MockWriterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MockWriter
     */
    private $writer;

    public function testWriteNamespace()
    {
        $this->writer->writeNamespace('\\Phockito\internal');

        $this->assertEquals('namespace Phockito\internal;', $this->writer->build());
    }

    public function testWriteClassExtend()
    {
        $this->writer->writeClassExtend('MockWriterTestExtended', 'MockWriterTest');

        $this->assertStringStartsWith('class MockWriterTestExtended extends MockWriterTest implements \Phockito\internal\Marker\MockMarker', $this->writer->build());
    }

    public function testWriteInterfaceExtend()
    {
        $this->writer->writeInterfaceExtend('MockWriterTestExtended', 'MockWriterTest');

        $this->assertStringStartsWith('class MockWriterTestExtended implements MockWriterTest, \Phockito\internal\Marker\MockMarker', $this->writer->build());
    }

    public function testFull()
    {
        $method = new Method('Foo',[new Parameter('a', new Type('array', new IsAnything()), null)], new Type('mixed', new IsAnything()));

        $this->writer->writeNamespace('\\Phockito\internal');
        $this->writer->writeClassExtend('MockWriterTestExtended', 'MockWriterTest');
        $this->writer->writeMethod($method);
        $this->writer->writeClose();

        eval($this->writer->build());
    }

    protected function setUp()
    {
        $this->writer = new MockWriter();
    }
}
