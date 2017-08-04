<?php declare(strict_types = 1);

namespace DoctrineAnnotationCodingStandardTests\Sniffs;

use PHPUnit\Framework\TestCase as BaseTestCase;
use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Files\LocalFile;
use PHP_CodeSniffer\Runner;

abstract class TestCase extends BaseTestCase
{
    /**
     * @var Runner
     */
    protected $codeSniffer;

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
    protected function checkFile(string $filePath, string $sniff): File
    {
        $this->codeSniffer->ruleset->sniffs = [$sniff => $sniff];
        $this->codeSniffer->ruleset->populateTokenListeners();

        $file = new LocalFile($filePath, $this->codeSniffer->ruleset, $this->codeSniffer->config);
        $file->process();

        return $file;
    }
}
