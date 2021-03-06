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
        $this->assertSame(['baz' => 'Foo\\Bar\\Baz'], $this->getSniff()->getImports()->toArray());
    }

    public function testUseWithRename()
    {
        $this->checkFile(__DIR__ . '/data/UseWithRename.inc', DummySniff::class);
        $this->assertSame(['testling' => 'Foo\\Bar\\Baz'], $this->getSniff()->getImports()->toArray());
    }

    public function testTraitUse()
    {
        $this->checkFile(__DIR__ . '/data/TraitUse.inc', DummySniff::class);
        $this->assertEmpty($this->getSniff()->getImports()->toArray(), 'Trait "use" parsed as "import"');
    }

    public function testIgnoreFunctionUse()
    {
        $file = $this->checkFile(__DIR__ . '/data/FunctionUse.inc', DummySniff::class);
        $this->assertNoSniffErrors($file);
    }

    public function testIgnoreFunctionUseWithoutSpace()
    {
        $file = $this->checkFile(__DIR__ . '/data/FunctionUseWithoutSpace.inc', DummySniff::class);
        $this->assertNoSniffErrors($file);
    }

    /**
     * @dataProvider invalidNamespaceStatementProvider
     * @expectedException \DoctrineAnnotationCodingStandard\Exception\ParseErrorException
     * @param string $content
     */
    public function testParseErrorsNamespace(string $content)
    {
        $this->checkString($content, DummySniff::class);
    }

    /**
     * @return string[][]
     */
    public function invalidNamespaceStatementProvider(): array
    {
        return [
            ['namespace;'],
            ['namespace();'],
            ['namespace <=>;'],
            ['namespace Foo<=>Bar;'],
            ['namespace ;'],
        ];
    }

    public function testNamespaceExtraction()
    {
        $this->checkString('namespace     Foo\\Bar     ;;', DummySniff::class);
        $this->assertSame('Foo\\Bar', $this->getSniff()->getNamespace());
    }

    /**
     * @dataProvider invalidUseStatementProvider
     * @expectedException \DoctrineAnnotationCodingStandard\Exception\ParseErrorException
     * @param string $content
     */
    public function testParseErrorsUse(string $content)
    {
        $this->checkString($content, DummySniff::class);
    }

    /**
     * @return string[][]
     */
    public function invalidUseStatementProvider(): array
    {
        return [
            ['use <=>;'],
            ['use Foo()Bar() As Bar;'],
            ['use Foo()As Bar;'],
            ['use Foo\Bar As()Bar;'],
            ['use Foo\Bar As <=>;'],
            ['use Foo\Bar As Bar <=>;'],
        ];
    }
}
