<?php declare(strict_types = 1);
/**
 * This is heavily based on base TestCase class from Slevomat Coding Standard,
 * licensed under MIT license as well.
 *
 * Source: https://github.com/slevomat/coding-standard/blob/master/tests/Sniffs/TestCase.php
 */

namespace DoctrineAnnotationCodingStandardTests\Sniffs;

use DoctrineAnnotationCodingStandardTests\Sniffs\Commenting\DummySniff;
use PHPUnit\Framework\TestCase as BaseTestCase;
use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Files\DummyFile;
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

    /**
     * @param string $content
     * @param string $sniff
     * @return File
     */
    protected function checkString(string $content, string $sniff): File
    {
        $this->codeSniffer->ruleset->sniffs = [$sniff => $sniff];
        $this->codeSniffer->ruleset->populateTokenListeners();

        $file = new DummyFile('<?php ' . $content, $this->codeSniffer->ruleset, $this->codeSniffer->config);
        $file->process();

        return $file;
    }

    protected function assertFixedEqualsFile(string $expectedFile, File $file) {
        $file->fixer->fixFile();
        $this->assertStringEqualsFile($expectedFile, $file->fixer->getContents());
    }

    protected function assertNoSniffErrorInFile(File $file)
    {
        $errors = $file->getErrors();
        $this->assertEmpty($errors, sprintf('No errors expected, but %d errors found.', count($errors)));
    }

    protected function assertSniffError(File $codeSnifferFile, int $line, string $code, string $message = null)
    {
        $errors = $codeSnifferFile->getErrors();
        $this->assertTrue(isset($errors[$line]), sprintf('Expected error on line %s, but none found.', $line));

        $sniffCode = sprintf('%s.%s', $this->getSniffName(), $code);

        $this->assertTrue(
            $this->hasError($errors[$line], $sniffCode, $message),
            sprintf(
                'Expected error %s%s, but none found on line %d.%sErrors found on line %d:%s%s%s',
                $sniffCode,
                $message !== null ? sprintf(' with message "%s"', $message) : '',
                $line,
                PHP_EOL . PHP_EOL,
                $line,
                PHP_EOL,
                $this->getFormattedErrors($errors[$line]),
                PHP_EOL
            )
        );
    }

    protected function assertNoSniffErrors(File $codeSnifferFile)
    {
        $errors = $codeSnifferFile->getErrors();
        $this->assertEmpty($errors, 'Expected no errors');
    }

    protected function assertNoSniffErrorOnLine(File $codeSnifferFile, int $line)
    {
        $errors = $codeSnifferFile->getErrors();
        $this->assertFalse(
            isset($errors[$line]),
            sprintf(
                'Expected no error on line %s, but found:%s%s%s',
                $line,
                PHP_EOL . PHP_EOL,
                isset($errors[$line]) ? $this->getFormattedErrors($errors[$line]) : '',
                PHP_EOL
            )
        );
    }

    /**
     * @param mixed[][][] $errorsOnLine
     * @param string $sniffCode
     * @param string|null $message
     * @return bool
     */
    private function hasError(array $errorsOnLine, string $sniffCode, string $message = null): bool
    {
        foreach ($errorsOnLine as $errorsOnPosition) {
            foreach ($errorsOnPosition as $error) {
                if (
                    $error['source'] === $sniffCode
                    && ($message === null || strpos($error['message'], $message) !== false)
                ) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param mixed[][][] $errors
     * @return string
     */
    private function getFormattedErrors(array $errors): string
    {
        return implode(PHP_EOL, array_map(function (array $errors): string {
            return implode(PHP_EOL, array_map(function (array $error): string {
                return sprintf("\t%s: %s", $error['source'], $error['message']);
            }, $errors));
        }, $errors));
    }

    protected function getSniffName(): string
    {
        return preg_replace(
            [
                '~\\\~',
                '~\.Sniffs~',
                '~Sniff$~',
            ],
            [
                '.',
                '',
                '',
            ],
            $this->getSniffClassName()
        );
    }

    protected function getSniffClassName(): string
    {
        return preg_replace('/Tests?/', '', get_class($this));
    }

    protected function getSniff(): DummySniff
    {
        $sniff = reset($this->codeSniffer->ruleset->sniffs);

        if (!$sniff instanceof DummySniff) {
            throw new \LogicException();
        }

        return $sniff;
    }
}
