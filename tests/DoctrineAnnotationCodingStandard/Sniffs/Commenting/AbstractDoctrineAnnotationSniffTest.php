<?php declare(strict_types = 1);

namespace DoctrineAnnotationCodingStandardTests\Sniffs\Commenting;

use PHPUnit\Framework\TestCase;
use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Files\LocalFile;
use PHP_CodeSniffer\Runner;

class AbstractDoctrineAnnotationSniffTest extends TestCase
{
    /**
     * @var Runner
     */
    private $codeSniffer;

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

    private function getSniff(): DummySniff
    {
        $sniff = reset($this->codeSniffer->ruleset->sniffs);

        if (!$sniff instanceof DummySniff) {
            throw new \LogicException();
        }

        return $sniff;
    }

    protected function setUp()
    {
        $this->codeSniffer = new Runner();
        $this->codeSniffer->config = new Config(['-s']);
        $this->codeSniffer->init();
    }

    /**
     * @param string $filePath
     * @param string $sniff
     * @return File
     */
    private function checkFile(string $filePath, string $sniff): File
    {
        $this->codeSniffer->ruleset->sniffs = [$sniff => $sniff];
        $this->codeSniffer->ruleset->populateTokenListeners();

        $file = new LocalFile($filePath, $this->codeSniffer->ruleset, $this->codeSniffer->config);
        $file->process();

        return $file;
    }
}
