<?php declare(strict_types = 1);

namespace DoctrineAnnotationCodingStandardTests\Sniffs\Commenting;

use DoctrineAnnotationCodingStandardTests\Sniffs\TestCase;

class AbstractDoctrineAnnotationSniffTest extends TestCase
{
    public function testNamespaceDetectionWithoutNamespace()
    {
        $this->checkFile(__DIR__ . '/data/FileNoNamespace.inc', DummySniff::class);
        $this->assertSame(null, $this->getSniff()->getNamespace());
    }

    public function testNamespaceDetection()
    {
        $this->checkFile(__DIR__ . '/data/FileWithNamespace.inc', DummySniff::class);
        $this->assertSame('Foo\\Bar\\Baz', $this->getSniff()->getNamespace());
    }

    public function testSimpleUse()
    {
        $this->checkFile(__DIR__ . '/data/SimpleUse.inc', DummySniff::class);
        $this->assertEquals(['baz' => 'Foo\\Bar\\Baz'], $this->getSniff()->getImports());
    }

    public function testUseWithRename()
    {
        $this->checkFile(__DIR__ . '/data/UseWithRename.inc', DummySniff::class);
        $this->assertEquals(['testling' => 'Foo\\Bar\\Baz'], $this->getSniff()->getImports());
    }

    private function getSniff(): DummySniff
    {
        $sniff = reset($this->codeSniffer->ruleset->sniffs);

        if (!$sniff instanceof DummySniff) {
            throw new \LogicException();
        }

        return $sniff;
    }
}
